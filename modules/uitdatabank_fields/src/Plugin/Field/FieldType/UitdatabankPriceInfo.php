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
 * Plugin implementation of the 'uitdatabank_price_info' field type.
 *
 * @FieldType(
 *   id = "uitdatabank_price_info",
 *   label = @Translation("UiTdatabank Price Info"),
 *   description = @Translation("Uitdatabank Price Info"),
 *   default_widget = "uitdatabank_price_info_widget",
 *   default_formatter = "uitdatabank_price_info_formatter"
 * )
 */
class UitdatabankPriceInfo extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['category'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Category'));
    $properties['name'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Name'));
    $properties['price'] = DataDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Price'));
    $properties['price_currency'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Price Currency'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'category' => [
          'type' => 'varchar',
          'default' => '',
          'length' => 255,
        ],
        'name' => [
          'type' => 'varchar',
          'default' => '',
          'length' => 255,
        ],
        'price' => [
          'type' => 'float',
          'size' => 'normal',
        ],
        'price_currency' => [
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

    $values['category'] = 'tariff';
    $values['name'] = 'Tariff';
    $values['price'] = mt_rand(1, 100);
    $values['price_currency'] = 'EUR';

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $price = $this->get('price')->getValue();
    $price_currency = $this->get('price_currency')->getValue();

    return ($price === NULL || $price === '')
      && ($price_currency === NULL || $price_currency === '');
  }

}
