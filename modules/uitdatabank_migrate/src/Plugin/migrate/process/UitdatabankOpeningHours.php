<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Maps UiTdatabank opening hour data to uitdatabank_opening_hours field.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_opening_hours",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankOpeningHours extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $parsed = [];

    if ($value && is_array($value)) {
      foreach ($value as $index => $item) {
        $parsed[$index] = [
          'opens' => $item['opens'],
          'closes' => $item['closes'],
          'days_of_week' => implode(',', $item['dayOfWeek']),
        ];
      }
    }

    return $parsed;
  }

}
