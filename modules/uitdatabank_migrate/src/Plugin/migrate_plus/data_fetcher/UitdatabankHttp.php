<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate_plus\data_fetcher;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\migrate_plus\Plugin\migrate_plus\data_fetcher\Http;

/**
 * Retrieve data over an HTTP connection for migration.
 *
 * @DataFetcher(
 *   id = "uitdatabank_http",
 *   title = @Translation("UiTdatabank HTTP")
 * )
 */
class UitdatabankHttp extends Http {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = \Drupal::httpClient();

    // Add API key to all request, throw error if not configured.
    $settings = \Drupal::config('uitdatabank.settings');
    if (!$settings->get('api_key')) {
      throw new PluginException('UiTdatabank API key required.');
    }
    else {
      $configuration['headers']['X-Api-Key'] = $settings->get('api_key');
    }

    $this->setRequestHeaders($configuration['headers']);
  }

}
