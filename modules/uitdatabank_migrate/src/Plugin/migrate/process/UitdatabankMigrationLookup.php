<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\migrate\process\MigrationLookup;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Looks up the value of a property based on a previous migration.
 *
 * Creates destination if not present using the previous migration.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_migration_lookup",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankMigrationLookup extends MigrationLookup {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $migration_ids = $this->configuration['migration'];
    if (!is_array($migration_ids)) {
      $migration_ids = [$migration_ids];
    }
    if (!is_array($value)) {
      $value = [$value];
    }
    $this->skipOnEmpty($value);
    $self = FALSE;
    /** @var \Drupal\migrate\Plugin\MigrationInterface[] $migrations */
    $destination_ids = NULL;
    $source_id_values = [];
    $migrations = $this->migrationPluginManager->createInstances($migration_ids);
    foreach ($migrations as $migration_id => $migration) {
      if ($migration_id == $this->migration->id()) {
        $self = TRUE;
      }
      if (isset($this->configuration['source_ids'][$migration_id])) {
        $configuration = ['source' => $this->configuration['source_ids'][$migration_id]];
        $source_id_values[$migration_id] = $this->processPluginManager
          ->createInstance('get', $configuration, $this->migration)
          ->transform(NULL, $migrate_executable, $row, $destination_property);
      }
      else {
        $source_id_values[$migration_id] = $value;
      }
      // Break out of the loop as soon as a destination ID is found.
      if ($destination_ids = $migration->getIdMap()->lookupDestinationId($source_id_values[$migration_id])) {
        break;
      }
    }

    if (!$destination_ids && !empty($this->configuration['no_stub'])) {
      return NULL;
    }

    if (!$destination_ids && ($self || isset($this->configuration['stub_id']) || count($migrations) == 1)) {
      // If the lookup didn't succeed, figure out which migration will do the
      // stubbing.
      if ($self) {
        $migration = $this->migration;
      }
      elseif (isset($this->configuration['stub_id'])) {
        $migration = $migrations[$this->configuration['stub_id']];
      }
      else {
        $migration = reset($migrations);
      }
      $destination_plugin = $migration->getDestinationPlugin(TRUE);
      // Only keep the process necessary to produce the destination ID.
      $process = $migration->getProcess();

      // We already have the source ID values but need to key them for the Row
      // constructor.
      $source_ids = $migration->getSourcePlugin()->getIds();
      $values = [];
      foreach (array_keys($source_ids) as $index => $source_id) {
        $values[$source_id] = $source_id_values[$migration->id()][$index];
      }

      /*
       * Here is the difference with MigrationLookup:
       * - append given real values, using field selectors as defined for the
       *   source plugin.
       * - create a real row
       * - run prepareRow() on the new row.
       */
      foreach ($migration->getSourcePlugin()->fields() as $field_name => $selector) {
        $field_data = $value;
        $field_selectors = explode('/', trim($selector, '/'));
        foreach ($field_selectors as $field_selector) {
          if (isset($field_data[$field_selector])) {
            $field_data = $field_data[$field_selector];
          }
          else {
            $field_data = NULL;
          }
        }
        $values[$field_name] = $field_data;
      }
      $values += $migration->getSourceConfiguration();
      $stub_row = $this->createRow($values, $source_ids);
      $migration->getSourcePlugin()->prepareRow($stub_row);

      // Do a normal migration with the stub row.
      $migrate_executable->processRow($stub_row, $process);
      $destination_ids = [];
      $id_map = $migration->getIdMap();
      try {
        $destination_ids = $destination_plugin->import($stub_row);
      }
      catch (\Exception $e) {
        $id_map->saveMessage($stub_row->getSourceIdValues(), $e->getMessage());
      }

      if ($destination_ids) {
        $id_map->saveIdMapping($stub_row, $destination_ids, MigrateIdMapInterface::STATUS_NEEDS_UPDATE);
      }
    }
    if ($destination_ids) {
      if (count($destination_ids) == 1) {
        return reset($destination_ids);
      }
      else {
        return $destination_ids;
      }
    }
  }

  /**
   * Create a row source.
   *
   * @param array $values
   *   An array of values to add as properties on the object.
   * @param array $source_ids
   *   An array containing the IDs of the source using the keys as the field
   *   names.
   *
   * @return \Drupal\migrate\Row
   *   The row.
   */
  protected function createRow(array $values, array $source_ids) {
    return new Row($values, $source_ids);
  }

}
