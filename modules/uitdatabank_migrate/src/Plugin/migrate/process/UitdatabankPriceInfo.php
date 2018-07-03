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

        // Catch translated tariff names.
        $name = $item['name'];
        if (is_array($name)) {
          $langcode = $row->getSourceProperty('language');
          $langcode = $langcode ?: 'nl';
          $name = $name[$langcode];
        }

        $parsed[$index] = [
          'category' => $item['category'],
          'name' => $name,
          'price' => $item['price'],
          'price_currency' => $item['priceCurrency'],
        ];
      }
    }

    return $parsed;
  }

}
