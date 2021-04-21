<?php

namespace Drupal\arvestbank_external_login\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\views\Views;
use Drupal\Core\Form\FormState;

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
    ];

    return $renderArray;

  }

}
