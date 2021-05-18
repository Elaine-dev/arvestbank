<?php

namespace Drupal\arvestbank_ads\Plugin\Block;

use Drupal\Core\Block\BlockBase;
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
   * Returns the node id for the ad to use in this context.
   *
   * @return int
   *   nid.
   */
  private function getAdNid(): int {

    // Placeholder function to return the proper ad node id.
    return 0;

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

          // Get the file id for this media item.
          $fid = Media::load($media_id)->field_acquiadam_asset_image[0]->getValue()['target_id'];

          // Load the file from this fid.
          if ($file = File::load($fid)) {

            // This will be the public:// path to the media item.
            $ad_image_url = $file->getFileUri();

            // Continue if there is a url for this ad image.
            if (!empty($ad_image_url)) {

              // Render array for the image.
              $ad_image = [
                '#theme' => 'image_style',
                '#style_name' => 'ad_main',
                '#uri' => $ad_image_url,
              ];

              // If there is a CTA, link this image.
              if (!empty($ad_cta_url)) {
                $ad_content = [
                  '#type' => 'link',
                  '#url' => Url::fromUri($ad_cta_url),
                  '#title' => $ad_image,
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
  public function getCacheMaxAge(): int {
    return 0;
  }

}
