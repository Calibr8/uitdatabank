<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;
use Drupal\uitdatabank\Form\UitdatabankConfiguration;

/**
 * Obtain JSON data for migration, with paged results.
 *
 * @DataParser(
 *   id = "uitdatabank_json",
 *   title = @Translation("UiTdatabank JSON")
 * )
 */
class UitdatabankJson extends Json {

  /**
   * {@inheritdoc}
   */
  protected function getSourceData($url) {
    $start = 0;
    $final_source_data = $source_data = [];

    $page_max_item = UitdatabankConfiguration::API_PAGE_MAX_ITEMS;

    do {
      $paged_url = "$url&start=$start&limit=" . $page_max_item;
      $response = $this->getDataFetcherPlugin()->getResponseContent($paged_url);

      // Convert objects to associative arrays.
      $source_data = json_decode($response, TRUE);

      // If json_decode() has returned NULL, it might be that the data isn't
      // valid utf8 - see http://php.net/manual/en/function.json-decode.php#86997.
      if (is_null($source_data)) {
        $utf8response = utf8_encode($response);
        $source_data = json_decode($utf8response, TRUE);
      }

      // Backwards-compatibility for depth selection.
      if (is_int($this->itemSelector)) {
        $source_data = $this->selectByDepth($source_data);
      }
      else {
        // Otherwise, we're using xpath-like selectors.
        $selectors = explode('/', trim($this->itemSelector, '/'));
        foreach ($selectors as $selector) {
          if (!empty($selector)) {
            $source_data = $source_data[$selector];
          }
        }
      }

      $final_source_data = array_merge($final_source_data, $source_data);

      $start += $page_max_item;

      // @todo: remove when API can handle more that 10000 items.
      if ($start >= 10000) {
        break;
      }
    } while (count($source_data) >= $page_max_item);

    return $final_source_data;
  }

  /**
   * {@inheritdoc}
   */
  protected function fetchNextRow() {
    $current = $this->iterator->current();
    if ($current) {
      foreach ($this->fieldSelectors() as $field_name => $selector) {
        $field_data = $current;
        $field_selectors = explode('/', trim($selector, '/'));
        foreach ($field_selectors as $field_selector) {
          if (isset($field_data[$field_selector])) {
            $field_data = $field_data[$field_selector];
          }
          else {
            $field_data = NULL;
          }
        }
        $this->currentItem[$field_name] = $field_data;
      }
      if (!empty($this->configuration['include_raw_data'])) {
        $this->currentItem['raw'] = $current;
      }
      $this->iterator->next();
    }
  }

}
