<?php

namespace Drupal\arvestbank_rates\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginBase;

/**
 * Defines the "creditcardrate" plugin.
 *
 * @CKEditorPlugin(
 *   id = "creditcardrate",
 *   label = @Translation("Credit Card Rate"),
 * )
 */
class CreditCardRate extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'arvestbank_rates') . '/js/plugins/creditcardrate/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      'creditcardrate' => [
        'label' => t('Credit Card Rate'),
        'image' => drupal_get_path('module', 'arvestbank_rates') . '/js/plugins/creditcardrate/icons/creditcardrate.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {

    // Get Rates config.
    $ratesConfigValues = \Drupal::config('arvestbank_rates.settings')->getOriginal();

    // Add token array to drupalSettings.
    $config = [
      'creditcardrate_tokens' => [],
    ];

    // Loop over tokens adding to token array.
    foreach ($ratesConfigValues as $tokenName => $tokenValue) {
      // Decide on human name for config variable.
      $tokenHumanName = ucwords(str_replace(['__', '_'], [' - ', ' '], $tokenName));
      // Add to array.
      $config['creditcardrate_tokens'][] = [
        $tokenHumanName,
      ];
    }

    // Alphabetize tokens.
    sort($config['creditcardrate_tokens']);

    return $config;
  }

}
