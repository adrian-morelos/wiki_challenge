<?php

namespace Drupal\wiki_challenge\QueryMode;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * The Query Mode manager.
 *
 * Responsible for handling Query Modes.
 *
 * @package Drupal\wiki_challenge\QueryMode
 */
class QueryModeManager implements QueryModeManagerInterface {

  /**
   * The list of Query Modes.
   *
   * @var \Drupal\wiki_challenge\QueryMode\QueryModeInterface[]
   */
  protected $queryModes = [];

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a QueryModeManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function addQueryMode(QueryModeInterface $query_modes) {
    $this->queryModes[$query_modes->getId()] = $query_modes;
  }

  /**
   * {@inheritdoc}
   */
  public function getQueryMode() {
    $config = $this->configFactory->get('wiki_challenge.query_modes_manager');
    $query_mode_id = $config->get('default_query_mode_id');
    return $this->queryModes[$query_mode_id];
  }

  /**
   * {@inheritdoc}
   */
  public function getQueryModeById($query_mode_id = NULL) {
    $query_mode = FALSE;
    if (!is_null($query_mode_id)) {
      $query_mode = $this->queryModes[$query_mode_id];
    }
    return $query_mode;
  }

  /**
   * {@inheritdoc}
   */
  public function listQueryModes() {
    return $this->queryModes;
  }

  /**
   * {@inheritdoc}
   */
  public function listQueryModeIds() {
    $ids = [];
    foreach ($this->queryModes as $query_mode) {
      $ids[$query_mode->getId()] = $query_mode->getName();
    }
    return $ids;
  }

}
