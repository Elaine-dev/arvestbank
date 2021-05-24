<?php

namespace Drupal\arvestbank_migrate\Plugin\migrate\process;

use Drupal\migrate\Annotation\MigrateProcessPlugin;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "geolocation"
 * )
 *
 */
class Geolocation extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $lat = $row->get('lat');
    $lng = $row->get('lon');

    if ($lat && $lng) {
      return [
        'lat' => $lat,
        'lng' => $lng,
      ];
    }

  }

}
