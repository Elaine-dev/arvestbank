<?php

namespace Drupal\arvestbank_ads\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
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
   * Returns the Node ID of the Ad to use.
   *
   * @return int
   *   nid.
   */
  private function getAdNid(): int {

    // Initialize return variable.
    $ad_nid = 0;

    // Get the current campaign.
    if ($ad_campaign_nid = \Drupal::service('ad_services')->getCampaignNid()) {

      // If there is a valid campaign proceed.
      if ($ad_campaign = Node::load($ad_campaign_nid)) {

        // Match the calling nav item to the correct fieldname.
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
  public function build(): array {

    // Initialize the main build return var, and placeholder for content.
    $build = [];
    $ad_content = [];

    // Use /templates/ad-block.html.twig.
    $build['#theme'] = 'ad_block';

    // Storage for node entity types.
    $storage = \Drupal::entityTypeManager()->getStorage('node');

    // Get the node id of the ad to use with this block instance.
    if ($nid = self::getAdNid()) {

      // Check for having this field.
      if ($storage->load($nid)->hasField('field_ad_nav_image')) {

        // If the media loaded successfully, continue with the formatting.
        if ($media_id = $storage->load($nid)->get('field_ad_nav_image')[0]->getValue()['target_id']) {

          // Get the media item
          $media_entity = Media::load($media_id);

          // Get the file id for this media item.
          $fid = $media_entity->field_acquiadam_asset_image[0]->getValue()['target_id'];

          // Save the alt text value from original image.
          $alt_value = $media_entity->field_acquiadam_asset_image->first()->get('alt')->getValue();

          // Load the file from this fid.
          if ($file = File::load($fid)) {

            // This will be the public:// path to the media item.
            $ad_image_url = $file->getFileUri();

            // Continue if there is a url for this ad image.
            if (!empty($ad_image_url)) {

              // Render array for the ad image.
              $ad_image = [
                '#theme' => 'image_style',
                '#style_name' => 'tile_navigation',
                '#uri' => $ad_image_url,
                '#attributes' => [
                  'alt' => $alt_value,
                ],
              ];

              // Get the CTA url for this ad.
              $ad_cta_url = $storage->load($nid)->get('field_cta')[0]->getValue()['uri'] ?? NULL;

              // If there is a CTA, link this image.
              if (!empty($ad_cta_url)) {
                $ad_content = [
                  '#type' => 'link',
                  '#url' => Url::fromUri($ad_cta_url),
                  '#title' => $ad_image,
                  '#attributes' => [
                    'data-tileType' => 'main_navigation',
                  ],
                ];
              }
              // Else just return the image.
              else {
                $ad_content = $ad_image;
              }

            }

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
  public function getCacheMaxAge(): int {
    return 86400;
  }

}
