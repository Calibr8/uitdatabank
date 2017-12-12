<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Skips processing the property row when the input value is empty.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_check_if_image",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankCheckIfImage extends ProcessPluginBase {

  /**
   * Stops processing the current property when value is not set.
   *
   * @param mixed $value
   *   The input value.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   The migration in which this process is being executed.
   * @param \Drupal\migrate\Row $row
   *   The row from the source to process.
   * @param string $destination_property
   *   The destination property currently worked on. This is only used together
   *   with the $row above.
   *
   * @return mixed
   *   The input value, $value, if it is not empty.
   *
   * @throws \Drupal\migrate\MigrateSkipProcessException
   *   Thrown if the source property is not set and rest of the process should
   *   be skipped.
   */
  public function process($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if (is_array($value)) {
      foreach ($value as $index => $item) {
        if ($item['@type'] != 'schema:ImageObject') {
          unset($value[$index]);
        }
      }
    }

    if (empty($value)) {
      throw new MigrateSkipProcessException();
    }

    return $value;
  }

}
