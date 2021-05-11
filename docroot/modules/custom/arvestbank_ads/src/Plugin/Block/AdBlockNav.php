<?php

namespace Drupal\arvestbank_ads\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\views\Views;

/**
 * Provides an Ad Block for the Nav.
 *
 * @Block(
 *  id = "ad_block_nav",
 *  admin_label = @Translation("Ad Block - Nav"),
 * )
 */
class AdBlockNav extends BlockBase {

  //class AdBlockNav extends BlockBase implements BlockPluginInterface {
//class AdBlockNav extends BlockBase implements ContainerFactoryPluginInterface {
  // BlockPluginInterface


  /**
   * @return string
   */
  private function getAdFieldFromContext() {

    // Initiaize return variable.
    $return = FALSE;

    $fieldnames = [
      'field_ad_nav_personal',
      'field_ad_nav_business',
      'field_ad_nav_credit_cards',
      'field_ad_nav_home_loans',
      'field_ad_nav_investments',
    ];

    $return = $fieldnames[0];

    // Returns FALSE or the fieldname that matches to this path.
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
