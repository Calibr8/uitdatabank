<?php

namespace Drupal\uitdatabank_fields\Plugin\Field\FieldFormatter;

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
   *
   * @todo: see if can use a template file for this.
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $markup_wrapper = '<div class="opening-hours--wrapper">%s</div>';
    $markup_row = '<div class="opening-hours--day opening-hours--day-%s"><div class="opening-hours--day-name">%s</div><div class="opening-hours--day-hours">%s</div></div>';
    $markup_times = '%s - %s';

    $days_of_week = [
      'monday' => [],
      'tuesday' => [],
      'wednesday' => [],
      'thursday' => [],
      'friday' => [],
      'saturday' => [],
      'sunday' => [],
    ];

    foreach ($items as $delta => $item) {
      $days = explode(',', $item->days_of_week);
      foreach ($days as $day) {
        $days_of_week[trim($day)][] = sprintf($markup_times, $item->opens, $item->closes);
      }
    }

    $rows = [];
    foreach ($days_of_week as $name => $day) {
      if (empty($day)) {
        continue;
      }
      $rows[] = sprintf($markup_row, Html::cleanCssIdentifier($name), $this->t(ucfirst($name)), implode(' / ', $day));
    }

    return [['#markup' => sprintf($markup_wrapper, implode('', $rows))]];
  }

}
