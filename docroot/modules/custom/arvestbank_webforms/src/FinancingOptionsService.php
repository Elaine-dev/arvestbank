<?php

namespace Drupal\arvestbank_webforms;

/**
 * Class FinancingOptionsService.
 */
class FinancingOptionsService {

  /**
   * Constructs a new FinancingOptionsService object.
   */
  public function financingOptions($key) {

    // Format of this mapping:
    // loan_type_1|loan_home_imp_type|loan_secure|loan_veh_type|loan_secure_home
    // (fieldnames from the webform).
    $financing_options_mapping = [
      'vehicle||vehicle|car_truck|' => 'auto|heloc|cd_install',
      'vehicle||vehicle|motorcycle|' => 'motorcycle|heloc|cd_install',
      'vehicle||vehicle|atv_utv|' => 'atv_utv|heloc|cd_install',
      'vehicle||vehicle|rv|' => 'rv|heloc|cd_install',
      'vehicle||home_re||all_once' => 'he_loan|he_arm',
      'vehicle||home_re||over_time' => 'heloc|he_loan|he_arm',
      'vehicle||cash_acct_sec||all_once' => 'cd_install|he_loan',
      'vehicle||cash_acct_sec||over_time' => 'cd_loc|heloc|ploc',
      'vehicle||unsecured||all_once' => 'unsecured|auto|ploc',
      'vehicle||unsecured||over_time' => 'ploc|heloc|cd_loc',
      'vehicle||marine_boat||' => 'marine_boat|auto|rv',
      'home_imp|home_add|vehicle|car_truck|' => 'auto|marine_boat|cd_install',
      'home_imp|home_add|vehicle|motorcycle|' => 'motorcycle|marine_boat|cd_install',
      'home_imp|home_add|vehicle|atv_utv|' => 'atv_utv|marine_boat|cd_install',
      'home_imp|home_add|vehicle|rv|' => 'rv|marine_boat|cd_install',
      'home_imp|home_add|home_re||all_once' => 'he_loan|he_arm',
      'home_imp|home_add|home_re||over_time' => 'heloc|he_loan|he_arm',
      'home_imp|home_add|cash_acct_sec||all_once' => 'cd_install|he_loan',
      'home_imp|home_add|cash_acct_sec||over_time' => 'cd_loc|heloc',
      'home_imp|home_add|unsecured||all_once' => 'unsecured_home_imp',
      'home_imp|home_add|unsecured||over_time' => 'ploc|unsecured|cd_loc',
      'home_imp|home_add|marine_boat||' => 'marine_boat|auto|rv',
      'home_imp|hvac_oth|vehicle|car_truck|' => 'auto|marine_boat|cd_install',
      'home_imp|hvac_oth|vehicle|motorcycle|' => 'motorcycle|marine_boat|cd_install',
      'home_imp|hvac_oth|vehicle|atv_utv|' => 'atv_utv|marine_boat|cd_install',
      'home_imp|hvac_oth|vehicle|rv|' => 'rv|marine_boat|cd_install',
      'home_imp|hvac_oth|home_re||all_once' => 'he_loan|he_arm',
      'home_imp|hvac_oth|home_re||over_time' => 'heloc|he_loan|he_arm',
      'home_imp|hvac_oth|cash_acct_sec||all_once' => 'cd_install|he_loan',
      'home_imp|hvac_oth|cash_acct_sec||over_time' => 'cd_loc|heloc',
      'home_imp|hvac_oth|unsecured||all_once' => 'unsecured_home_imp',
      'home_imp|hvac_oth|unsecured||over_time' => 'ploc|unsecured|cd_loc',
      'home_imp|hvac_oth|marine_boat||' => 'marine_boat|auto|rv',
      'home_imp|appliance|vehicle|car_truck|' => 'auto|marine_boat|cd_install',
      'home_imp|appliance|vehicle|motorcycle|' => 'motorcycle|marine_boat|cd_install',
      'home_imp|appliance|vehicle|atv_utv|' => 'atv_utv|marine_boat|cd_install',
      'home_imp|appliance|vehicle|rv|' => 'rv|marine_boat|cd_install',
      'home_imp|appliance|home_re||all_once' => 'he_loan|he_arm',
      'home_imp|appliance|home_re||over_time' => 'heloc|he_loan|he_arm',
      'home_imp|appliance|cash_acct_sec||all_once' => 'cd_install|he_loan',
      'home_imp|appliance|cash_acct_sec||over_time' => 'cd_loc|heloc',
      'home_imp|appliance|unsecured||all_once' => 'unsecured',
      'home_imp|appliance|unsecured||over_time' => 'ploc|unsecured|cd_loc',
      'home_imp|appliance|marine_boat||' => 'marine_boat|auto|rv',
      'home_imp|spa_pool|vehicle|car_truck|' => 'auto|marine_boat|cd_install',
      'home_imp|spa_pool|vehicle|motorcycle|' => 'motorcycle|marine_boat|cd_install',
      'home_imp|spa_pool|vehicle|atv_utv|' => 'atv_utv|marine_boat|cd_install',
      'home_imp|spa_pool|vehicle|rv|' => 'rv|marine_boat|cd_install',
      'home_imp|spa_pool|home_re||all_once' => 'he_loan|he_arm',
      'home_imp|spa_pool|home_re||over_time' => 'heloc|he_loan|he_arm',
      'home_imp|spa_pool|cash_acct_sec||all_once' => 'cd_install|he_loan',
      'home_imp|spa_pool|cash_acct_sec||over_time' => 'cd_loc|heloc',
      'home_imp|spa_pool|unsecured||all_once' => 'unsecured_home_imp',
      'home_imp|spa_pool|unsecured||over_time' => 'ploc|unsecured|cd_loc',
      'home_imp|spa_pool|marine_boat||' => 'marine_boat|auto|rv',
    ];

    if (array_key_exists($key, $financing_options_mapping)) {
      $return = $financing_options_mapping[$key];
    }
    else {
      $return = [];
    }

    return $return;

  }

}
