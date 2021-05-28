<?php

namespace Drupal\arvestbank_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
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

      if ($entity->bundle() === 'stage_page') {

        // Initialize the return variable.
        $sidebar_block = [];

        // Sidebar block title.
        if (!empty($entity->hasField('field_block_title'))) {
          if (!empty($entity->get('field_block_title')->getValue()[0]['value'])) {
            $sidebar_block['title'] = [
              '#markup' => $entity->get('field_block_title')->getValue()[0]['value'],
              '#prefix' => '<h3>',
              '#suffix' => '</h3>',
            ];
          }
        }

        // Initialize sidebar block conents.
        $sidebar_block_contents = [];

        // Sidebar block body.
        if (!empty($entity->hasField('body'))) {
          if (!empty($entity->get('body')->getValue()[0]['value'])) {
            $sidebar_block_contents['body'] = [
              '#markup' => $entity->get('body')->getValue()[0]['value'],
            ];
          }
        }

        // Sidebar block button.
        if (!empty($entity->hasField('field_block_button'))) {
          if (!empty($entity->get('field_block_button')->getValue()[0]['uri'])) {
            $sidebar_block_button = $entity->get('field_block_button')->getValue()[0];
            $sidebar_button_url = Url::fromUri($sidebar_block_button['uri'], []);
            $sidebar_button_link = Link::fromTextAndUrl($sidebar_block_button['title'], $sidebar_button_url)->toString();
            $sidebar_button = [
              '#markup' => render($sidebar_button_link),
              '#prefix' => Markup::create('<button>'),
              '#suffix' => Markup::create('</button>'),
            ];
            $sidebar_block_contents['button'] = $sidebar_button;
          }
        }

        // If there is sidebar block content, wrap with the flex-wrapper
        // and add to the sidebar_block.
        if (!empty($sidebar_block_contents)) {
          $sidebar_block_contents['#prefix'] = '<div class="flex-wrapper">';
          $sidebar_block_contents['#suffix'] = '</div>';
          $sidebar_block[] = $sidebar_block_contents;
        }

      }

    }

    // If there is disclosure text add it to the markup.
    if (!empty($sidebar_block)) {
      $build['sidebar_block'] = $sidebar_block;
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
