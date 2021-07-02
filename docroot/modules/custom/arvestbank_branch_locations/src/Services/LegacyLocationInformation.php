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
   * These mappings were provided in Asana by old web team, orig source unknown.
   *
   * @var array
   */
  public $regionalCapitalCity = [
    'Alma' => 'fortsmith',
    'Anderson' => 'bentonville',
    'Ashdown' => 'littlerock',
    'Aurora' => 'springfield',
    'Bartlesville' => 'bartlesville',
    'Bella Vista' => 'bentonville',
    'Belton' => 'kansascity',
    'Benton' => 'littlerock',
    'Bentonville' => 'bentonville',
    'Berryville' => 'bentonville',
    'Branson' => 'springfield',
    'Branson West' => 'springfield',
    'Broken Arrow' => 'tulsa',
    'Broken Bow' => 'fortsmith',
    'Bryant' => 'littlerock',
    'Cabot' => 'littlerock',
    'Caney' => 'bartlesville',
    'Carthage' => 'joplin',
    'Cassville' => 'bentonville',
    'Catoosa' => 'tulsa',
    'Centerton' => 'bentonville',
    'Chickasha' => 'lawton',
    'Choctaw' => 'oklahomacity',
    'Clarksville' => 'fortsmith',
    'Conway' => 'littlerock',
    'Coweta' => 'tulsa',
    'De Queen' => 'fortsmith',
    'Del City' => 'oklahomacity',
    'Dewey' => 'bartlesville',
    'Dierks' => 'littlerock',
    'Duncan' => 'lawton',
    'Edmond' => 'oklahomacity',
    'Elgin' => 'lawton',
    'Elkins' => 'fayetteville',
    'Eufaula' => 'tulsa',
    'Eureka Springs' => 'bentonville',
    'Farmington' => 'fayetteville',
    'Fayetteville' => 'fayetteville',
    'Flippin' => 'yellville',
    'Fort Smith' => 'fortsmith',
    'Gardner' => 'kansascity',
    'Gentry' => 'siloamsprings',
    'Gladstone' => 'kansascity',
    'Glenwood' => 'littlerock',
    'Gravette' => 'bentonville',
    'Greenwood' => 'fortsmith',
    'Grove' => 'siloamsprings',
    'Harrison' => 'yellville',
    'Hollister' => 'springfield',
    'Hot Springs' => 'littlerock',
    'Hot Springs Village' => 'littlerock',
    'Huntsville' => 'springdale',
    'Idabel' => 'fortsmith',
    'Independence' => 'kansascity',
    'Jacksonville' => 'littlerock',
    'Jay' => 'siloamsprings',
    'Jenks' => 'tulsa',
    'Jonesboro' => 'littlerock',
    'Joplin' => 'joplin',
    'Kansas' => 'siloamsprings',
    'Kansas City' => 'kansascity',
    'Kimberling City' => 'springfield',
    'Lamar' => 'joplin',
    'Lawton' => 'lawton',
    'Lead Hill' => 'yellville',
    'Leawood' => 'kansascity',
    'Lebanon' => 'springfield',
    'Lee\'s Summit' => 'kansascity',
    'Lenexa' => 'kansascity',
    'Lincoln' => 'fayetteville',
    'Little Rock' => 'littlerock',
    'Lockwood' => 'joplin',
    'Lonoke' => 'littlerock',
    'Lowell' => 'bentonville',
    'Manila' => 'littlerock',
    'Marshfield' => 'springfield',
    'McAlester' => 'tulsa',
    'Mena' => 'fortsmith',
    'Miami' => 'joplin',
    'Midwest City' => 'oklahomacity',
    'Mission' => 'kansascity',
    'Monett' => 'joplin',
    'Monette' => 'littlerock',
    'Moore' => 'oklahomacity',
    'Morrilton' => 'littlerock',
    'Mount Ida' => 'littlerock',
    'Mountain Grove' => 'springfield',
    'Mountain Home' => 'yellville',
    'Muskogee' => 'tulsa',
    'Nashville' => 'littlerock',
    'Neosho' => 'joplin',
    'Nevada' => 'joplin',
    'Nixa' => 'springfield',
    'Noel' => 'bentonville',
    'Norman' => 'oklahomacity',
    'North Little Rock' => 'littlerock',
    'Nowata' => 'bartlesville',
    'Oklahoma City' => 'oklahomacity',
    'Okmulgee' => 'tulsa',
    'Olathe' => 'kansascity',
    'Ottawa' => 'kansascity',
    'Overland Park' => 'kansascity',
    'Owasso' => 'tulsa',
    'Paris' => 'fortsmith',
    'Pea Ridge' => 'bentonville',
    'Pittsburg' => 'joplin',
    'Poteau' => 'fortsmith',
    'Prairie Grove' => 'fayetteville',
    'Pryor' => 'tulsa',
    'Rogers' => 'bentonville',
    'Russellville' => 'fortsmith',
    'Sallisaw' => 'fortsmith',
    'Sand Springs' => 'tulsa',
    'Sapulpa' => 'tulsa',
    'Shawnee' => 'oklahomacity',
    'Shell Knob' => 'bentonville',
    'Sherwood' => 'littlerock',
    'Siloam Springs' => 'siloamsprings',
    'Springdale' => 'springdale',
    'Springfield' => 'springfield',
    'Stillwater' => 'oklahomacity',
    'Stilwell' => 'siloamsprings',
    'Tahlequah' => 'tulsa',
    'Tulsa' => 'tulsa',
    'Van Buren' => 'fortsmith',
    'Vinita' => 'bartlesville',
    'Wagoner' => 'tulsa',
    'Waldron' => 'fortsmith',
    'Walters' => 'lawton',
    'Webb City' => 'joplin',
    'West Fork' => 'fayetteville',
    'West Plains' => 'springfield',
    'Westville' => 'siloamsprings',
    'Yellville' => 'yellville',
    'Yukon' => 'oklahomacity',
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
    // If we don't have a region id for this city.
    else {
      // If we have this city's regional capital city with a region id.
      if (
        isset($this->regionalCapitalCity[$cityName])
        && isset($this->legacyLocationInformation[$this->regionalCapitalCity[$cityName]]['regionId'])
      ) {
        // Return the regional capital's region id.
        return $this->legacyLocationInformation[$this->regionalCapitalCity[$cityName]]['regionId'];
      }

    }

    // Return false if we didn't find a region id.
    return FALSE;

  }

}
