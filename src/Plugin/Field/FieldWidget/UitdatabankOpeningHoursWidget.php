<?php

namespace Drupal\uitdatabank\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'uitdatabank_opening_hours_widget' widget.
 *
 * @FieldWidget(
 *   id = "uitdatabank_opening_hours_widget",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "uitdatabank_opening_hours"
 *   }
 * )
 */
class UitdatabankOpeningHoursWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $field_items = [];

    $field_items['opens'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opening time'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->opens) ? $items[$delta]->opens : NULL,
      '#size' => 5,
      '#maxlength' => 5,
      '#description' => $this->t('E.g.: 08:00'),
    ] + $element;

    $field_items['closes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Closing time'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->closes) ? $items[$delta]->closes : NULL,
      '#size' => 5,
      '#maxlength' => 5,
      '#description' => $this->t('E.g.: 18:00'),
    ] + $element;

    $field_items['days_of_week'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Days of week'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->days_of_week) ? $items[$delta]->days_of_week : NULL,
      '#size' => 60,
      '#maxlength' => 255,
      '#description' => $this->t('E.g.: monday,tuesday'),
    ] + $element;

    return $field_items;
  }

}
