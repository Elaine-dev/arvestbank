<?php

namespace Drupal\arvestbank_ads\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

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
   * @return string[]
   */
  private function adStyleOptions() {
    return [
      'sidebar' => 'Sidebar',
      'navigation' => 'Navigation',
      'main' => 'Main Content',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['ad_style'] = [
      '#title' => t('Ad Style'),
      '#type' => 'select',
      '#options' => self::adStyleOptions(),
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
  private function getAdNid() {
    return 6956;
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
    $nid = self::getAdNid();

    // Storage for node entity types.
    $storage = \Drupal::entityTypeManager()->getStorage('node');

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

          // Set the block style.
          $block_style = $this->getConfiguration()['ad_style'];

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

    // Set the return ad content.
    $build['#content'] = $ad_content;

    return $build;

  }

}
