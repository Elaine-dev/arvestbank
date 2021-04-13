<?php

namespace Drupal\arvestbank_menus\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides the appropriate sidebar menu in a block.
 *
 * @Block(
 *   id = "sidebar_menu_block",
 *   admin_label = @Translation("Sidebar Menu Block"),
 * )
 */
class SidebarMenuBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Start render array with a container.
    $renderArray = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['sidebar-menu'],
      ],
    ];

    // Test markup.
    $renderArray['test'] = ['#markup' => 'test'];

    // Build and return form.
    return $renderArray;

  }

}
