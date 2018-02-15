<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Determine duration value.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_duration",
 * )
 */
class UitdatabankDuration extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $parsed = 'inapplicable';
    $start = $end = NULL;

    $calendar_type = $row->getSourceProperty('calendar_type');
    switch ($calendar_type) {

      case 'single':
        $start = strtotime($row->getSourceProperty('start_date'));
        $end = strtotime($row->getSourceProperty('end_date'));
        break;

      case 'multiple':

        if ($sub_event = $row->getSourceProperty('sub_event')) {
          $start = strtotime($sub_event[0]['startDate']);
          $end = strtotime($sub_event[0]['endDate']);
        }
        break;

      case 'periodic':
      case 'permanent':
        break;
    }

    // Timezones don't matter here, we just need the time difference.
    if ($start && $end) {
      $diff = round(abs($end - $start) / 3600);
      $parsed = $diff > 4 ? 'full' : 'half';
    }

    return $parsed;
  }

}
