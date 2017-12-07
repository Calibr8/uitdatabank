<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process UiTdatabank geo data to geofield.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_geo",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankGeo extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!isset($value['latitude']) || !isset($value['longitude'])) {
      return NULL;
    }

    return \Drupal::service('geofield.wkt_generator')->WktBuildPoint([$value['longitude'], $value['latitude']]);
  }

}
