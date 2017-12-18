<?php

namespace Drupal\uitdatabank\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'uitdatabank_opening_hours_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "uitdatabank_opening_hours_formatter",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "uitdatabank_opening_hours"
 *   }
 * )
 */
class UitdatabankOpeningHoursFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   *
   * @todo: use a template file for this.
   */
  protected function viewValue(FieldItemInterface $item) {
    $markup = <<<MARKUP
      <span class="opening-hours">
        <span class="opening-hours--opens">%s</span>
        <span class="opening-hours--closed">%s</span>
        <span class="opening-hours--days-of-week">%s</span>
      </span>
MARKUP;

    $opens = Html::escape($item->opens);
    $closes = Html::escape($item->closes);
    $days_of_week = Html::escape($item->days_of_week);

    return sprintf($markup, $opens, $closes, $days_of_week);
  }

}
