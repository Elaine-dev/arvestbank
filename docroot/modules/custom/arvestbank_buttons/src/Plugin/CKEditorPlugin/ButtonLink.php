<?php

namespace Drupal\arvestbank_buttons\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginBase;

/**
 * Defines the "buttonlink" plugin.
 *
 * @CKEditorPlugin(
 *   id = "buttonlink",
 *   label = @Translation("Button Link")
 * )
 */
class ButtonLink extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'arvestbank_buttons') . '/js/plugins/buttonlink/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      'ButtonLink' => [
        'label' => t('Button'),
        'image' => drupal_get_path('module', 'arvestbank_buttons') . '/js/plugins/buttonlink/icons/ButtonLink.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

}
