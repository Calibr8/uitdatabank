<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Fill Age field based on age_min and age_max.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_ages",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankAges extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $parsed = [];

    for ($i = $row->getSourceProperty('age_min'); $i <= $row->getSourceProperty('age_max'); $i++) {
      $parsed[$i] = $i;
    }

    return $parsed;
  }

}
