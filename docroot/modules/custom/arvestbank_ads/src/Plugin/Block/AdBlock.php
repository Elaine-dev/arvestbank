<?php

namespace Drupal\arvestbank_ads\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'AdBlock' block.
 *
 * @Block(
 *  id = "ad_block",
 *  admin_label = @Translation("Ad block"),
 * )
 */
class AdBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'ad_block';
     $build['ad_block']['#markup'] = 'Implement AdBlock.';

    return $build;
  }

}
