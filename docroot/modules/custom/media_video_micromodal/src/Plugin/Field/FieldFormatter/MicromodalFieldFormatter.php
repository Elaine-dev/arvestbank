<?php

namespace Drupal\media_video_micromodal\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\Plugin\Field\FieldType\StringItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\file\Entity\File;
use Drupal\image\Plugin\Field\FieldType\ImageItem;
use Drupal\media\Entity\Media;
use Drupal\media\IFrameUrlHelper;
use Drupal\Core\Routing\RequestContext;
use Drupal\Core\PrivateKey;

/**
 * Plugin implementation of the 'micromodal_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "micromodal_field_formatter",
 *   label = @Translation("Micromodal field formatter"),
 *   field_types = {
 *     "string",
 *     "image"
 *   }
 * )
 */
class MicromodalFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
      'string_classes' => '',
      'thumbnail_image_style' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    if ($this->fieldDefinition->getType() == 'string') {

      $settings = [
        'string_classes' => [
          '#title' => t('Additional Classes'),
          '#type' => 'textfield',
          '#size' => 60,
          '#maxlength' => 255,
          '#default_value' => $this->getSetting('string_classes'),
          '#description' => t('Add additional classes to the text link, separate by spaces.'),
        ],
      ];

    }

    elseif ($this->fieldDefinition->getType() == 'image') {

      $settings = [
        'thumbnail_image_style' => [
          '#title' => t('Video Thumbnail Image Style'),
          '#type' => 'select',
          '#options' => image_style_options(FALSE),
          '#empty_option' => '<' . t('no preview') . '>',
          '#default_value' => $this->getSetting('thumbnail_image_style'),
          '#description' => t('Thumbnail for the video, click the thumbnail for the modal window.'),
        ],
      ];

    }

    // Implement settings form.
    return $settings + parent::settingsForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    // Implement settings summary.
    if (!empty($this->getSetting('string_classes'))) {
      $summary[] = 'Additional Classes: ' . $this->getSetting('string_classes');
    }
    if (!empty($this->getSetting('thumbnail_image_style'))) {
      $summary[] = 'Image Style: ' . $this->getSetting('thumbnail_image_style');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {

      // Load the media item.
      // Use this for thumbnails.
      if ($item instanceof ImageItem) {
        $media_id = $item->getValue()['target_id'];
        $media = Media::load($media_id);
        $formatter_type = 'image';
      }
      // Use this for the media name.
      elseif ($item instanceof StringItem) {
        $media = $item->getEntity();
        $media_id = $media->id();
        $formatter_type = 'string';
      }
      else {
        $formatter_type = 'n/a';
      }

      // If the media loaded successfully, continue with the formatting.
      if (!empty($media)) {

        // Grab the remote video URL.
        $video_url = $media->getFields()['field_media_oembed_video']->getValue()[0]['value'];

        // Use these to generate the URL for local oembed iframe.
        $request = new RequestContext($video_url);
        $private_key = new PrivateKey(\Drupal::state());
        $url_helper = new IFrameUrlHelper($request, $private_key);

        // These are needed to create the hash successfully.
        $max_width = 0;
        $max_height = 0;

        // Use parts above to generate the iframe url.
        $media_oembed_url = Url::fromRoute('media.oembed_iframe', [], [
          'query' => [
            'url' => $video_url,
            'max_width' => 0,
            'max_height' => 0,
            'hash' => $url_helper->getHash($video_url, $max_width, $max_height),
          ],
        ])->toString();

        if ($formatter_type == 'image') {

          // Media ID of the thumbnail.
          $thumbnail_id = $media->getFields()['thumbnail']->getValue()[0]['target_id'];

          // Use the image style setting to style the thumbnail.
          if (!empty($thumbnail_id)) {
            $thumbnail_file = File::load($thumbnail_id);
            $render_thumbnail = [
              '#theme' => 'image_style',
              '#style_name' => $this->getSetting('thumbnail_image_style'),
              '#uri' => $thumbnail_file->uri->value,
            ];
            $linked_item = render($render_thumbnail);
          }

        }

        elseif ($formatter_type == 'string') {

          if (!empty($this->getSetting('string_classes'))) {
            $linked_item_render = [
              '#markup' => '<span class="' . $this->getSetting('string_classes') . '">' . $media->getName() . '</span>',
            ];
            $linked_item = render($linked_item_render);
          }
          else {
            $linked_item = $media->getName();
          }

        }

        if (!empty($linked_item)) {

          // This will be used as the value of the div.
          $modal_id = 'modal-media-' . $media_id;

          // Send these to the twig template.
          $elements[$delta] = [
            '#theme' => 'media_video_micromodal',
            '#modal_id' => $modal_id,
            '#linked_item' => $linked_item,
            '#iframe_src' => $media_oembed_url,
          ];

        }

      }

    }

    // Attach libraries.
    if (!empty($elements)) {
      $elements['#attached'] = [
        'library' => [
          'media_video_micromodal/micromodal_libraries',
        ],
      ];
    }

    return $elements;

  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
    return $field_definition->getTargetEntityTypeId() === 'media'
      // @todo: this always returns "null", maybe b/c of this bug:
      // https://www.drupal.org/project/drupal/issues/2976795
      // && $field_definition->getTargetBundle() === 'video'
      && ($field_definition->getFieldStorageDefinition()->getName() === 'thumbnail'
      || $field_definition->getFieldStorageDefinition()->getName() === 'name');
  }

}
