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
   * Builds the render array for the sidebar menu block.
   *
   * If not showing up check sidebar hide logic in currentRouteHasSidebarMenu().
   *
   * {@inheritdoc}
   */
  public function build() {

    // Instantiate render array to return.
    $renderArray = [];

    // Get current node, term, or view.
    $node = \Drupal::routeMatch()->getParameter('node');
    $term = \Drupal::routeMatch()->getParameter('taxonomy_term');
    $view_id = \Drupal::routeMatch()->getRouteObject()->getDefault('view_id');
    $view_display = \Drupal::routeMatch()->getRouteObject()->getDefault('display_id');

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
      // If this is an education article.
      elseif ($node->getType() == 'article_education_article') {

        // Get rendered menu block.
        $menuBlockId = self::MENU_SIDEBAR_BLOCKS['education-center-menu'];
        $menuBlock = Block::load($menuBlockId);

        // Add rendered menu block to our block render array.
        $renderArray['menu_block'] = \Drupal::entityTypeManager()
          ->getViewBuilder('block')->view($menuBlock);

      }

    }
    // If we're on an education article category term page.
    // Or we're on the education center view.
    elseif (
      (
        $term
        && $term->bundle() == 'education_article_category'
      )
      ||(
        $view_id == 'education_center'
        && $view_display == 'education_center'
      )
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
   *
   * @return array
   *   The default wrapper for the block render array.
   */
  private function getBaseRenderArray(MenuLinkContent $menuLink) {

    // Start render array with a container.
    return [
      '#type'       => 'container',
      '#attributes' => [
        'class' => ['sidebar-menu'],
      ],
    ];


  }

}
