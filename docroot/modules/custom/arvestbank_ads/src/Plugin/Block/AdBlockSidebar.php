<?php

namespace Drupal\arvestbank_ads\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;

/**
 * Provides an Ad Block for the Sidebar.
 *
 * @Block(
 *  id = "ad_block_sidebar",
 *  admin_label = @Translation("Ad Block - Sidebar"),
 * )
 */
class AdBlockSidebar extends BlockBase {

  /**
   * Returns the sidebar field to use based off of the uri path.
   *
   * @return string
   *   fieldname
   */
  private function getAdFieldFromPath() {

    // Initiaize return variable.
    $return = FALSE;

    // Get the current URI / path alias.
    $current_uri = \Drupal::request()->getRequestUri();

    // Get the fieldmap of paths to fieldnames.
    $fieldmap = \Drupal::service('ad_services')->getSidebarFieldMap();

    // Format to just grab the first two parts.
    $current_uri = ltrim($current_uri, '/');
    $current_uri_ar = explode('/', $current_uri);

    // Set a temp path array to work with.
    $temp_uri_ar = $current_uri_ar;

    // Only going 4 levels deep - helps prevent from maliciousness.
    if (count($temp_uri_ar) > 6) {
      $temp_uri_ar = array_slice($temp_uri_ar, 0, 6);
    }

    // Loop over this array, popping off elements until we have a key match
    // or the array is empty.
    while (!empty($temp_uri_ar)) {

      // String for the path to check.
      $path_key = implode('/', $temp_uri_ar);

      // Check if there is a field for this path, if so set the return var.
      if (array_key_exists($path_key, $fieldmap)) {
        // This should contain the proper fieldname to use.
        $return = $fieldmap[$path_key];
        // Kill this array to exit the loop.
        $temp_uri_ar = [];
      }
      else {
        // Shorten this array.
        array_pop($temp_uri_ar);
      }

    }

    // Returns FALSE or the fieldname that matches to this path.
    return $return;

  }

  /**
   * @return int
   */
  private function getAdNid() {

    // Initalize return variable.
    $ad_nid = 0;

    // Get the current campaign.
    if ($ad_campaign_nid = \Drupal::service('ad_services')->getCampaignNid()) {

      // If there is a valid campaign proceed.
      if ($ad_campaign = Node::load($ad_campaign_nid)) {

        // Match the current page to a sidebar menu item.
        if ($fieldname = self::getAdFieldFromPath()) {
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

    // Get the list of style options.
    $ad_styles = \Drupal::service('ad_services')->adStyleOptions();

    // Storage for node entity types.
    $storage = \Drupal::entityTypeManager()->getStorage('node');

    // Get the node id of the ad to use with this block instance.
    if ($nid = self::getAdNid()) {

      // If the media loaded successfully, continue with the formatting.
      if ($media_id = $storage->load($nid)->get('field_image')[0]->getValue()['target_id']) {

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
              '#style_name' => 'ad_sidebar',
              '#uri' => $ad_image_url,
            ];

            // Get the CTA url for this ad.
            $ad_cta_url = $storage->load($nid)->get('field_cta')[0]->getValue()['uri'] ?? NULL;

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
