<?php

namespace Drupal\wiki_challenge;

use Drupal\Core\Database\Connection;
use Drupal\Component\Utility\Random;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides logic responsible for generating nodes of type Wiki Articles.
 */
final class GenerateWikiArticles {

  use StringTranslationTrait;

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The random data generator.
   *
   * @var \Drupal\Component\Utility\Random
   */
  protected $random;

  /**
   * Include the messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Include the database service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Include the entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Generate Wiki Articles construct.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Database\Connection $database
   *   The database service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, LanguageManagerInterface $language_manager, MessengerInterface $messenger, Connection $database) {
    $this->entityTypeManager = $entity_type_manager;
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->languageManager = $language_manager;
    $this->messenger = $messenger;
    $this->database = $database;
  }

  /**
   * Method responsible for creating content.
   *
   * The number of elements is less than or equal to 40. The number of
   * generated nodes cannot be greater than 40 in a non-batch operation
   * to avoid timeouts.
   *
   * @param int $num
   *   The numbers of nodes to generate.
   */
  public function generateContent($num = 40) {
    if (!($num > 0)) {
      // Nothing to do - Stop here.
      return NULL;
    }
    if ($num > 40) {
      // The number of generated nodes cannot be greater than 40 in a
      // non-batch operation to avoid timeouts.
      $num = 40;
    }
    $config = [
      'langcode' => $this->languageManager->getDefaultLanguage()->getId(),
      'num' => $num,
      'time_range' => 0,
      'type' => WIKI_CHALLENGE_WIKIPEDIA_ARTICLE_BUNDLE,
      'users' => $this->getUsers(),
      'title_length' => 7,
    ];
    // Generate nodes.
    for ($i = 1; $i <= $config['num']; $i++) {
      $this->addNode($config);
    }
    $this->messenger->addMessage($this->formatPlural($config['num'], '1 node created of type @bundle.', 'Finished creating @count nodes of type @bundle.', ['@bundle' => $config['type']]));
  }

  /**
   * Create one node. Used by both batch and non-batch code branches.
   *
   * @param array $config
   *   The config used to generate the nodes.
   */
  protected function addNode(array &$config = []) {
    if (!isset($config['time_range'])) {
      $config['time_range'] = 0;
    }
    $users = $config['users'];
    $delta = array_rand($users);
    $uid = $users[$delta];
    $node = $this->nodeStorage->create([
      'nid' => NULL,
      'type' => $config['type'],
      'title' => $this->getRandom()->sentences(mt_rand(2, $config['title_length']), TRUE),
      'uid' => $uid,
      'revision' => mt_rand(0, 1),
      'status' => TRUE,
      'promote' => mt_rand(0, 1),
      'created' => REQUEST_TIME - mt_rand(0, $config['time_range']),
      'langcode' => $config['langcode'],
    ]);
    // Populate all fields with sample values.
    $this->populateFields($node);
    // See devel_generate_node_insert() for actions that happen before and after
    // this save.
    $node->save();
  }

  /**
   * Retrieve 50 uids from the database.
   */
  protected function getUsers() {
    $users = [];
    $query = "SELECT uid FROM {users}";
    $result = $this->database->queryRange($query, 0, 50);
    foreach ($result as $record) {
      $users[] = $record->uid;
    }
    return $users;
  }

  /**
   * Returns the random data generator.
   *
   * @return \Drupal\Component\Utility\Random
   *   The random data generator.
   */
  protected function getRandom() {
    if (!$this->random) {
      $this->random = new Random();
    }
    return $this->random;
  }

  /**
   * Populate the fields on a given entity with sample values.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be enriched with sample field values.
   */
  public function populateFields(EntityInterface $entity) {
    $properties = [
      'entity_type' => $entity->getEntityType()->id(),
      'bundle' => $entity->bundle()
    ];
    /** @var \Drupal\field\FieldConfigInterface[] $instances */
    $instances = $this->entityTypeManager->getStorage('field_config')->loadByProperties($properties);
    foreach ($instances as $instance) {
      $field_storage = $instance->getFieldStorageDefinition();
      $max = $cardinality = $field_storage->getCardinality();
      if ($cardinality == FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED) {
        // Just an arbitrary number for 'unlimited'.
        $max = rand(1, 3);
      }
      $field_name = $field_storage->getName();
      $entity->$field_name->generateSampleItems($max);
    }
  }

}
