<?php

namespace Drupal\wiki_challenge\RenderMode;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * The implementation of the Render Mode 'Render as view modes'.
 */
class RenderAsViewModesRenderMode implements RenderModeInterface {

  /**
   * Include the entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The RenderAsViewModesRenderMode construct.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'Render as view modes';
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return 'render_mode_render_as_view_modes';
  }

  /**
   * {@inheritdoc}
   */
  public function doRender(array $entities = []) {
    if (empty($entities)) {
      return [];
    }
    $results = [];
    /* @var $entity \Drupal\Core\Entity\EntityInterface */
    foreach ($entities as $entity) {
      $bundle = $entity->bundle();
      $view_mode = 'node.' . $bundle . '_search_result';
      $results[] = $this->entityTypeManager->getViewBuilder($entity->getEntityTypeId())->view($entity, $view_mode);;
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
