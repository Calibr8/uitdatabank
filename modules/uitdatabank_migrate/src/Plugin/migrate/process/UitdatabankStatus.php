<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Determine published status based on unpublish date.
 *
 * This additional check is required, because unpublish dates in the past will
 * be ignored on entity creation, which will result in incorrect status.
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_status",
 * )
 */
class UitdatabankStatus extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $now = \Drupal::time()->getCurrentTime();
    $status = (int) ($value > $now);

    /*
     * Items with workflowstatus REJECTED or DELETED must be kept unpublished.
     * @see http://documentatie.uitdatabank.be/content/uitdatabank/latest/werking-uitdatabank/#items-met-de-status-approved-of-rejected
     */
    if ($status) {
      $workflow_status = $row->getSourceProperty('workflow_status');
      $status = (int) !in_array($workflow_status, ['REJECTED', 'DELETED']);
    }

    // For unpublished state, clear scheduler fields to avoid interference with
    // status.
    if (!$status) {
      $row->setDestinationProperty('publish_on', NULL);
      $row->setDestinationProperty('unpublish_on', NULL);
    }

    return $status;
  }

}
