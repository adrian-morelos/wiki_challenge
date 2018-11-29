<?php

namespace Drupal\wiki_challenge\RenderMode;

/**
 * Defines a common interface for Render Modes.
 */
interface RenderModeInterface {

  /**
   * Gets the name of the render mode.
   */
  public function getName();

  /**
   * Gets the id of the render mode.
   */
  public function getId();

  /**
   * Render a set of a nodes.
   *
   * @param array $entities
   *   The search word.
   *
   * @return array
   *   The render array.
   */
  public function doRender(array $entities = []);

  /**
   * Description of the Render Mode code logic.
   *
   * Provide online user help for know what is the logic associated
   * with each Render Mode.
   *
   * @return array
   *   The render array.
   */
  public function getHelp();

}
