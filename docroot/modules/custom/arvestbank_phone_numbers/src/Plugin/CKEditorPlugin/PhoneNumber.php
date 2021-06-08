<?php

namespace Drupal\arvestbank_phone_numbers\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginBase;

/**
 * Defines the "phonenumber" plugin.
 *
 * @CKEditorPlugin(
 *   id = "phonenumber",
 *   label = @Translation("Phone Number"),
 * )
 */
class PhoneNumber extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'arvestbank_phone_numbers') . '/js/plugins/phonenumber/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      'phonenumber' => [
        'label' => t('Phone Number'),
        'image' => drupal_get_path('module', 'arvestbank_phone_numbers') . '/js/plugins/phonenumber/icons/phonenumber.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {

    // Get phone numbers.
    $phoneNumbersQuery = \Drupal::entityQuery('node')
      ->condition('type', 'cta_phone_number')
      ->condition('status', 1);
    $phoneNumbers = $phoneNumbersQuery->execute();

    // Add token array to drupalSettings.
    $config = [
      'phonenumber_tokens' => [],
    ];

    // Loop over tokens adding to token array.
    foreach ($phoneNumbers as $phoneNumberRevisionId => $phoneNumberEntityId) {

      // Load phone number entity.
      $phoneNumberEntity = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($phoneNumberEntityId);

      // Add to array.
      $config['phonenumber_tokens'][] = [
        'number' => $phoneNumberEntity->getTitle(),
        'token' => '[arvestbank_phone_numbers:nid=' . $phoneNumberEntity->id() . ']',
      ];
    }

    return $config;
  }

}
