<?php

namespace Drupal\uitdatabank;

/**
 * Uitdatabank Configuration Manager.
 */
class ConfigurationManager {

  /**
   * API key/project registration url.
   *
   * @var string
   */
  const API_KEY_REQUEST_URL = 'https://projectaanvraag.uitdatabank.be';

  /**
   * Max page items supported by api.
   *
   * @var int
   *
   * @todo: remove when /places doesn't return '500' error with pages > 1000.
   */
  const API_PAGE_MAX_ITEMS = 1000;

  /**
   * Default/fallback image.
   *
   * @var string
   */
  const DEFAULT_IMAGE_NAME = 'uitdatabank_default_image.jpg';

  /**
   * API documentation url.
   *
   * @var string
   */
  const DOCUMENTATION_URL = 'http://documentatie.uitdatabank.be/content/search_api_3/latest/start.html';

  /**
   * Image directory where all downloaded images will be placed.
   *
   * @var string
   */
  const IMAGE_DIRECTORY = 'uitdatabank';

  /**
   * Config.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Defaults config.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $configDefaults;

  /**
   * Uitdatabank Configuration Manager constructor.
   */
  public function __construct() {
    $this->config = \Drupal::configFactory()->getEditable('uitdatabank.settings');
    $this->configDefaults = \Drupal::configFactory()->getEditable('uitdatabank.settings.defaults');
  }

  /**
   * Get API key/project registration url.
   *
   * @return string
   *   The registration url;
   */
  public function getApiKeyRequestUrl() {
    return static::API_KEY_REQUEST_URL;
  }

  /**
   * Get API configuration, without defaults.
   *
   * @return \Drupal\Core\Config\Config
   *   Editable config object.
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * Get API defaults configuration.
   *
   * This configuration includes:
   *  - default/fallback image fid.
   *
   * @return \Drupal\Core\Config\Config
   *   Editable config object.
   */
  public function getConfigDefaults() {
    return $this->configDefaults;
  }

  /**
   * Get default/fallback image file name.
   *
   * @return string
   *   The image file name.
   */
  public function getDefaultImageName() {
    return static::DEFAULT_IMAGE_NAME;
  }

  /**
   * Get API documentation url.
   *
   * @return string
   *   The documentation url.
   */
  public function getDocumentationUrl() {
    return static::DOCUMENTATION_URL;
  }

  /**
   * Get image directory name where all downloaded images will be placed.
   *
   * @return string
   *   The directory name.
   */
  public function getImageDirectory() {
    return static::IMAGE_DIRECTORY;
  }

}