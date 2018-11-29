<?php

namespace Drupal\wiki_challenge\RenderMode;

/**
 * The implementation of the Render Mode 'Render as snippets'.
 */
class RenderAsSnippetsRenderMode implements RenderModeInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'Render as snippets';
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return 'render_mode_render_as_snippets';
  }

  /**
   * {@inheritdoc}
   */
  public function doRender(array $entities = []) {
    if (empty($entities)) {
      // Nothing to do, Stop here.
      return [];
    }
    $results = [];
    foreach ($entities as $entity) {
      $results[] = [
        '#theme' => 'wiki_challenge_search_result_item',
        '#entity' => $entity,
      ];
    }
    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function getHelp() {
    return [];
  }

}
