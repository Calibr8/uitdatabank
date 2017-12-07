<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process UiTdatabank booking url with label.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_booking_url"
 * )
 */
class UitdatabankBookingUrl extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $parsed = [
      'uri' => $value,
      'title' => $row->getSourceProperty('bookinginfo_urllabel'),
    ];

    return $parsed;
  }

}
