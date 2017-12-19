<?php

namespace Drupal\uitdatabank\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'uitdatabank_price_info_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "uitdatabank_price_info_formatter",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "uitdatabank_price_info"
 *   }
 * )
 */
class UitdatabankPriceInfoFormatter extends FormatterBase {

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
   * @todo: see if can use a template file for this.
   */
  protected function viewValue(FieldItemInterface $item) {
    $markup = <<<MARKUP
      <span class="price price--category-%s">
        <span class="price--name">%s</span>
        <span class="price--currency">%s</span>
        <span class="price--value">%s</span>
      </span>
MARKUP;

    $category = Html::cleanCssIdentifier($item->category);
    $name = Html::escape($item->name);
    $price = Html::escape($item->price);
    $currency = Html::escape($item->price_currency);

    switch ($currency) {
      case 'EUR':
        $currency = '&euro;';
    }

    return sprintf($markup, $category, $name, $currency, $price);
  }

}
