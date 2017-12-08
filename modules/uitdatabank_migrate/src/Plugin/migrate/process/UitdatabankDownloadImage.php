<?php

namespace Drupal\uitdatabank_migrate\Plugin\migrate\process;

use Drupal\file\Entity\File;
use Drupal\migrate\Plugin\migrate\process\Download;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Downloads a file from a HTTP(S) remote location into the local file system.
 *
 * @code
 * process:
 *   media:
 *     plugin: uitdatabank_download_image
 *     path: 'public://uitdatabank/media/'
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "uitdatabank_download_image",
 *   handle_multiples = TRUE
 * )
 */
class UitdatabankDownloadImage extends Download {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    foreach ($value as $index => $item) {
      $item['file'] = NULL;

      $url_parts = explode('/', $item['contentUrl']);
      $file_name = array_pop($url_parts);
      $file_uri = $this->configuration['path'] . $file_name;

      // Check if image is already present as managed file.
      $query = \Drupal::entityQuery('file');
      $query->condition('uri', $file_uri);
      $result = $query->execute();

      $fid = NULL;
      if ($result) {
        $fid = reset($result);
      }
      else {
        $params = [
          $item['contentUrl'],
          $file_uri,
        ];

        $final_destination = parent::transform($params, $migrate_executable, $row, $destination_property);

        if ($final_destination) {
          $file = File::create([
            'uid' => 1,
            'filename' => $file_name,
            'uri' => $final_destination,
            'status' => 1,
          ]);
          $file->save();

          if ($file->id()) {
            $fid = $file->id();
          }
        }
      }

      if ($fid) {
        $item['file'] = [
          'target_id' => $fid,
          'alt' => $item['description'],
        ];
      }

      $value[$index] = $item;
    }

    return $value;
  }

}
