<?php

namespace Drupal\arvestbank_ads\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;

/**
 * Provides an Ad Block for the Main Content.
 *
 * @Block(
 *  id = "ad_block_main",
 *  admin_label = @Translation("Ad Block - Main Content"),
 * )
 */
class AdBlockMain extends BlockBase {

  /**
   * Returns the fieldname from the ad campaign to use with this nav item.
   *
   * @return string
   *   fieldname
   */
  private function getAdFieldFromContext() {

    // Initialize return variable.
    $return = FALSE;

    // The current aliased path.
    $current_uri = \Drupal::request()->getRequestUri();

    // This array maps ad block ID's with fieldnames of the ad campaign.
    $fieldnames = [
      // Application.
      '/personal/apply/exit' => 'field_ad_exit_application',
      // Banking.
      '/personal/bank/online/banking/exit' => 'field_ad_exit_banking',
      // Credit Card Apply.
      '/personal/bank/credit-cards/apply/exit' => 'field_ad_exit_ccapply',
      // Switch Kit.
      '/personal/bank/online/online-switch-kit/exit' => 'field_ad_exit_switchkit',
    ];

    // If the current path is valid for a fieldname set the return var.
    if (array_key_exists($current_uri, $fieldnames)) {
      $return = $fieldnames[$current_uri];
    }

    // Returns FALSE or the fieldname that matches to this nav item.
    return $return;

  }

  /**
   * Returns the node id for the ad to use in this context.
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
  public function build() {

    // Initialize the main build return var, and placeholder for content.
    $build = [];
    $ad_content = [];

    // Use /templates/ad-block.html.twig.
    $build['#theme'] = 'ad_block';

    // Get the node id of the ad to use with this block instance.
    if ($nid = self::getAdNid()) {

      // Storage for node entity types.
      $storage = \Drupal::entityTypeManager()->getStorage('node');

      // Get the CTA url for this ad.
      $ad_cta_url = $storage->load($nid)->get('field_cta')[0]->getValue()['uri'] ?? NULL;

      // If the media loaded successfully, continue with the formatting.
      if ($ad_node_field = $storage->load($nid)->get('field_ad_content_image')[0]) {

        if ($media_id = $ad_node_field->getValue()['target_id']) {

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

              // Render array for the image.
              $ad_image = [
                '#theme' => 'image_style',
                '#style_name' => 'tile_main',
                '#uri' => $ad_image_url,
                '#attributes' => [
                  'alt' => $alt_value,
                ],
              ];

              // If there is a CTA, link this image.
              if (!empty($ad_cta_url)) {
                $ad_content = [
                  '#type' => 'link',
                  '#url' => Url::fromUri($ad_cta_url),
                  '#title' => $ad_image,
                  '#attributes' => [
                    'data-tileType' => 'tile_main',
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

    // Build array.
    return $build;

  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    // Block should be cached on a per page (route) level.
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }

}
