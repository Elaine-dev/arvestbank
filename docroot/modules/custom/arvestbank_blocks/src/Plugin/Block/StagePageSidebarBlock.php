<?php

namespace Drupal\arvestbank_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a block for the stage page sidebar.  Shows the block or alert.
 *
 * @Block(
 *  id = "stagepage_sidebar_block",
 *  admin_label = @Translation("Stage Page Sidebar Block"),
 * )
 */
class StagePageSidebarBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Initialize the return render array.
    $build = [];

    // Get the current entity with the helper function.
    $entity = \Drupal::service('arvestbank_blocks.service')->getRouteEntity();

    // Check for Node.
    if ($entity instanceof Node) {

      $sidebar_block = 'SIDEBAR BLOCK';
      //dump($entity);die();

    }

    // If there is disclosure text add it to the markup.
    if (!empty($sidebar_block)) {
      $build['sidebar_block'] = [
        '#prefix' => '',
        '#suffix' => '',
        '#markup' => $sidebar_block,
      ];
    }

    // Return the build render array.
    return $build;

  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    return 0;
  }

}
