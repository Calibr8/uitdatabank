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
   * @return array
   *   The input value, $value, if it is not empty.
   */
  public function process($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if (is_array($value)) {
      $pattern = "/\.(jpe?g|png|gif)$/i";

      foreach ($value as $index => $item) {

        if (isset($item['contentUrl']) && !preg_match($pattern, $item['contentUrl'])) {
          unset($value[$index]);
        }
      }
    }
    else {
      $value = [];
    }

    return $value;
  }

}
