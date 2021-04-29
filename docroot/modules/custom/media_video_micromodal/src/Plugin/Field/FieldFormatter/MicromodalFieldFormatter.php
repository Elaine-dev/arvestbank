<?php

namespace Drupal\media_video_micromodal\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\file\Entity\File;
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
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {

      // Load the media item.
      $media_id = $item->getValue()['target_id'];
      $media = Media::load($media_id);

      $video_url = $media->getFields()['field_media_oembed_video']->getValue()[0]['value'];

      $request = new RequestContext($video_url);
      $private_key = new PrivateKey(\Drupal::state());
      $url_helper = new IFrameUrlHelper($request, $private_key);

      $max_width = 0;
      $max_height = 0;

      $media_oembed_url = Url::fromRoute('media.oembed_iframe', [], [
        'query' => [
          'url' => $video_url,
          'max_width' => 0,
          'max_height' => 0,
          'hash' => $url_helper->getHash($video_url, $max_width, $max_height),
        ],
      ])->toString();

      $thumbnail_id = $media->getFields()['thumbnail']->getValue()[0]['target_id'];

      // Initialize a default value for the thumbnail.
      $thumbnail_url = '';

      $style = ImageStyle::load('medium');
      if (!empty($thumbnail_id)) {
        $thumbnail_file = File::load($thumbnail_id);
        $thumbnail_url = $style->buildUrl($thumbnail_file->uri->value);
      }

      // This will be used as the value of the div.
      $modal_id = 'modal-media-' . $media_id;

      $elements[$delta] = [
        '#theme' => 'media_video_micromodal',
        '#modal_id' => $modal_id,
        '#thumbnail_url' => $thumbnail_url,
        '#iframe_src' => $media_oembed_url,
      ];

    }

    if (!empty($elements)) {
      // Attach libraries.
      $elements['#attached'] = [
        'library' => [
          'media_video_micromodal/micromodal_libraries',
        ],
      ];
    }

    return $elements;

  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

  /**
   * {@inheritdoc}
   */
//  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
//    return $field_definition->getTargetEntityTypeId() === 'node'
//      && $field_definition->getTargetBundle() === 'calculators'
//      && $field_definition->getName() === 'field_calculator';
//  }

}
