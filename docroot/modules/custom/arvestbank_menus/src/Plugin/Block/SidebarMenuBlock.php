<?php

namespace Drupal\arvestbank_menus\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\block\Entity\Block;

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
   * Maps menu machine names to their corresponding sidebar blocks.
   */
  const MENU_SIDEBAR_BLOCKS = [
    'main'                  => 'sidebar_menu_main_navigation',
    'top-menu'              => 'sidebar_menu_top_menu',
    'footer'                => 'sidebar_menu_footer_menu',
    'education-center-menu' => 'sidebar_menu_education_center_menu',
  ];

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Instantiate render array to return.
    $renderArray = [];

    // Get current node or term.
    $node = \Drupal::routeMatch()->getParameter('node');
    $term = \Drupal::routeMatch()->getParameter('taxonomy_term');

    // If this is a node page.
    if ($node) {

      // Get canonical menu link.
      $canonicalMenuLinkHelper = \Drupal::service('arvestbank_menus.canonical_menu_link_helper');
      $canonicalMenuLinkId = $canonicalMenuLinkHelper->getCanonicalMenuLinkIds($node->id(), TRUE);

      // If this node has a canonical menu link.
      if ($canonicalMenuLinkId) {

        // Load the menu link content entity.
        $canonicalMenuLink = MenuLinkContent::load($canonicalMenuLinkId);

        // Get the menu name the canonical menu link belongs to.
        $canonicalMenuName = $canonicalMenuLink->getMenuName();

        // If we have a sidebar menu block for the canonical menu.
        if (isset(self::MENU_SIDEBAR_BLOCKS[$canonicalMenuName])) {

          // Get base render array for this block.
          $renderArray = $this->getBaseRenderArray($canonicalMenuLink);

          // Get menu block.
          $menuBlockId = self::MENU_SIDEBAR_BLOCKS[$canonicalMenuName];
          $menuBlock = Block::load($menuBlockId);

          // Render menu block (showing children) and add to render array.
          $renderArray['menu_block'] = \Drupal::entityTypeManager()
            ->getViewBuilder('block')->view($menuBlock);

        }

      }

    }
    // If we're on an education article category term page.
    elseif (
      $term
      && $term->bundle() == 'education_article_category'
    ) {

      // Get rendered menu block.
      $menuBlockId = self::MENU_SIDEBAR_BLOCKS['education-center-menu'];
      $menuBlock = Block::load($menuBlockId);

      // Add rendered menu block to our block render array.
      $renderArray['menu_block'] = \Drupal::entityTypeManager()
        ->getViewBuilder('block')->view($menuBlock);

    }

    // Build and return form.
    return $renderArray;
  }

  /**
   * Get the base render array for this block.
   *
   * @param \Drupal\menu_link_content\Entity\MenuLinkContent $menuLink
   *   The canonical menu link for this page.
   */
  private function getBaseRenderArray(MenuLinkContent $menuLink) {

    // Start render array with a container.
    $renderArray = [
      '#type'       => 'container',
      '#attributes' => [
        'class' => ['sidebar-menu'],
      ],
    ];

    // Test markup.
    $renderArray['test'] = ['#markup' => 'test'];
  }

}
