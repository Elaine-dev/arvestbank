<?php

namespace Drupal\arvestbank_external_login\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an external login block.
 *
 * @Block(
 *   id = "menu_external_login_block",
 *   admin_label = @Translation("Menu External Login Block"),
 * )
 */
class MenuExternalLoginBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $renderArray = [
      '#type' => 'webform',
      '#webform' => 'external_login_menu',
    ];

    return $renderArray;

  }

}
