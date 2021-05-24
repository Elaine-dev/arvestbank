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
 *   id = "dam_lookup"
 * )
 *
 */
class DamLookup extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $database = \Drupal::database();

    $queryString = "
      SELECT d.field_acquiadam_asset_id_value FROM media_field_data m
      INNER JOIN media_revision__field_acquiadam_asset_id d
      ON (m.vid = d.revision_id)
      WHERE m.name LIKE '%" . $value . "%'
      and m.bundle = 'acquia_dam_image'
    ";

    $query = $database->query($queryString);
    $result = $query->fetchAll();

    if (!empty($result[0]->field_acquiadam_asset_id_value)) {
      return $result[0]->field_acquiadam_asset_id_value;
    }
    else {
      $migrate_executable->saveMessage("NO ASSET MATCH: " . $value);
      return FALSE;
    }

  }

}
