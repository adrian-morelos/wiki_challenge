<?php

namespace Drupal\wiki_challenge\QueryMode;

/**
 * The Query Mode manager interface.
 */
interface QueryModeManagerInterface {

  /**
   * Adds a Query Mode.
   *
   * @param \Drupal\wiki_challenge\QueryMode\QueryModeInterface $query_mode
   *   The Query Mode.
   */
  public function addQueryMode(QueryModeInterface $query_mode);

  /**
   * Get selected QueryMode relevant for the entity.
   *
   * @return \Drupal\wiki_challenge\QueryMode\QueryModeInterface
   *   The appropriate Query Mode for the customer entity.
   */
  public function getQueryMode();

  /**
   * Get a query mode relevant for the entity.
   *
   * @param string $query_mode_id
   *   The query mode Id.
   *
   * @return \Drupal\wiki_challenge\QueryMode\QueryModeInterface
   *   The appropriate Query Mode for the given checker Id.
   */
  public function getQueryModeById($query_mode_id = NULL);

  /**
   * Returns an array of all registered Query Modes.
   *
   * @return \Drupal\wiki_challenge\QueryMode\QueryModeInterface[]
   *   All registered Query Modes keyed by query mode ID.
   */
  public function listQueryModes();

  /**
   * Returns an array of the IDs of all registered Query Modes.
   *
   * @return array
   *   Array of the IDs of all registered Query Modes.
   *   Format is: ['query mode key' => 'query mode name']
   */
  public function listQueryModeIds();

}
