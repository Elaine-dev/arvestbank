<?php

namespace Drupal\arvestbank_ads\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;

/**
 * Plugin implementation of the 'ad_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "ad_formatter",
 *   label = @Translation("Ad Formatter"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class AdFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
      'ad_style' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $ad_styles = \Drupal::service('ad_services')->adStyleOptions();

    $settings = [
      'ad_style' => [
        '#title' => t('Style for the Ad.'),
        '#type' => 'select',
        '#options' => $ad_styles,
        '#default_value' => $this->getSetting('ad_style'),
      ],
    ];

    // Implement settings form.
    return $settings + parent::settingsForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.
    $summary[] = 'Ad Style: ' . $this->getSetting('ad_style');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = [];

    if (method_exists($items[0], 'getValue')) {

      if (!empty($items[0]->getValue()['target_id'])) {

        $ad_nid = $items[0]->getValue()['target_id'];

        if ($ad = Node::load($ad_nid)) {

          switch ($this->getSetting('ad_style')) {

            case 'sidebar':
              $image_fieldname = 'field_image';
              $image_style = 'ad_sidebar';
              break;

            case 'navigation':
              $image_fieldname = 'field_ad_nav_image';
              $image_style = 'ad_navigation';
              break;

            case 'main':
              $image_fieldname = 'field_ad_content_image';
              $image_style = 'ad_main';

          }

          if (!empty($image_fieldname) && method_exists($ad->get($image_fieldname)[0], 'getValue')) {

            if ($image_media_id = $ad->get($image_fieldname)[0]->getValue()['target_id']) {

              if ($image_media = Media::load($image_media_id)) {

                if ($fid = $image_media->field_acquiadam_asset_image[0]->getValue()['target_id']) {

                  $file = File::load($fid);

                  $elements[] = [
                    '#theme' => 'image_style',
                    '#style_name' => $image_style,
                    '#uri' => $file->uri->value,
                  ];

                }

              }

            }

          }

        }

      }

    }

    return $elements;

  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
    return ($field_definition->getTargetEntityTypeId() === 'node' && $field_definition->getTargetBundle() === 'ad_campaign');
  }

}
