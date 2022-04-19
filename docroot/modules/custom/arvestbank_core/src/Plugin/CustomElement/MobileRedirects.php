<?php
namespace Drupal\arvestbank_core\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;

/**
 * Generic HTML element plugin for Site Studio.
 *
 * @CustomElement(
 *   id = "mobile_redirects",
 *   label = @Translation("Mobile Redirects to the different app stores.")
 * )
 */
class MobileRedirects extends CustomElementPluginBase {
  public function getFields() {
    return [
      // This is an example text field.
      'apple_store_url' => [
        'htmlClass' => 'col-xs-12',
        'title' => 'Apple Store URL',
        'type' => 'textfield',
      ],
      'google_store_url' => [
        'htmlClass' => 'col-xs-12',
        'title' => 'Google Store URL',
        'type' => 'textfield',
      ],
    ];
  }

  public function render($settings, $markup, $class) {
    return [
      '#theme' => 'generic_element',
      '#settings' => $settings,
      '#markup' => $markup,
      '#class' => $class,
      '#attached' => [
        'drupalSettings' => [
          'appleUrl' => $settings['apple_store_url'],
          'googleUrl' => $settings['google_store_url'],
        ],
        'library' => [
          'arvestbank_core/mobile_redirect',
        ],
      ],
    ];
  }
}
