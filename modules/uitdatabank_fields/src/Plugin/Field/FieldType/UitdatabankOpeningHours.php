<?php

namespace Drupal\uitdatabank_fields\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'uitdatabank_opening_hours' field type.
 *
 * @FieldType(
 *   id = "uitdatabank_opening_hours",
 *   label = @Translation("UiTdatabank Opening Hours"),
 *   description = @Translation("UiTdatabank Opening Hours"),
 *   default_widget = "uitdatabank_opening_hours_widget",
 *   default_formatter = "uitdatabank_opening_hours_formatter"
 * )
 */
class UitdatabankOpeningHours extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['opens'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Opens'));
    $properties['closes'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Closes'));
    $properties['days_of_week'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Days of week'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'opens' => [
          'type' => 'varchar',
          'default' => '',
          'length' => 5,
        ],
        'closes' => [
          'type' => 'varchar',
          'default' => '',
          'length' => 5,
        ],
        'days_of_week' => [
          'type' => 'varchar',
          'default' => '',
          'length' => 255,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values = [];

    $values['opens'] = '08:00';
    $values['closes'] = '21:00';
    $values['days_of_week'] = 'monday,tuesday,wednesday';

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $opens = $this->get('opens')->getValue();
    $closes = $this->get('closes')->getValue();
    $days_of_week = $this->get('days_of_week')->getValue();

    return ($opens === NULL || $opens === '')
      && ($closes === NULL || $closes === '')
      && ($days_of_week === NULL || $days_of_week === '');
  }

}
