<?php

namespace Drupal\wiki_challenge\RenderMode;

/**
 * The Render Mode manager interface.
 */
interface RenderModeManagerInterface {

  /**
   * Adds a Render Mode.
   *
   * @param \Drupal\wiki_challenge\RenderMode\RenderModeInterface $render_mode
   *   The Render Mode.
   */
  public function addRenderMode(RenderModeInterface $render_mode);

  /**
   * Get selected RenderMode relevant for the entity.
   *
   * @return \Drupal\wiki_challenge\RenderMode\RenderModeInterface
   *   The appropriate Render Mode for the customer entity.
   */
  public function getRenderMode();

  /**
   * Get a render mode relevant for the entity.
   *
   * @param string $render_mode_id
   *   The render mode Id.
   *
   * @return \Drupal\wiki_challenge\RenderMode\RenderModeInterface
   *   The appropriate Render Mode for the given checker Id.
   */
  public function getRenderModeById($render_mode_id = NULL);

  /**
   * Returns an array of all registered Render Modes.
   *
   * @return \Drupal\wiki_challenge\RenderMode\RenderModeInterface[]
   *   All registered Render Modes keyed by render mode ID.
   */
  public function listRenderModes();

  /**
   * Returns an array of the IDs of all registered Render Modes.
   *
   * @return array
   *   Array of the IDs of all registered Render Modes.
   *   Format is: ['render mode key' => 'render mode name']
   */
  public function listRenderModeIds();

}
