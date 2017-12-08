<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\process\EntityLookup;

/**
 * This plugin generates multiple entities within the process plugin.
 *
 * Based on \Drupal\migrate_plus\Plugin\migrate\process\EntityGenerate.
 *
 * All the configuration from the lookup plugin applies here. In its most
 * simple form, this plugin needs no configuration. If there are fields on the
 * generated entity that are required or need some default value, that can be
 * provided via a default_values configuration option.
 *
 * @see \Drupal\migrate_plus\Plugin\migrate\process\EntityLookup
 *
 * Example usage with default_values configuration:
 * @code
 * destination:
 *   plugin: 'entity:node'
 * process:
 *   type:
 *     plugin: default_value
 *     default_value: page
 *   field_tags:
 *     plugin: entity_generate
 *     source: tags
 *     default_values:
 *       description: Default description
 *       field_long_description: Default long description
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_entity_generate",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankEntityGenerate extends EntityLookup {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrateExecutable, Row $row, $destinationProperty) {
    $result = [];

    foreach ($value as $index => $item) {

      $key = isset($item['name']) ? $item['name'] : $item;
      // Creates an entity if the lookup determines it doesn't exist.
      if (!($entity = parent::transform($key, $migrateExecutable, $row, $destinationProperty))) {
        $entity = $this->generateEntity($item);
      }

      $result[$index] = $entity;
    }

    return $result;
  }

  /**
   * Generates an entity for a given value.
   *
   * @param string $value
   *   Value to use in creation of the entity.
   *
   * @return int|string
   *   The entity id of the generated entity.
   */
  protected function generateEntity($value) {
    if (!empty($value)) {
      $entity = $this->entityManager
        ->getStorage($this->lookupEntityType)
        ->create($this->entity($value));
      $entity->save();

      return $entity->id();
    }
  }

  /**
   * Fabricate an entity.
   *
   * @param $value
   *   Values to use in creation of the entity.
   *
   * @return array
   *   Entity value array.
   */
  protected function entity($value) {

    // Support both singles and multiples.
    $entity_values = is_array($value) ? $value : [$this->lookupValueKey => $value];

    if ($this->lookupBundleKey) {
      $entity_values[$this->lookupBundleKey] = $this->lookupBundle;
    }

    // Gather any static default values for properties/fields.
    if (isset($this->configuration['default_values']) && is_array($this->configuration['default_values'])) {
      foreach ($this->configuration['default_values'] as $key => $value) {
        $entity_values[$key] = $value;
      }
    }

    return $entity_values;
  }

}
