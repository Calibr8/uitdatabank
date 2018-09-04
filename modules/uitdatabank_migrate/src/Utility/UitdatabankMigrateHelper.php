<?php

namespace Drupal\uitdatabank_migrate\Utility;

use Drupal\Component\Utility\Html;
use Drupal\migrate\Row;
use Drupal\node\Entity\Node;

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

    // Event specific parameters.
    if ($endpoint_name == 'events') {

      if ($parameters = $settings->get($endpoint_name)) {
        $request_params[] = $parameters;
      }
    }

    // Event and places specific parameters.
    if (in_array($endpoint_name, ['events', 'places'])) {

      // Make sure we get items with workflow status DELETED too, so we know
      // we have to force unpublish them.
      $request_params[] = 'workflowStatus=*';

      // Get all items, otherwise we might miss items we do need to update.
      $request_params[] = 'availableFrom=*';
      $request_params[] = 'availableTo=*';

      // @todo:  When performing an update run, add parameters to only get new
      // and modified items since last run. Use last sinc date instead of * for
      // availableFrom and ommit availableTo
      // @see http://documentatie.uitdatabank.be/content/search_api_3/latest/searching/created-and-modified.html
    }

    // Organizer and places specific parameters.
    // @todo: uncomment original when we have solution for organizer filtering.
    // @see below.
    //if (in_array($endpoint_name, ['organizers', 'places'])) {
    if (in_array($endpoint_name, ['places'])) {

      if ($settings->get($endpoint_name . '_existing_only')) {
        $request_params[] = self::getExistingItemsRequestParameters($configuration, $endpoint_name);
      }
      elseif ($parameters = $settings->get($endpoint_name)) {
        $request_params[] = $parameters;
      }
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
   * Get request parameters to filter for existing places or organisations.
   *
   * @param array $configuration
   *   Migrate source plugin configuration.
   * @param string $endpoint_name
   *   Endpoint settings name.
   *
   * @return string
   *   Request parameter string.
   */
  public static function getExistingItemsRequestParameters(array $configuration, $endpoint_name = 'places') {

    $query = sprintf("SELECT sourceid1, destid1 FROM {%s} ", 'migrate_map_uitdatabank_' . $endpoint_name);
    $map = \Drupal::database()->query($query)->fetchAllAssoc('destid1');

    // Regex to capture id from source url (e.g.: https://io.uitdatabank.be/place/7655e3c7-a92e-4e0b-8b10-8980e8701f80)
    $regex = "/^https:\/\/.+\/\w+\/(.+)$/";

    // Create list of items we can fetch by id, or by name as fallback.
    $processed = [];
    foreach ($map as $id => $item) {
      $sourceid = $item->sourceid1;

      $matches = [];
      preg_match($regex, $sourceid, $matches);

      // Id's can be as is.
      if (count($matches) > 1) {
        $processed[] = sprintf('id:"%s"', $matches[1]);
      }

      // Fetch and prepare name value.
      elseif ($node = Node::load($id)) {

        $name = urlencode($node->getTitle());
        // Replace '+' back to spaces, as '+' is a reserved character in
        // Elastic Search Query string syntax and spaces are allowed.
        // @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html#_reserved_characters
        $name = str_replace('+', ' ', $name);

        switch ($endpoint_name) {

          case 'organizers':
            $processed[] = sprintf('name:"%s"', $name);
            break;

          default:
            $processed[] = sprintf('name.\*:"%s"', $name);
        }
      }
    }

    // todo: This doesn't work for organizers, 'q' is unknown query parameter,
    // but we don't know yet what does work to fetch multiple organizers.
    return sprintf('q=(%s)', implode(' OR ', $processed));
  }

}
