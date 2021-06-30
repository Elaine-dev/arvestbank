<?php

namespace Drupal\arvestbank_branch_locations\Services;

/**
 * Provides Legacy Location Information.
 */
class LegacyLocationInformation {

  /**
   * Copied from old site, contains "region ids", needed for form API.
   *
   * @var array
   */
  public $legacyLocationInformation = [
    'bartlesville' => [
      'name' => 'Arvest Bank - Bartlesville',
      'regionId'    => 500,
      'bankId'    => 501,
      'state'        => 'OK',
    ],
    'bentonville'    => [
      'name' => 'Arvest Bank - Benton County',
      'regionId'    => 101,
      'bankId'    => 101,
      'state'        => 'AR',
    ],
    'fayetteville'    => [
      'name' => 'Arvest Bank - Fayetteville',
      'regionId'    => 301,
      'bankId'    => 301,
      'state'        => 'AR',
    ],
    'fortsmith'        => [
      'name' => 'Arvest Bank - Fort Smith',
      'regionId'    => 401,
      'bankId'    => 401,
      'state'        => 'AR',
    ],
    'joplin'        => [
      'name' => 'Arvest Bank - Joplin',
      'regionId'    => 750,
      'bankId'    => 750,
      'state'        => 'MO',
    ],
    'kansascity'    => [
      'name' => 'Arvest Bank - Kansas City',
      'regionId'    => 950,
      'bankId'    => 951,
      'state'        => 'KS',
    ],
    'lawton'        => [
      'name' => 'Arvest Bank - Lawton',
      'regionId'    => 801,
      'bankId'    => 801,
      'state'        => 'OK',
    ],
    'littlerock'    => [
      'name' => 'Arvest Bank - Little Rock',
      'regionId'    => 451,
      'bankId'    => 450,
      'state'        => 'AR',
    ],
    'oklahomacity'    => [
      'name' => 'Arvest Bank - Oklahoma City',
      'regionId'    => 601,
      'bankId'    => 601,
      'state'        => 'OK',
    ],
    'prairiegrove'    => [
      'name' => 'Arvest Bank - Prairie Grove',
      'regionId'    => 301,
      'bankId'    => 301,
      'state'        => 'AR',
    ],
    'shawnee'        => [
      'name' => 'Arvest Bank - Shawnee',
      'regionId'    => 601,
      'bankId'    => 601,
      'state'        => 'OK',
    ],
    'siloamsprings' => [
      'name' => 'Arvest Bank - Siloam Springs',
      'regionId'    => 201,
      'bankId'    => 201,
      'state'        => 'AR',
    ],
    'springdale'    => [
      'name' => 'Arvest Bank - Springdale',
      'regionId'    => 251,
      'bankId'    => 251,
      'state'        => 'AR',
    ],
    'springfield'    => [
      'name' => 'Arvest Bank - Springfield',
      'regionId'    => 900,
      'bankId'    => 901,
      'state'        => 'MO',
    ],
    'tulsa'            => [
      'name' => 'Arvest Bank - Tulsa',
      'regionId'    => 551,
      'bankId'    => 551,
      'state'        => 'OK',
    ],
    'yellville'        => [
      'name' => 'Arvest Bank - Yellville',
      'regionId'    => 850,
      'bankId'    => 850,
      'state'        => 'AR',
    ],
  ];

  /**
   * Getting city from location Term.
   */
  public function getCityFromLocationTerm($locationTerm) {

    // Get field_private_bank_location_link value.
    $title = $locationTerm->getName();

    // Find and return city.
    preg_match_all('/^[A-Z]{2} - (.*)$/', $title, $matches);
    if (isset($matches[1][0])) {
      return $matches[1][0];
    }

    return NULL;

  }

  /**
   * Gets a legacy region id from a city name.
   *
   * @param string $cityName
   *   The city name as pulled from location nodes.
   *
   * @return bool|string
   *   Returns the regionId if found, or false if not found.
   */
  public function getRegionIdFromCityName($cityName) {

    // Get city name in the legacy city name format.
    $formattedCityName = str_replace(' ', '', strtolower($cityName));

    // If we have a legacy region id for the given city name.
    if (isset($this->legacyLocationInformation[$formattedCityName]['regionId'])) {
      // Return the region id.
      return $this->legacyLocationInformation[$formattedCityName]['regionId'];
    }
    // If we don't have a record return FALSE.
    else {
      return FALSE;
    }

  }

}
