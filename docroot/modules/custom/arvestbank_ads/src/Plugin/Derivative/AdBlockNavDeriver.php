<?php

namespace Drupal\arvestbank_ads\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Provides block plugin definitions for ads in the main Navigation.
 *
 * @see \Drupal\arvestbank_ads\Plugin\Block\AdBlockNav
 */
class AdBlockNavDeriver extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {

    $this->derivatives = [
      'ads_nav_411' => [
        'admin_label' => 'Ads Nav Personal',
      ] + $base_plugin_definition,
      'ads_nav_586' => [
        'admin_label' => 'Ads Nav Business',
      ] + $base_plugin_definition,
      'ads_nav_706' => [
        'admin_label' => 'Ads Nav Credit Cards',
      ] + $base_plugin_definition,
      'ads_nav_726' => [
        'admin_label' => 'Ads Nav Home Loans',
      ] + $base_plugin_definition,
      'ads_nav_746' => [
        'admin_label' => 'Ads Nav Investments',
      ] + $base_plugin_definition,
    ];

    return $this->derivatives;
  }

}
