<?php

namespace Drupal\wiki_challenge\QueryMode;

/**
 * The Database API Query Mode implementation.
 */
class DatabaseApiQueryMode implements QueryModeInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'Database API - Static Query';
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return 'query_mode_database_api';
  }

  /**
   * {@inheritdoc}
   */
  public function doSearch($keys = NULL) {
    $connection = \Drupal::database();
    $query = $connection->select('node_field_data', 'a')
      ->fields('a', ['nid'])
      ->condition('title', '%' . $connection->escapeLike($keys) . '%', 'LIKE')
      ->condition('type', WIKI_CHALLENGE_WIKIPEDIA_ARTICLE_BUNDLE);
    $nids = $query->execute()->fetchAllKeyed(0, 0);
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
