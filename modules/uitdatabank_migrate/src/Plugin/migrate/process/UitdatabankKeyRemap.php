<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Changes the source keys based on a dynamic map.
 *
 * Example for a term field to be used for UitdatabankEntityGenerate :
 *
 * Source array:
 *
 * @code
 * $term = [
 *    'label' => '...',
 *    'id' => '...',
 *    'domain' => '...',
 * ];
 * @endcode
 *
 * UitdatabankEntityGenerate expects:
 *
 * @code
 * $term = [
 *    'name' => '...',
 *    'field_uitdatabank_terms_id' => '...',
 *    'field_uitdatabank_terms_domain' => '...',
 * ];
 * @endcode
 *
 * This can be fixed with following config:
 *
 * @code
 * process:
 *   bar:
 *     plugin: uitdatabank_key_dynamic_map
 *     source: terms
 *     plugin: uitdatabank_key_remap
 *        map:
 *          label: name
 *          id: field_uitdatabank_terms_id
 *          domain: field_uitdatabank_terms_domain
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_key_remap",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankKeyRemap extends ProcessPluginBase {

  /**
   * Replace source keys with target keys.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $result = [];
    $map = $this->configuration['map'];

    foreach ($value as $index => $item) {

      foreach ($map as $source => $target) {
        if (isset($item[$source])) {
          $result[$index][$target] = $item[$source];
        }
      }
    }

    return $result;
  }

}
