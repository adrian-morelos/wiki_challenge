<?php

namespace Drupal\wiki_challenge\QueryMode;

/**
 * Defines a common interface for Query Modes.
 */
interface QueryModeInterface {

  /**
   * Gets the name of the query mode.
   */
  public function getName();

  /**
   * Gets the id of the query mode.
   */
  public function getId();

  /**
   * Perform a node search given a keywords.
   *
   * @param string $keys
   *   The search word.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   The Result Items.
   */
  public function doSearch($keys = NULL);

  /**
   * Description of the Query Mode code logic.
   *
   * Provide online user help for know what is the logic associated
   * with each Query Mode.
   *
   * @return array
   *   The render array.
   */
  public function getHelp();

}
