<?php

namespace Drupal\uitdatabank_fields\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'uitdatabank_price_info_widget' widget.
 *
 * @FieldWidget(
 *   id = "uitdatabank_price_info_widget",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "uitdatabank_price_info"
 *   }
 * )
 */
class UitdatabankPriceInfoWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $field_items = [];

    $field_items['category'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Price category'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->category) ? $items[$delta]->category : NULL,
      '#size' => 60,
      '#maxlength' => 255,
      '#description' => $this->t('E.g.: "base", "tariff"'),
    ] + $element;

    $field_items['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Price name'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->name) ? $items[$delta]->name : NULL,
      '#size' => 60,
      '#maxlength' => 255,
      '#description' => $this->t('E.g.: "Base", "Reduction"'),
    ] + $element;

    $field_items['price'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Price'),
        '#title_display' => 'before',
        '#default_value' => isset($items[$delta]->price) ? $items[$delta]->price : NULL,
        '#size' => 10,
        '#maxlength' => 10,
      ] + $element;

    $field_items['price_currency'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Price currency'),
        '#title_display' => 'before',
        '#default_value' => isset($items[$delta]->price_currency) ? $items[$delta]->price_currency : NULL,
        '#size' => 10,
        '#maxlength' => 10,
        '#description' => $this->t('E.g.: "EUR"'),
      ] + $element;

    return $field_items;
  }

}
