<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
/**
 * Process UiTdatabank sub event data.
 *
 * Based on \Drupal\migrate\Plugin\migrate\process\UitdatabankFormatDate, but
 * handles both start and end datetime.
 *
 * Example usage for datetime fields with a timezone and settings:
 * @code
 * process:
 *   field_time:
 *     plugin: uitdatabank_sub_event
 *     from_format: 'Y-m-d\TH:i:sO'
 *     to_format: 'Y-m-d\TH:i:s'
 *     timezone: 'UTC'
 *     settings:
 *       validate_format: false
 *     source: event_time
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_sub_event",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankSubEvent extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    // Validate the configuration.
    if (empty($this->configuration['from_format'])) {
      throw new MigrateException('Format date plugin is missing from_format configuration.');
    }
    if (empty($this->configuration['to_format'])) {
      throw new MigrateException('Format date plugin is missing to_format configuration.');
    }

    $fromFormat = $this->configuration['from_format'];
    $toFormat = $this->configuration['to_format'];
    $timezone = isset($this->configuration['timezone']) ? $this->configuration['timezone'] : NULL;
    $settings = isset($this->configuration['settings']) ? $this->configuration['settings'] : [];

    $transformed = [];
    foreach ($value as $index => $item) {

      // Attempts to transform the supplied date using the defined input format.
      // DateTimePlus::createFromFormat can throw exceptions, so we need to
      // explicitly check for problems.
      try {
        $start = DateTimePlus::createFromFormat($fromFormat, $item['startDate'], $timezone, $settings);
        $end = DateTimePlus::createFromFormat($fromFormat, $item['endDate'], $timezone, $settings);

        // Force timezone.
        $datetimeplus = new DateTimePlus('now', $timezone, $settings);
        $start->setTimezone($datetimeplus->getTimezone());
        $start = $start->format($toFormat);

        $end->setTimezone($datetimeplus->getTimezone());
        $end = $end->format($toFormat);

        $transformed[] = [
          'value' => $start,
          'end_value' => $end,
        ];

      }
      catch (\InvalidArgumentException $e) {
        throw new MigrateException(sprintf('Format date plugin could not transform "%s" using the format "%s". Error: %s', $value, $fromFormat, $e->getMessage()), $e->getCode(), $e);
      }
      catch (\UnexpectedValueException $e) {
        throw new MigrateException(sprintf('Format date plugin could not transform "%s" using the format "%s". Error: %s', $value, $fromFormat, $e->getMessage()), $e->getCode(), $e);
      }
    }

    return $transformed;
  }

}
