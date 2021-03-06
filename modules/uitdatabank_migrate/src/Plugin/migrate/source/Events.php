<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\source\Url;
use Drupal\uitdatabank_migrate\Utility\UitdatabankMigrateHelper;

/**
 * Source plugin for the Events.
 *
 * @MigrateSource(
 *   id = "events"
 * )
 */
class Events extends Url {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {

    $configuration = UitdatabankMigrateHelper::addEndpointParameters($configuration, 'events');
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // Handle translatable fields, also need to catch language not present
    // source.
    $translatable_fields = [
      'address',
      'description',
      'name',
    ];
    $langcode = $row->getSourceProperty('language');
    $langcode = $langcode ?: 'nl';
    foreach ($translatable_fields as $name) {
      $value = $row->getSourceProperty($name);

      if (isset($value[$langcode])) {
        $value = $value[$langcode];
        $row->setSourceProperty($name, $value);
      }
    }
    unset($name, $value, $translatable_fields);

    // Preprocess typicalAgeRange values.
    $value = $row->getSourceProperty('age_min');
    $ages = explode('-', $value);
    foreach ($ages as $index => $age) {
      $ages[$index] = (int) $age;
    }
    $row->setSourceProperty('age_min', isset($ages[0]) ? $ages[0] : 0);
    $row->setSourceProperty('age_max', isset($ages[1]) && $ages[1] ? $ages[1] : 100);
    unset($age, $ages, $index, $value);

    $row = UitdatabankMigrateHelper::validateOrganizerId($row);
    $row = UitdatabankMigrateHelper::validatePlaceId($row);

    return parent::prepareRow($row);
  }

}
