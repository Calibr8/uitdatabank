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
    $default_fid = \Drupal::config('uitdatabank.settings.defaults')->get('image');
    $default_image = [
      '@id' => '',
      'copyrightHolder' => '',
      'description' => '',
      'file' => [
        'target_id' => $default_fid,
        'alt' => '',
      ],
    ];


    if ($value) {
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

          // Download can fail, catch this.
          try {
            $final_destination = parent::transform($params, $migrate_executable, $row, $destination_property);
          }
          catch (\Exception $e) {
            $final_destination = NULL;
          }

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
          else {
            // Only add default for first image.
            if (!$index) {
              $fid = $default_fid;
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
    }
    else {
      $value[] = $default_image;
    }

    return $value;
  }

}
