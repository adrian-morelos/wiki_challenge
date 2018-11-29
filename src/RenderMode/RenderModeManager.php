<?php

namespace Drupal\wiki_challenge\RenderMode;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * The Render Mode manager.
 *
 * Responsible for handling Render Modes.
 *
 * @package Drupal\wiki_challenge\RenderMode
 */
class RenderModeManager implements RenderModeManagerInterface {

  /**
   * The list of Render Modes.
   *
   * @var \Drupal\wiki_challenge\RenderMode\RenderModeInterface[]
   */
  protected $renderModes = [];

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a RenderModeManager object.
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
  public function addRenderMode(RenderModeInterface $render_mode) {
    $this->renderModes[$render_mode->getId()] = $render_mode;
  }

  /**
   * {@inheritdoc}
   */
  public function getRenderMode() {
    $config = $this->configFactory->get('wiki_challenge.render_modes_manager');
    $render_mode_id = $config->get('default_render_mode_id');
    return $this->renderModes[$render_mode_id];
  }

  /**
   * {@inheritdoc}
   */
  public function getRenderModeById($render_mode_id = NULL) {
    $render_mode = FALSE;
    if (!is_null($render_mode_id)) {
      $render_mode = $this->renderModes[$render_mode_id];
    }
    return $render_mode;
  }

  /**
   * {@inheritdoc}
   */
  public function listRenderModes() {
    return $this->renderModes;
  }

  /**
   * {@inheritdoc}
   */
  public function listRenderModeIds() {
    $ids = [];
    foreach ($this->renderModes as $render_mode) {
      $ids[$render_mode->getId()] = $render_mode->getName();
    }
    return $ids;
  }

}
