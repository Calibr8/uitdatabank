<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;


/**
 * Converts date/datetime from one format to another.
 *
 * Based on \Drupal\migrate\Plugin\migrate\process\FormatDate, but with actual
 * timezone conversion.
 *
 * If original datetime contains timezone indication, no timezone
 * conversion will take place in \Drupal\Component\Datetime\DateTimePlus and
 * DateTime (http://php.net/manual/en/datetime.createfromformat.php).
 *
 * Available configuration keys
 * - from_format: The source format string as accepted by
 *   @link http://php.net/manual/datetime.createfromformat.php \DateTime::createFromFormat. @endlink
 * - to_format: The destination format.
 * - timezone: String identifying the required time zone, see
 *   DateTimePlus::__construct().
 * - settings: keyed array of settings, see DateTimePlus::__construct().
 *
 * Example usage for datetime fields with a timezone and settings:
 * @code
 * process:
 *   field_time:
 *     plugin: uitdatabank_format_date
 *     from_format: 'Y-m-d\TH:i:sO'
 *     to_format: 'Y-m-d\TH:i:s'
 *     timezone: 'UTC'
 *     settings:
 *       validate_format: false
 *     source: event_time
 * @endcode
 *
 * If the source value was '2004-12-19T10:19:42+02:00' the transformed value
 * would be 2004-12-19T08:19:42.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_format_date"
 * )
 */
class UitdatabankFormatDate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if (empty($value)) {
      return '';
    }

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

    // Attempts to transform the supplied date using the defined input format.
    // DateTimePlus::createFromFormat can throw exceptions, so we need to
    // explicitly check for problems.
    $transformed = '';
    try {
      /** @var \Drupal\Component\Datetime\DateTimePlus $transformed */
      $transformed = DateTimePlus::createFromFormat($fromFormat, $value, $timezone, $settings);
    }
    catch (\InvalidArgumentException $e) {
      try {

        // Fallback, as API doesn't always return the default format.
        // @todo: remove when API has been fixed.
        $transformed = new DateTimePlus($value, $timezone, $settings);
      }
      catch (\UnexpectedValueException $e) {
        throw new MigrateException(sprintf('Format date plugin could not transform "%s" using the format "%s" or Zulu time. Error: %s', $value, $fromFormat, $e->getMessage()), $e->getCode(), $e);
      }
    }
    catch (\UnexpectedValueException $e) {
      throw new MigrateException(sprintf('Format date plugin could not transform "%s" using the format "%s". Error: %s', $value, $fromFormat, $e->getMessage()), $e->getCode(), $e);
    }

    if ($transformed) {
      // Force timezone.
      /** @var \Drupal\Component\Datetime\DateTimePlus $datetimeplus */
      $datetimeplus = new DateTimePlus('now', $timezone, $settings);
      $transformed->setTimezone($datetimeplus->getTimezone());

      // Catch year 2038 problem as availableTo dates can have values up to 2100.
      // @see https://en.wikipedia.org/wiki/Year_2038_problem.
      if ($transformed->getTimestamp() > 2147483647) {
        $transformed->setTimestamp(2147483647);
      }

      $transformed = $transformed->format($toFormat);
    }

    return $transformed;
  }

}
