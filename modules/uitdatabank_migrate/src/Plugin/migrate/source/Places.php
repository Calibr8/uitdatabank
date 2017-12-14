<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\source\Url;

/**
 * Source plugin for the Places.
 *
 * @MigrateSource(
 *   id = "places"
 * )
 */
class Places extends Url {

  /**
   * {@inheritdoc}
   *
   * @todo: catch fatal error when api key is not set, thrown by
   * Drupal\migrate_plus\Plugin\migrate_plus\data_fetcher\Http.php
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    $settings = \Drupal::config('uitdatabank.settings');
    $request_params = [
      'embed=true',
    ];

    if ($api_key = $settings->get('api_key')) {
      $request_params[] = 'apiKey=' . $api_key;
    }
    else {
      drupal_set_message('UiTdatabank API key required.', 'error');
    }

    if ($event_params = $settings->get('event_parameters')) {
      $request_params[] = $event_params;
    }

    $request_params = '?' . implode('&', $request_params);

    if (!is_array($configuration['urls'])) {
      $configuration['urls'] = [$configuration['urls'] . $request_params];
    }

    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // @todo: make plugin for this.
    // Handle translatable fields, also need to catch language not present
    // source.
    $translatable_fields = [
      'address',
      'description',
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

    return parent::prepareRow($row);
  }

}
