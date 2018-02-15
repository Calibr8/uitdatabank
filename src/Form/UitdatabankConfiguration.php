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

    $form['parameters'] = array(
      '#type' => 'details',
      '#title' => $this->t('Endpoint parameters'),
      '#open' => TRUE,
    );

    // @todo: more extensive description of what is default, expected and possible.
    $instructions[] = $this->t('Add parameters per endpoint to narrow the imported/synced dataset for each content type.');
    $instructions[] = $this->t('Explore the <a href=":url" target="_blank">official documentation</a> to find all available parameters.', [':url' => UITDATABANK_API_DOCUMENTATION_URL]);
    $instructions[] = $this->t('<strong>Notes</strong>');
    $markup = sprintf('<p>%s</p>', implode('</p><p>', $instructions));

    $notes[] = $this->t('"embed=true" is always added.');
    $markup .= sprintf('<ol><li>%s</li></ol>', implode('</li><li>', $notes));

    $form['parameters'][''] = array(
      '#type' => 'item',
      '#markup' => $markup,
    );

    $event_parameters = $settings->get('events_parameters');
    $form['parameters']['events_parameters'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Parameters for https://search.uitdatabank.be/events/'),
      '#default_value' => $event_parameters,
    ];

    $places_parameters = $settings->get('places_parameters');
    $form['parameters']['places_parameters'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Parameters for https://search.uitdatabank.be/places/'),
      '#default_value' => $places_parameters,
    ];

    $organizers_parameters = $settings->get('organizers_parameters');
    $form['parameters']['organizers_parameters'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Parameters for https://search.uitdatabank.be/organizers/'),
      '#default_value' => $organizers_parameters,
    ];

    $fid = $settings->get('default_image');
    $form['default_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Default image'),
      '#description' => $this->t('Used for Events and Places when no image is provided or copying of an image fails.'),
      '#upload_location' => file_default_scheme() . '://' . UITDATABANK_IMAGE_DIRECTORY,
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

    \Drupal::configFactory()
      ->getEditable('uitdatabank.settings')
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('events_parameters', $form_state->getValue('events_parameters'))
      ->set('places_parameters', $form_state->getValue('places_parameters'))
      ->set('organizers_parameters', $form_state->getValue('organizers_parameters'))
      ->set('default_image', reset($form_state->getValue('default_image')))
      ->save();

    // @todo Check if we need to update media entities referencing the old fid
    // if fid has changed.

    $message = $this->t('UiTdatabank settings saved.');
    drupal_set_message($message);
  }

}
