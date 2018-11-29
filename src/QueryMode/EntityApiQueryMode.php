<?php

namespace Drupal\wiki_challenge\QueryMode;

use Drupal\node\NodeInterface;

/**
 * The Entity API Query Mode implementation.
 */
class EntityApiQueryMode implements QueryModeInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'Entity API - Entity Query';
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return 'query_mode_entity_api';
  }

  /**
   * {@inheritdoc}
   */
  public function doSearch($keys = NULL) {
    $query = \Drupal::entityQuery('node')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('type', WIKI_CHALLENGE_WIKIPEDIA_ARTICLE_BUNDLE);
    // By Default return all the wiki articles if the search keys are empty.
    if (!empty($keys)) {
      $query->condition('title', $keys, 'CONTAINS');
    }
    $nids = $query->execute();
    if (empty($nids)) {
      return [];
    }
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    // Load multiple nodes.
    return $node_storage->loadMultiple($nids);
  }

  /**
   * {@inheritdoc}
   */
  public function getHelp() {
    return [];
  }

}
