<?php

namespace Drupal\arvestbank_ads\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;

/**
 * Provides a 'AdBlock' block.
 *
 * @Block(
 *  id = "ad_block",
 *  admin_label = @Translation("Ad block"),
 * )
 */
class AdBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $ad_styles = \Drupal::service('ad_services')->adStyleOptions();

    $form['ad_style'] = [
      '#title' => t('Ad Style'),
      '#type' => 'select',
      '#options' => $ad_styles,
      '#default_value' => $config['ad_style'] ?? NULL,
      '#description' => t('Style for this block.'),
    ];

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['ad_style'] = $values['ad_style'];
  }

  /**
   * @return int
   */
  private function getCampaignNid() {
    return 6959;
  }

  /**
   * @return string
   */
  private function getAdFieldFromPath() {

    // Initiaize return variable.
    $return = FALSE;

    // Get the current URI / path alias.
    $current_uri = \Drupal::request()->getRequestUri();

    // Get the fieldmap of paths to fieldnames.
    $fieldmap = \Drupal::service('sidebar_field_map')->getSidebarFieldMap();

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
    $ad_campaign_nid = self::getCampaignNid();

    // If there is a valid campaign proceed.
    if ($ad_campaign = Node::load($ad_campaign_nid)) {

      // Match the current page to a sidebar menu item.
      if ($fieldname = self::getAdFieldFromPath()) {
        if (!empty($ad_campaign->get($fieldname)->getValue()[0]['target_id'])) {
          $ad_nid = $ad_campaign->get($fieldname)->getValue()[0]['target_id'];
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

    // Set the block style.
    $block_style = $this->getConfiguration()['ad_style'];

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
          $ad_url = $file->getFileUri();

          // Get the style for this block,
          // and use the corresponding image and image style.
          if (array_key_exists($this->getConfiguration()['ad_style'], self::adStyleOptions())) {

            // Set a default image style.
            $image_style = 'medium';

            // Check for image style for this block style.
            if (array_key_exists('ad_' . $block_style, image_style_options(TRUE))) {
              $image_style = 'ad_' . $block_style;
            }

            $ad_content = [
              '#theme' => 'image_style',
              '#style_name' => $image_style,
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
