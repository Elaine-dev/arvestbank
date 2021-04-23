<?php

namespace Drupal\arvestbank_external_login\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an external login block.
 *
 * @Block(
 *   id = "homepage_external_login_block",
 *   admin_label = @Translation("Homepage External Login Block"),
 * )
 */
class HomepageExternalLoginBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $renderArray = [
      '#type' => 'webform',
      '#webform' => 'external_login',
      '#attached' => [
        'library' => [
          'arvestbank_external_login/external-login',
        ],
      ],
    ];

    return $renderArray;

  }

}
