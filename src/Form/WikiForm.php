<?php

namespace Drupal\wiki_challenge\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\wiki_challenge\QueryMode\QueryModeManagerInterface;
use Drupal\wiki_challenge\RenderMode\RenderModeManagerInterface;

/**
 * Builds the search form for the Wiki page.
 */
class WikiForm extends FormBase {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The Query Mode manager service.
   *
   * @var \Drupal\wiki_challenge\QueryMode\QueryModeManagerInterface
   */
  protected $queryModes;

  /**
   * The Render Mode manager service.
   *
   * @var \Drupal\wiki_challenge\RenderMode\RenderModeManagerInterface
   */
  protected $renderModes;

  /**
   * Constructs a new SearchBlockForm.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\wiki_challenge\QueryMode\QueryModeManagerInterface $query_modes
   *   The Query Mode manager service.
   * @param \Drupal\wiki_challenge\RenderMode\RenderModeManagerInterface $render_modes
   *   The Query Mode manager service.
   */
  public function __construct(LanguageManagerInterface $language_manager, RendererInterface $renderer, QueryModeManagerInterface $query_modes, RenderModeManagerInterface $render_modes) {
    $this->languageManager = $language_manager;
    $this->renderer = $renderer;
    $this->queryModes = $query_modes;
    $this->renderModes = $render_modes;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('language_manager'),
      $container->get('renderer'),
      $container->get('wiki_challenge.query_mode_manager'),
      $container->get('wiki_challenge.render_mode_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wiki_challenge_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $parameter = NULL) {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
    $search_term = '';
    if (isset($parameter)) {
      $search_term = $parameter;
    }
    elseif ($search_value = $this->getRequest()->get('keys')) {
      $search_term = $search_value;
    }
    $keys_title = $this->t(
      'Enter the terms you wish to search for.',
      [],
      ['langcode' => $langcode]
    );
    $this->attachHelp($form);
    $form['search'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Wiki Search'),
      '#attributes' => [
        'class' => ['form--inline'],
      ],
    ];
    // Field: keys.
    $form['search']['keys'] = [
      '#type' => 'search',
      '#title' => $this->t('Search', [], ['langcode' => $langcode]),
      '#description' => $this->t('Enter your keywords.', [], ['langcode' => $langcode]),
      '#size' => 30,
      '#default_value' => $search_term,
      '#attributes' => [
        'title' => $keys_title,
        'placeholder' => 'Type here to search'
      ],
    ];
    // Field: Result Render Mode.
    $options = $this->renderModes->listRenderModeIds();
    $default_render_mode = $this->config('wiki_challenge.render_modes_manager')->get('default_render_mode_id');
    $request_render_mode = $this->getRequest()->get('render_mode');
    if (!empty($request_render_mode) && isset($options[$request_render_mode])) {
      $default_render_mode = $request_render_mode;
    }
    $form['search']['render_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Result Render Mode', [], ['langcode' => $langcode]),
      '#description' => $this->t('Select the render mode used to <br>display the result items.', [], ['langcode' => $langcode]),
      '#default_value' => $default_render_mode,
      '#options' => $options,
      '#required' => TRUE,
    ];
    // Field: Query Mode.
    $options = $this->queryModes->listQueryModeIds();
    $default_query_mode = $this->config('wiki_challenge.query_modes_manager')->get('default_query_mode_id');
    $request_query_mode = $this->getRequest()->get('query_mode');
    if (!empty($request_query_mode) && isset($options[$request_query_mode])) {
      $default_query_mode = $request_query_mode;
    }
    $form['search']['query_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Query Mode', [], ['langcode' => $langcode]),
      '#description' => $this->t('Select the query mode used to perform the search query.', [], ['langcode' => $langcode]),
      '#default_value' => $default_query_mode,
      '#options' => $options,
      '#required' => TRUE,
    ];
    // Form Actions.
    $form['search']['actions'] = [
      '#type' => 'actions',
    ];
    $form['search']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];
    // Result Items.
    $form['result_items'] = [
      '#type' => 'container',
      '#title' => $this->t('Wiki Search result.'),
      '#attributes' => [
        'class' => ['search-result-items'],
      ],
    ];
    // Attach the Result Items to the form render array.
    $this->attachSearchResultItems($form, $search_term, $default_query_mode, $default_render_mode);
    // Attach stylesheet library.
    $form['#attached']['library'][] = 'wiki_challenge/wiki_search';
    // Dependency on search api config entity.
    $this->renderer->addCacheableDependency($form, $langcode);
    return $form;
  }

  /**
   * Attach Help elements.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   */
  public function attachHelp(array &$form = []) {
    $form['help'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Wiki Search Help'),
      '#attributes' => [
        'class' => ['form--inline'],
      ],
    ];
    $form['help']['summary'] = [
      '#markup' => $this->t("<p>Welcome to the Wiki Article search page!. Once the <strong>'Wiki Challenge'</strong> module is installed, a type of content called <strong>'Wikipedia Article'</strong> is automatically added and configured along with a view mode called <strong>'Wikipedia Article Search Result'</strong>.</p><p>After that <strong>wiki challenge's</strong> module is installed 30 nodes of type <strong>'Wikipedia Article'</strong> are automatically generated using the service <strong>'wiki_challenge.generate.content'</strong> implemented on this module(This logic can be found on the implementation of the hook: <strong>'hook_modules_installed'</strong> of the Wiki Challenge's module)</p><p>On this page, you can search nodes of type <strong>'Wikipedia Articles'</strong> that contains a given parameter in its title, either searching by terms using the search box below or by providing a search term on the URL by following the route pattern: <strong>/wiki/[search_term]</strong>.</p>"),
    ];
  }

  /**
   * Form Search Result Items.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param string $search_term
   *   The search term.
   * @param string $query_mode_id
   *   The query mode ID.
   * @param string $render_mode_id
   *   The render mode ID.
   */
  public function attachSearchResultItems(array &$form = [], $search_term = '', $query_mode_id = NULL, $render_mode_id = NULL) {
    if (empty($query_mode_id) || empty($render_mode_id)) {
      return NULL;
    }
    $query_mode = $this->queryModes->getQueryModeById($query_mode_id);
    if (!$query_mode) {
      return NULL;
    }
    $render_mode = $this->renderModes->getRenderModeById($render_mode_id);
    if (!$render_mode) {
      return NULL;
    }
    // Perform the Search Query.
    $entities = $query_mode->doSearch($search_term);
    // Render Wiki Articles nodes.
    $items = $render_mode->doRender($entities);
    // Add Header.
    if (!empty($search_term)) {
      $form['search_result_header'] = [
        '#type' => 'item',
        '#markup' => $this->t("<div class='clearfix'><h2>Search term(s): <strong>@search_term</strong>.</h2></div>", ['@search_term' => $search_term]),
        '#weight' => 10,
      ];
    }
    // Attach the Result Items to the form render array.
    $form['result_items'] = [
      '#type' => 'container',
      '#title' => $this->t('Wiki Search result.'),
      '#attributes' => [
        'class' => ['search-result-items'],
      ],
      '#weight' => 20,
    ];
    if (!empty($items)) {
      $form['result_items']['items'] = $items;
    }
    else {
      // No result was found for the search term, show a friendly message.
      $form['result_items_empty'] = [
        '#markup' => $this->t("<div><h3>Your search yielded no results.</h3></div>"),
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // This form submits to the wiki search page, so processing happens there.
    $keys = $form_state->getValue('keys');
    $render_mode = $form_state->getValue('render_mode');
    $query_mode = $form_state->getValue('query_mode');
    $query = [
      'render_mode' => $render_mode,
      'query_mode' => $query_mode,
    ];
    $uri = !empty($keys) ? "internal:/wiki/{$keys}" : 'internal:/wiki/';
    $url = Url::fromUri($uri, ['query' => $query]);
    $form_state->setRedirectUrl($url);
  }

}
