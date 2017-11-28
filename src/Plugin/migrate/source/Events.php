<?php

namespace Drupal\uitdatabank\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\source\Url;

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
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    // @todo: add query parameters to url.
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // todo: custom stuff.

    return parent::prepareRow($row);
  }

}
