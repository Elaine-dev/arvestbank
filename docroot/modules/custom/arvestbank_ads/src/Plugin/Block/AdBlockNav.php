<?php

namespace Drupal\arvestbank_ads\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;

/**
 * Provides an Ad Block for the Nav.
 *
 * @Block(
 *  id = "ad_block_nav",
 *  admin_label = @Translation("Ad Block - Nav"),
 *  deriver = "Drupal\arvestbank_ads\Plugin\Derivative\AdBlockNavDeriver"
 * )
 */
class AdBlockNav extends BlockBase {

  /**
   * Returns the fieldname from the ad campaign to use with this nav item.
   *
   * @return string
   *   fieldname
   */
  private function getAdFieldFromContext() {

    // Initialize return variable.
    $return = FALSE;

    // This array maps ad block ID's with fieldnames of the ad campaign.
    $fieldnames = [
      'ads_nav_411' => 'field_ad_nav_personal',
      'ads_nav_586' => 'field_ad_nav_business',
      'ads_nav_706' => 'field_ad_nav_credit_cards',
      'ads_nav_726' => 'field_ad_nav_home_loans',
      'ads_nav_746' => 'field_ad_nav_investments',
    ];

    // Get the block derivative id.
    $block_id = $this->getDerivativeId();

    // Match that block id to the fieldname.
    if (array_key_exists($block_id, $fieldnames)) {
      $return = $fieldnames[$block_id];
    }

    // Returns FALSE or the fieldname that matches to this nav item.
    return $return;

  }

  /**
   * @return int
   */
  private function getAdNid() {

    // Initialize return variable.
    $ad_nid = 0;

    // Get the current campaign.
    if ($ad_campaign_nid = \Drupal::service('ad_services')->getCampaignNid()) {

      // If there is a valid campaign proceed.
      if ($ad_campaign = Node::load($ad_campaign_nid)) {

        // Match the current page to a sidebar menu item.
        if ($fieldname = self::getAdFieldFromContext()) {
          if (!empty($ad_campaign->get($fieldname)->getValue()[0]['target_id'])) {
            $ad_nid = $ad_campaign->get($fieldname)->getValue()[0]['target_id'];
          }

        }

      }

    }

    return $ad_nid;

  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Initialize the main build return var, and placeholder for content.
    $build = [];
    $ad_content = [];

    // Use /templates/ad-block.html.twig.
    $build['#theme'] = 'ad_block';

    // Storage for node entity types.
    $storage = \Drupal::entityTypeManager()->getStorage('node');

    // Get the node id of the ad to use with this block instance.
    if ($nid = self::getAdNid()) {

      // If the media loaded successfully, continue with the formatting.
      if ($media_id = $storage->load($nid)->get('field_ad_nav_image')[0]->getValue()['target_id']) {

        // Get the file id for this media item.
        $fid = Media::load($media_id)->field_acquiadam_asset_image[0]->getValue()['target_id'];

        // Load the file from this fid.
        if ($file = File::load($fid)) {

          // This will be the public:// path to the media item.
          $ad_url = $file->getFileUri();

          if (!empty($ad_url)) {
            $ad_content = [
              '#theme' => 'image_style',
              '#style_name' => 'ad_navigation',
              '#uri' => $ad_url,
            ];
          }

        }

      }

    }

    // Set the return ad content.
    $build['#content'] = $ad_content;

    return $build;

  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
