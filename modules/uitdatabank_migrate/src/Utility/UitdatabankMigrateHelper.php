<?php

namespace Drupal\uitdatabank_migrate\Utility;

use Drupal\Component\Utility\Html;
use Drupal\migrate\Row;

/**
 * UitdatabankMigrateHelper.
 *
 * @package Drupal\uitdatabank_migrate\Utility
 */
class UitdatabankMigrateHelper {

  /**
   * Add endpoint parameters to the configuration as set in config.
   *
   * @param array $configuration
   *   Migrate source plugin configuration.
   * @param string $endpoint_name
   *   Endpoint settings name.
   *
   * @return array
   *   Updated configuration array.
   *
   * @see \Drupal\uitdatabank\Form\UitdatabankConfiguration
   */
  public static function addEndpointParameters(array $configuration, $endpoint_name) {

    $settings = \Drupal::config('uitdatabank.settings');

    // Pagination is handled in getSourceData().
    // @see Drupal\uitdatabank_migrate\Plugin\migrate_plus\data_parser\UitdatabankJson
    $request_params = [
      'embed=true',
    ];

    if ($parameters = $settings->get($endpoint_name)) {
      $request_params[] = $parameters;
    }

    // When performing an update run, add parameters to only getnew and modified
    // items since last run.
    // @see http://documentatie.uitdatabank.be/content/search_api_3/latest/searching/created-and-modified/
    // @todo

    $request_params = '?' . implode('&', $request_params);

    if (!is_array($configuration['urls'])) {
      $configuration['urls'] = [$configuration['urls'] . $request_params];
    }

    return $configuration;
  }

  /**
   * Make sure Organizer has an id.
   *
   * UiTdatabank contains legacy organizers without id, which have not been
   * given an id by Publiq.
   * In that case, generate one, so we can at least use this one internally.
   *
   * @param \Drupal\migrate\Row $row
   *   The row to check.
   *
   * @return \Drupal\migrate\Row
   *   The validated row.
   */
  public static function validateOrganizerId(Row $row) {

    $organizer = $row->getSourceProperty('organizer');

    if (!isset($organizer['@id'])) {
      $organizer['@id'] = Html::cleanCssIdentifier(strtolower($organizer['name']));
    }

    $row->setSourceProperty('organizer', $organizer);

    return $row;
  }

}
