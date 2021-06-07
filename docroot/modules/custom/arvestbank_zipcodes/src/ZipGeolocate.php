<?php

namespace Drupal\arvestbank_zipcodes;

/**
 * Class ZipGeolocate services for zip to lat/lng mapping.
 */
class ZipGeolocate {

  /**
   * Initializes the zip code table.
   *
   * @return int
   *   Number of records inserted.
   */
  public function zipInit() : int {

    // Initialize the return count.
    $return = 0;

    // Database connection.
    $database = \Drupal::database();

    // Delete all records to start fresh.
    $database->delete('zipcodes')->execute();

    // This CSV contains the zip code data.
    if (($handle = fopen("modules/custom/arvestbank_zipcodes/data/zipcodes.csv", "r")) !== FALSE) {

      // First row has headers.
      $headers = TRUE;

      // Loop through the CSV adding records.
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($headers) {
          $headers = FALSE;
          continue;
        }
        $zip = str_pad($data[0], 5, 0, STR_PAD_LEFT);
        $lat = $data[5];
        $lng = $data[6];
        if (!empty($zip) && !empty($lat) && !empty($lng)) {
          $result = $database->insert('zipcodes')
            ->fields([
              'zip' => $zip,
              'lat' => $lat,
              'lng' => $lng,
            ])
            ->execute();
          if ($result) {
            $return++;
          }
        }
      }
      fclose($handle);
    }

    // Send back the count of records.
    return $return;

  }

  /**
   * Looks up lat/lon with zip code as input.
   *
   * @param string $zip
   *   Zip code.
   *
   * @return array
   *   Lat/Lon.
   */
  public function zipCoords(string $zip) : array {

    // Database connection.
    $database = \Drupal::database();

    $result = $database
      ->select('{zipcodes}', 'z')
      ->fields('z', ['lat', 'lng'])
      ->condition('zip', $zip, '=')
      ->execute();

    $zip_result = $result->fetchAll();

    if (!empty($zip_result[0])) {
      $return = [
        'lat' => $zip_result[0]->lat,
        'lng' => $zip_result[0]->lng,
      ];
    }
    else {
      $return = [];
    }

    return $return;

  }

}
