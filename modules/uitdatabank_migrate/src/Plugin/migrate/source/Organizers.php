<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\MigrationInterface;
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

}
