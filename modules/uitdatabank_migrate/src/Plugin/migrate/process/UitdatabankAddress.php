<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Maps UiTdatabank address data to addressfield.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_address",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankAddress extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $parsed = [
      'country_code' => $value['addressCountry'],
      'locality' => $value['addressLocality'],
      'postal_code' => $value['postalCode'],
      'address_line1' => $value['streetAddress'],
    ];

    return $parsed;
  }

}
