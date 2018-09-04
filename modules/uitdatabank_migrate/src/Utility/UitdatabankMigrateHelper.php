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
   * @see \Drupal\uitdatabank\Form\Configuration
   */
  public static function addEndpointParameters(array $configuration, $endpoint_name) {

    $settings = \Drupal::config('uitdatabank.settings');

    // Pagination is handled in getSourceData().
    // @see Drupal\uitdatabank_migrate\Plugin\migrate_plus\data_parser\UitdatabankJson
    $request_params = [
      // Always get full data.
      'embed=true',
    ];

    if ($parameters = $settings->get($endpoint_name)) {
      $request_params[] = $parameters;
    }

    // @todo:  When performing an update run, add parameters to only get new and modified
    // items since last run. Use last sinc date instead of * for availableFrom and ommit availableTo
    // @see http://documentatie.uitdatabank.be/content/search_api_3/latest/searching/created-and-modified.html
    if (in_array($endpoint_name, ['events', 'places'])) {

      // Make sure we get items with workflow status DELETED too, so we know
      // we have to force unpublish them.
      $request_params[] = 'workflowStatus=*';

      // Always get all items, otherwise we might miss items we do need to update.
      $request_params[] = 'availableFrom=*';
      $request_params[] = 'availableTo=*';
    }

    if (in_array($endpoint_name, ['organizers', 'places'])) {

      // Add query to limit to already imported entities by ID
      // => migrate map => fetch all "sourceid1" and 'destid1' value
      // query based using
      // - 'sourceid1' for valid id's
      // - name for the 'dummies' without id, by looking up using 'destid1'.
      //
    }

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

      $row->setSourceProperty('organizer', $organizer);
    }

    return $row;
  }

  /**
   * Make sure Place has an id.
   *
   * UiTdatabank contains legacy places without id, which have not been
   * given an id by Publiq.
   * In that case, generate one, so we can at least use this one internally.
   *
   * @param \Drupal\migrate\Row $row
   *   The row to check.
   *
   * @return \Drupal\migrate\Row
   *   The validated row.
   */
  public static function validatePlaceId(Row $row) {

    $location = $row->getSourceProperty('location');

    if (!isset($location['@id'])) {
      $name = NULL;

      if (is_array($location['name'])) {
        if (isset($location['name']['nl'])) {
          $name = $location['name']['nl'];
        }
        else {
          $name = reset($location['name']);
        }
      }
      elseif (!empty($location['name'])) {
        $name = $location['name'];
      }

      if ($name) {
        $location['@id'] = Html::cleanCssIdentifier(strtolower($name));
      }
      $row->setSourceProperty('location', $location);
    }

    return $row;
  }

  /**
   * @todo: function description.
   *
   * @param array $configuration
   *   Migrate source plugin configuration.
   * @param string $endpoint_name
   *   Endpoint settings name.
   *
   * @return array
   *   Updated configuration array.
   *
   * @see \Drupal\uitdatabank\Form\Configuration
   */
  public function getSourceAndDestinyIds (array $configuration, $endpoint_name = 'places') {

   // @todo.
  }

}
