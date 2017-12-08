<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate_plus\Plugin\migrate\process\EntityGenerate;

/**
 * This plugin generates terms within the process plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_entity_generate"
 * )
 *
 * @see \Drupal\migrate_plus\Plugin\migrate\process\EntityGenerate
 *
 * All the configuration from the entity and lookup plugins applies here. In its
 * most simple form, this plugin needs no configuration. If there are fields on
 * the generated entity that are required or need some default value, that can
 * be provided via a default_values configuration option.
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
 */
class UitdatabankEntityGenerate extends EntityGenerate {

  /**
   * Fabricate an entity.
   *
   * @param array $values
   *   Values to use in creation of the entity.
   *
   * @return array
   *   Entity value array.
   */
  protected function entity(array $values) {
    $entity_values = parent::entity($values);

    // @see \Drupal\migrate\Plugin\migrate\process\Flatten
    if (is_array($entity_values['name'])) {
      $entity_values = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($entity_values)), TRUE);
    }

    return $entity_values;
  }

}
