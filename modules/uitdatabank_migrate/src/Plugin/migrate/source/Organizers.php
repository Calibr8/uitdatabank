<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\source\Url;
use Drupal\uitdatabank_migrate\Utility\UitdatabankMigrateHelper;

/**
 * Source plugin for the Organizers.
 *
 * @MigrateSource(
 *   id = "organizers"
 * )
 */
class Organizers extends Url {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {

    $configuration = UitdatabankMigrateHelper::addEndpointParameters($configuration, 'organizers');
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // Handle translatable fields, also need to catch language not present
    // source.
    $translatable_fields = [
      'name',
    ];
    $langcode = $row->getSourceProperty('language');
    $langcode = $langcode?: 'nl';
    foreach ($translatable_fields as $name) {
      $value = $row->getSourceProperty($name);

      if (isset($value[$langcode])) {
        $value = $value[$langcode];
        $row->setSourceProperty($name, $value);
      }
    }
    unset($name, $value, $translatable_fields);

    $row = UitdatabankMigrateHelper::validateOrganizerId($row);

    return parent::prepareRow($row);
  }

}
