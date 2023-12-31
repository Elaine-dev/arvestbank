<?php

namespace Drupal\arvestbank_webforms;

/**
 * Class FinancingOptionsService.
 */
class FinancingOptionsService {

  /**
   * Map of financing options.
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
      'debt_consol||vehicle|car_truck|' => 'auto|heloc|cd_install',
      'debt_consol||vehicle|motorcycle|' => 'motorcycle|heloc|cd_install',
      'debt_consol||vehicle|atv_utv|' => 'atv_utv|heloc|cd_install',
      'debt_consol||vehicle|rv|' => 'rv|heloc|cd_install',
      'debt_consol||home_re||all_once' => 'he_loan|he_arm',
      'debt_consol||home_re||over_time' => 'heloc|he_loan|he_arm',
      'debt_consol||cash_acct_sec||all_once' => 'cd_install|he_loan',
      'debt_consol||cash_acct_sec||over_time' => 'cd_loc|heloc',
      'debt_consol||unsecured||all_once' => 'unsecured|heloc|ploc',
      'debt_consol||unsecured||over_time' => 'ploc|auto',
      'debt_consol||marine_boat||' => 'marine_boat|heloc|cd_loc',
      'marine_boat||vehicle|car_truck|' => 'auto|heloc|cd_install',
      'marine_boat||vehicle|motorcycle|' => 'motorcycle|heloc|cd_install',
      'marine_boat||vehicle|atv_utv|' => 'atv_utv|heloc|cd_install',
      'marine_boat||vehicle|rv|' => 'rv|heloc|cd_install',
      'marine_boat||home_re||all_once' => 'he_loan|he_arm',
      'marine_boat||home_re||over_time' => 'heloc|he_loan|he_arm',
      'marine_boat||cash_acct_sec||all_once' => 'cd_install|he_loan',
      'marine_boat||cash_acct_sec||over_time' => 'cd_loc|heloc',
      'marine_boat||unsecured||all_once' => 'unsecured|heloc|ploc',
      'marine_boat||unsecured||over_time' => 'ploc|auto|cd_loc',
      'marine_boat||marine_boat||' => 'marine_boat|heloc|cd_loc',
    ];

    if (array_key_exists($key, $financing_options_mapping)) {
      $return = $financing_options_mapping[$key];
    }
    else {
      $return = [];
    }

    return $return;

  }

  /**
   * Map of financing options for the "other" path.
   */
  public function financingOptionsOther($key) {

    // Format of this mapping:
    // loan_type_1|loan_other_purpose|loan_secure|loan_veh_type|loan_secure_home
    // (fieldnames from the webform).
    $financing_options_mapping = [
      'other|hvac_oth|vehicle|car_truck|' => 'auto|heloc|cd_install',
      'other|hvac_oth|vehicle|motorcycle|' => 'motorcycle|heloc|cd_install',
      'other|hvac_oth|vehicle|atv_utv|' => 'atv_utv|heloc|cd_install',
      'other|hvac_oth|vehicle|rv|' => 'rv|heloc|cd_install',
      'other|hvac_oth|home_re||all_once' => 'he_loan|he_arm',
      'other|hvac_oth|home_re||over_time' => 'heloc|he_loan|he_arm',
      'other|hvac_oth|cash_acct_sec||all_once' => 'cd_install|he_loan',
      'other|hvac_oth|cash_acct_sec||over_time' => 'cd_loc|heloc|ploc',
      'other|hvac_oth|unsecured||all_once' => 'unsecured|ploc',
      'other|hvac_oth|unsecured||over_time' => 'ploc|heloc|cd_loc',
      'other|hvac_oth|marine_boat||' => 'marine_boat|auto|rv',
      'other|spa_pool|vehicle|car_truck|' => 'auto|heloc|cd_install',
      'other|spa_pool|vehicle|motorcycle|' => 'motorcycle|heloc|cd_install',
      'other|spa_pool|vehicle|atv_utv|' => 'atv_utv|heloc|cd_install',
      'other|spa_pool|vehicle|rv|' => 'rv|heloc|cd_install',
      'other|spa_pool|home_re||all_once' => 'he_loan|he_arm',
      'other|spa_pool|home_re||over_time' => 'heloc|he_loan|he_arm',
      'other|spa_pool|cash_acct_sec||all_once' => 'cd_install|he_loan',
      'other|spa_pool|cash_acct_sec||over_time' => 'cd_loc|heloc|ploc',
      'other|spa_pool|unsecured||all_once' => 'unsecured|ploc',
      'other|spa_pool|unsecured||over_time' => 'ploc|heloc|cd_loc',
      'other|spa_pool|marine_boat||' => 'marine_boat|auto|rv',
      'other|vacation|vehicle|car_truck|' => 'auto|heloc|cd_install',
      'other|vacation|vehicle|motorcycle|' => 'motorcycle|heloc|cd_install',
      'other|vacation|vehicle|atv_utv|' => 'atv_utv|heloc|cd_install',
      'other|vacation|vehicle|rv|' => 'rv|heloc|cd_install',
      'other|vacation|home_re||all_once' => 'he_loan|he_arm',
      'other|vacation|home_re||over_time' => 'heloc|he_loan|he_arm',
      'other|vacation|cash_acct_sec||all_once' => 'cd_install|he_loan',
      'other|vacation|cash_acct_sec||over_time' => 'cd_loc|heloc|ploc',
      'other|vacation|unsecured||all_once' => 'unsecured|ploc',
      'other|vacation|unsecured||over_time' => 'ploc|heloc|cd_loc',
      'other|vacation|marine_boat||' => 'marine_boat|auto|rv',
      'other|education|home_re||all_once' => 'he_loan|he_arm',
      'other|education|home_re||over_time' => 'heloc|ploc|cd_loc',
      'other|education|cash_acct_sec||' => 'cd_loc|ploc|heloc',
      'other|education|unsecured||' => 'ploc|heloc|cd_loc',
      'other|medical_oth|vehicle|car_truck|' => 'auto|heloc|cd_install',
      'other|medical_oth|vehicle|motorcycle|' => 'motorcycle|heloc|cd_install',
      'other|medical_oth|vehicle|atv_utv|' => 'atv_utv|heloc|cd_install',
      'other|medical_oth|vehicle|rv|' => 'rv|heloc|cd_install',
      'other|medical_oth|home_re||all_once' => 'he_loan|he_arm',
      'other|medical_oth|home_re||over_time' => 'heloc|he_loan|he_arm',
      'other|medical_oth|cash_acct_sec||all_once' => 'cd_install|he_loan',
      'other|medical_oth|cash_acct_sec||over_time' => 'cd_loc|heloc|ploc',
      'other|medical_oth|unsecured||all_once' => 'unsecured|ploc',
      'other|medical_oth|unsecured||over_time' => 'ploc|heloc|cd_loc',
      'other|medical_oth|marine_boat||' => 'marine_boat|auto|rv',
      'other|cash_equity|||all_once' => 'he_loan|he_arm',
      'other|cash_equity|||over_time' => 'heloc|he_arm|he_loan',
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
