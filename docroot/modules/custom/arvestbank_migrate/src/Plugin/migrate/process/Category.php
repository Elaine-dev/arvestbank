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
 *   id = "category"
 * )
 *
 */
class Category extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $return = [];

    $cat_ar = [
      'art' => 686, // Abstract Art
      'agri' => 691, // Agriculture
      'org' => 696, // Charities &amp; Orgs
      'college' => 701, // College
      'dest' => 706, // Destinations
      'fashion' => 711, // Fashion
      'food' => 716, // Food
      'outdoor' => 721, // The Great Outdoors
      'school' => 726, // High Schools
      'usa' => 731, // Military &amp; Patriotic
      'pets' => 736, // Pets
      'public' => 741, // Public Services
      'sports' => 746, // Sports
      'whimsical' => 751, // Whimsical
    ];

    foreach (explode(',', $value) as $cat) {
      $return[] = $cat_ar[trim($cat)];
    }

    return $return;

  }

}
