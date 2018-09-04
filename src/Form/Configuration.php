<?php

namespace Drupal\uitdatabank\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\uitdatabank\ConfigurationManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Configuration.
 *
 * @ingroup uitdatabank
 */
class Configuration extends ConfigFormBase {

  /**
   * The configuration manager.
   *
   * @var \Drupal\uitdatabank\ConfigurationManager
   */
  protected $configManager;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\uitdatabank\ConfigurationManager $config_manager
   *   The configuration manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ConfigurationManager $config_manager) {
    parent::__construct($config_factory);
    $this->configManager = $config_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('uitdatabank.configuration_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'uitdatabank.settings',
      'uitdatabank.settings.defaults',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'uitdatabank_config';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->getConfig();
    $defaults = $this->getConfigDefaults();

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#default_value' => $settings->get('api_key'),
      '#required' => TRUE,
      '#description' => $this->t('Request your API key <a href=":url" target="_blank">here</a>', [':url' => $this->configManager->getApiKeyRequestUrl()]),
    ];

    $form['parameters'] = array(
      '#type' => 'details',
      '#title' => $this->t('Endpoint parameters'),
      '#open' => TRUE,
    );

    // @todo: more extensive description of what is default, expected and possible.
    $instructions[] = $this->t('Add parameters per endpoint to narrow the imported/synced dataset for each content type.');
    $instructions[] = $this->t('Explore the <a href=":url" target="_blank">official documentation</a> to find all available parameters.', [':url' => $this->configManager->getDocumentationUrl()]);
    $instructions[] = $this->t('<strong>Notes</strong>');
    $markup = sprintf('<p>%s</p>', implode('</p><p>', $instructions));

    $notes[] = $this->t('"embed=true" is always added.');
    $notes[] = $this->t('Pagination using "start" and "limit" parameters is already handled.');
    $markup .= sprintf('<ol><li>%s</li></ol>', implode('</li><li>', $notes));

    $form['parameters']['notes'] = array(
      '#type' => 'item',
      '#markup' => $markup,
    );

    $form['parameters']['events'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Parameters for https://search.uitdatabank.be/events/'),
      '#default_value' => $settings->get('events'),
    ];

    $form['parameters']['places'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Parameters for https://search.uitdatabank.be/places/'),
      '#default_value' => $settings->get('places'),
      '#states' => [
        'disabled' => [
          ':input[name=places_existing_only]' => [
            'checked' => TRUE,
          ],
        ],
      ],
    ];
    $form['parameters']['places_existing_only'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Only update existing places, fetched through events.'),
      '#default_value' => $settings->get('places_existing_only'),
    ];

    $form['parameters']['organizers'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Parameters for https://search.uitdatabank.be/organizers/'),
      '#default_value' => $settings->get('organizers'),
      '#states' => [
        'disabled' => [
          ':input[name=organizers_existing_only]' => [
            'checked' => TRUE,
          ],
        ],
      ],
    ];
    $form['parameters']['organizers_existing_only'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Only update existing organizers, fetched through events.'),
      '#default_value' => $settings->get('places_existing_only'),
    ];

    $form['defaults'] = array(
      '#type' => 'details',
      '#title' => $this->t('Defaults'),
      '#open' => TRUE,
    );

    $fid = $defaults->get('image');
    $form['defaults']['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Default image'),
      '#description' => $this->t('Used for Events and Places when no image is provided or copying of an image fails.'),
      '#upload_location' => file_default_scheme() . '://' . $this->configManager->getImageDirectory(),
      '#default_value' => $fid ? [$fid] : NULL,
      '#upload_validators' => [
        'file_validate_extensions' => ['gif png jpg jpeg'],
      ],
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $default_setting_names = [
      'image',
    ];

    $settings = $this->getConfig();
    $defaults = $this->getConfigDefaults();


    $values = $form_state->cleanValues()->getValues();
    unset($values['notes']);

    // Process default settings.
    foreach ($default_setting_names as $name) {
      $setting = is_array($values[$name]) ? reset($values[$name]) : $values[$name];
      $defaults->set($name, $setting);
      unset($values[$name]);
    }
    $defaults->save();

    // Process other settings.
    foreach ($values as $key => $value) {
      $value = is_array($value) ? reset($value) : $value;
      $settings->set($key, $value);
    }
    $settings->save();

    // @todo Check if we need to update media entities referencing the old fid
    // if fid has changed and remove old file.

    $message = $this->t('UiTdatabank settings saved.');
    drupal_set_message($message);
  }

  /**
   * Get configuration, without defaults.
   *
   * @return \Drupal\Core\Config\Config
   *   Editable config object.
   */
  private function getConfig() {
    return $this->config('uitdatabank.settings');
  }

  /**
   * Get defaults configuration.
   *
   * This configuration includes:
   *  - default/fallback image fid.
   *
   * @return \Drupal\Core\Config\Config
   *   Editable config object.
   */
  private function getConfigDefaults() {
    return $this->config('uitdatabank.settings.defaults');
  }

}
