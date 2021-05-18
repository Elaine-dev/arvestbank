<?php

namespace Drupal\arvestbank_ads;

use Drupal\views\Views;

/**
 * Class SidebarFieldMap.
 */
class AdServices {

  /**
   * Constructs a new SidebarFieldMap object.
   */
  public function __construct() {
  }

  /**
   * Returns an array with key of path and value of ad campaign field name.
   *
   * @return array
   *   Return array map.
   */
  public function getSidebarFieldMap(): array {

    return [
      'personal/bank' => 'field_ad_side_p_bank',
      'personal/borrow' => 'field_ad_side_p_borrow',
      'personal/invest' => 'field_ad_side_p_invest',
      'personal/protect' => 'field_ad_side_p_other',
      'calculators' => 'field_ad_side_p_other',
      'business/bank' => 'field_ad_side_b_bank',
      'business/borrow' => 'field_ad_side_b_borrow',
      'business/manage' => 'field_ad_side_b_manage',
      'business/protect' => 'field_ad_side_b_protect',
      'personal/bank/credit-cards' => 'field_ad_side_cc_personal',
      'business/borrow/credit-cards' => 'field_ad_side_cc_business',
      'contact/credit-cards' => 'field_ad_side_cc_manage',
      'personal/bank/credit-cards/dispute' => 'field_ad_side_cc_manage',
      'personal/bank/credit-cards/visa-checkout' => 'field_ad_side_cc_protect',
      'personal/bank/credit-cards/visa-purchase-alerts' => 'field_ad_side_cc_protect',
      'personal/borrow/home-loans' => 'field_ad_side_hl_get_started',
      'personal/borrow/home-loans/mortgage-programs' => 'field_ad_side_hl_lending_options',
      'personal/borrow/consumer-loans' => 'field_ad_side_hl_lending_options',
      'education-center/home-ownership' => 'field_ad_side_hl_learn_more',
      'personal/borrow/home-loans/servicing-center' => 'field_ad_side_hl_existing_loan',
      'personal/sign-on/login' => 'field_ad_side_hl_existing_loan',
      'personal/invest/investing/retirement-planning' => 'field_ad_side_it_planning',
      'investment-planning' => 'field_ad_side_it_planning',
      'personal/invest/planning' => 'field_ad_side_it_planning',
      'personal/invest/investing' => 'field_ad_side_it_investing',
      'personal/invest/find-a-client-advisor' => 'field_ad_side_it_investing',
      'personal/invest/manage' => 'field_ad_side_it_investing',
      'personal/invest/insurance' => 'field_ad_side_it_insurance',
      'personal/invest/trust-and-estate-services' => 'field_ad_side_it_trust_estate',
      'about' => 'field_ad_side_about',
      'contact' => 'field_ad_side_contact',
      'education-center' => 'field_ad_side_education_center',
      'documents-and-resources' => 'field_ad_side_docs_resources',
    ];

  }

  /**
   * Set array of ad styles.
   *
   * @return array
   *   styles.
   */
  public function adStyleOptions(): array {
    return [
      'sidebar' => 'Sidebar',
      'navigation' => 'Navigation',
      'main' => 'Main Content',
    ];
  }

  /**
   * Returns the node id of the current campaign.
   *
   * @return int
   *   nid.
   */
  public function getCampaignNid() {

    $return = FALSE;

    $view = Views::getView('ad_campaigns');
    $view->setDisplay('attachment_1');
    $view->render();
    if (!empty($view->result[0])) {
      $result = $view->result[0];
      if (property_exists($result, 'nid')) {
        $return = $result->nid;
      }
    }

    return $return;

  }

}
