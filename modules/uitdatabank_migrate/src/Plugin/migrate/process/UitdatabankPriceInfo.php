<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Maps UiTdatabank price info data to uitdatabank_price_info field.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_price_info",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankPriceInfo extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $parsed = [];

    if ($value && is_array($value)) {
      foreach ($value as $index => $item) {
        $parsed[$index] = [
          'category' => $item['category'],
          'name' => $item['name'],
          'price' => $item['price'],
          'price_currency' => $item['priceCurrency'],
        ];
      }
    }

    return $parsed;
  }

}
