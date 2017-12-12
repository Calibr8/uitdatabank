<?php

namespace Drupal\uitdatabank\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

define('UITDATABANK_API_KEY_REQUEST_URL', 'https://projectaanvraag.uitdatabank.be');
define('UITDATABANK_API_DOCUMENTATION_URL', 'http://documentatie.uitdatabank.be/content/search_api_3/latest/');

/**
 * Class UitdatabankConfiguration.
 *
 * @ingroup uitdatabank
 */
class UitdatabankConfiguration extends FormBase {

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
    $settings = \Drupal::config('uitdatabank.settings');

    $api_key = $settings->get('api_key');
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#default_value' => $api_key,
      '#required' => TRUE,
      '#description' => $this->t('Request your API key <a href=":url" target="_blank">here</a>', [':url' => UITDATABANK_API_KEY_REQUEST_URL]),
    ];

    // @todo: more extensive description of what is default, expected and possible.
    $event_parameters = $settings->get('event_parameters');
    $form['event_parameters'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Parameters for https://search.uitdatabank.be/events/'),
      '#default_value' => $event_parameters,
      '#description' => $this->t('Note: "embed=true" is used by default.<br>Explore <a href=":url" target="_blank">official documentation</a> to find all available parameters.', [':url' => UITDATABANK_API_DOCUMENTATION_URL]),
    ];

    // @todo: settings for https://search.uitdatabank.be/places/

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    \Drupal::configFactory()
      ->getEditable('uitdatabank.settings')
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('event_parameters', $form_state->getValue('event_parameters'))
      ->save();
  }

}
