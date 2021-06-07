<?php

namespace Drupal\arvestbank_menus\Services;

use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\arvestbank_menus\Plugin\Block\SidebarMenuBlock;
use Drupal\Core\Database\Database;

/**
 * A service providing canonical menu link helper functions.
 */
class CanonicalMenuLinkHelper {

  /**
   * Gets the canonical link for a given node id.
   *
   * @param int $node_id
   *   The node id to retrieve a canonical link for.
   * @param bool $single_result
   *   Weather to return a single id, or multiple.
   *   There should only be one result unless you're in the midst of switching.
   *
   * @return bool|mixed
   *   Returns a menu link id or FALSE.
   */
  public function getCanonicalMenuLinkIds(int $node_id, bool $single_result) {

    // Entity query to get canonical menu link.
    // Based on menu_ui_get_menu_link_defaults().
    $query = \Drupal::entityQuery('menu_link_content')
      ->condition('link.uri', 'entity:node/' . $node_id)
      ->condition('field_canonical_menu_item', 1)
      ->sort('id', 'ASC');

    // Limit to one result if requested.
    if ($single_result) {
      $query->range(0, 1);
    }

    // Execute Query.
    $result = $query->execute();

    // Return false if no results.
    if (empty($result)) {
      return FALSE;
    }
    else {
      // If a single result was requested return the id only.
      if ($single_result) {
        return reset($result);
      }
      // Return all results.
      else {
        return $result;
      }
    }

  }

  /**
   * Gets link field defaults for use in node form alter.
   *
   * @param int $menuLinkId
   *   The menu link to get defaults for.
   *
   * @return array
   *   The defaults.
   */
  public function getLinkFieldDefaults(int $menuLinkId) {

    $menu_link = MenuLinkContent::load($menuLinkId);
    $menu_link = \Drupal::service('entity.repository')->getTranslationFromContext($menu_link);
    return [
      'entity_id' => $menu_link->id(),
      'id' => $menu_link->getPluginId(),
      'title' => $menu_link->getTitle(),
      'title_max_length' => $menu_link->getFieldDefinitions()['title']->getSetting('max_length'),
      'description' => $menu_link->getDescription(),
      'description_max_length' => $menu_link->getFieldDefinitions()['description']->getSetting('max_length'),
      'menu_name' => $menu_link->getMenuName(),
      'parent' => $menu_link->getParentId(),
      'weight' => $menu_link->getWeight(),
      'menu_parent' => $menu_link->getMenuName() . ':' . $menu_link->getParentId(),
    ];

  }

  /**
   * Sets the canonical link field to true for the given menu link and saves.
   *
   * @param int $menuLinkId
   *   The menu link id to set as the canonical link for the given node.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function makeLinkCanonical(int $menuLinkId) {

    // Load menu link.
    $menuLink = MenuLinkContent::load($menuLinkId);

    // If we loaded the menu link successfully.
    if ($menuLink) {

      // If the menu item doesn't have the canonical field.
      // For some reason new menu items don't have menu_item_extras fields yet.
      if (
        !$menuLink->hasField('field_canonical_menu_item')
        && \Drupal::database()->schema()->tableExists('menu_link_content__field_canonical_menu_item')
      ) {

        // Add the field value manually.
        $query = \Drupal::database()->insert('menu_link_content__field_canonical_menu_item');
        $query->fields([
          'bundle',
          'deleted',
          'entity_id',
          'revision_id',
          'langcode',
          'delta',
          'field_canonical_menu_item_value',
        ]);
        $query->values([
          $menuLink->getMenuName(),
          0,
          $menuLink->id(),
          $menuLink->getRevisionId(),
          $menuLink->language()->getId(),
          0,
          1,
        ]);
        $query->execute();

      }

      // If the menu link has the canonical field value already.
      else {
        // Get current canonical field value.
        $currentCanonicalFieldValue
          = $menuLink->get('field_canonical_menu_item')->getValue();
        // If this menu link isn't already canonical.
        if (
          !isset($currentCanonicalFieldValue[0]['value'])
          || !$currentCanonicalFieldValue[0]['value']
        ) {
          // Set canonical field value.
          $menuLink->set('field_canonical_menu_item', TRUE);
          // Save menu link.
          $menuLink->save();
        }
      }

    }

  }

  /**
   * Called on menu link content entity presave to set others to non-canon.
   *
   * @param int $nodeId
   *   The node id the menu link refers to.
   * @param int $menuLinkToKeepId
   *   The menu link id that's being saved / the one to keep.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function setOtherNodeLinksToNonCanonical(int $nodeId, int $menuLinkToKeepId) {

    // Get all canonical links for this node including one we may have just set.
    $canonicalMenuLinks = $this->getCanonicalMenuLinkIds($nodeId, FALSE);

    // If there are canonical links.
    if ($canonicalMenuLinks) {
      // Loop over canonical links.
      foreach ($canonicalMenuLinks as $canonicalMenuLinkId) {
        // If this isn't the canonical link we're currently setting.
        if ($canonicalMenuLinkId != $menuLinkToKeepId) {
          // Unset that canonical field.
          $menuLinkToMakeNotCannonical = MenuLinkContent::load($canonicalMenuLinkId);
          // Set canonical field value.
          $menuLinkToMakeNotCannonical->set('field_canonical_menu_item', FALSE);
          // Save menu link.
          $menuLinkToMakeNotCannonical->save();
        }
      }
    }

  }

  /**
   * Checks if a given menu link has children.
   *
   * @param \Drupal\menu_link_content\Entity\MenuLinkContent $menuLink
   *   The menu link to check for children.
   *
   * @return bool
   *   Indicates weather or not the menu link has children.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function menuLinkHasChildren(MenuLinkContent $menuLink) {

    // Get menu link content storage.
    $menuContentStorage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
    // Determine what the parent property value would be for children.
    $parentPropertyValue = 'menu_link_content:' . $menuLink->uuid();
    // Get menu links with that parent property.
    $childrenLinks = $menuContentStorage->loadByProperties(['parent' => $parentPropertyValue]);
    // Return boolean indicating weather there are children links.
    return count($childrenLinks) > 0;

  }

  /**
   * Gets the menu link title or override if set for a given menu link.
   *
   * @param \Drupal\menu_link_content\Entity\MenuLinkContent $menuLink
   *   The menu link to get a menu title for.
   *
   * @return string
   *   The title to use for the menu.
   */
  public function getMenuTitleForLink(MenuLinkContent $menuLink) {

    if (
      $menuLink->hasField('field_sidebar_menu_title_overrid')
      && !$menuLink->get('field_sidebar_menu_title_overrid')->isEmpty()
      && isset($menuLink->get('field_sidebar_menu_title_overrid')->getValue()[0]['value'])
    ) {
      return $menuLink->get('field_sidebar_menu_title_overrid')->getValue()[0]['value'];
    }

    return $menuLink->getTitle();

  }

  /**
   * Used to determine if the current route has a sidebar menu.
   *
   * Based largely on logic in SidebarMenuBlock->build().
   *
   * Provides value for token [arvestbank_menus:has_sidebar_menu].
   */
  public function currentRouteHasSidebarMenu() {

    $hasSidebar = FALSE;

    // Get current node, term, or view.
    $node = \Drupal::routeMatch()->getParameter('node');
    $term = \Drupal::routeMatch()->getParameter('taxonomy_term');
    $view_id = \Drupal::routeMatch()->getRouteObject()->getDefault('view_id');
    $view_display = \Drupal::routeMatch()->getRouteObject()->getDefault('display_id');

    // If this is a node route.
    if (isset($node)) {

      // Get canonical menu link.
      $canonicalMenuLinkHelper = \Drupal::service('arvestbank_menus.canonical_menu_link_helper');

      // On revision pages $node is a string.
      if (is_string($node)) {
        $canonicalMenuLinkId = $canonicalMenuLinkHelper->getCanonicalMenuLinkIds($node, TRUE);
      }
      // For non-revision pages $node is an object.
      else {
        $canonicalMenuLinkId = $canonicalMenuLinkHelper->getCanonicalMenuLinkIds($node->id(), TRUE);
      }

      // If this node has a canonical menu link.
      if ($canonicalMenuLinkId) {

        // Load the menu link content entity.
        $canonicalMenuLink = MenuLinkContent::load($canonicalMenuLinkId);
        // Get the menu name the canonical menu link belongs to.
        $canonicalMenuName = $canonicalMenuLink->getMenuName();

        // If we have a sidebar menu block for the canonical menu.
        if (isset(SidebarMenuBlock::MENU_SIDEBAR_BLOCKS[$canonicalMenuName])) {

          // If this isn't a tier one menu item with no children.
          if (
            $canonicalMenuLinkHelper->menuLinkHasChildren($canonicalMenuLink)
            || $canonicalMenuLink->getParentId()
          ) {
            $hasSidebar = TRUE;
          }
        }

      }

      // If this is an education article.
      elseif (
        method_exists($node, 'getType') &&
        $node->getType() == 'article_education_article'
      ) {
        $hasSidebar = TRUE;
      }

    }
    // If this is a term route.
    elseif (
      isset($term)
      && $term->bundle() == 'education_article_category'
    ) {
      $hasSidebar = TRUE;
    }
    // If this is the education center view.
    elseif (
      $view_id == 'education_center'
      && $view_display == 'education_center'
    ) {
      $hasSidebar = TRUE;
    }

    return $hasSidebar;

  }

  /**
   * Mimics non-reliable MenuLinkContent->getParentId().
   *
   * @param int $mlid
   *   The menu link id to get a parent id for.
   *
   * @return bool|array
   *   Returns the parent menu item or FALSE.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getMenuLinkParent(int $mlid) {

    // Entity query to get menu link parent.
    $query = Database::getConnection()
      ->select('menu_link_content_data', 'ml')
      ->fields('ml', ['parent'])
      ->condition('ml.id', $mlid, '=');

    $queryResults = $query->execute()->fetchAll();
    if (count($queryResults)) {
      $menuLinkParentUuid = str_replace('menu_link_content:', '', $queryResults[0]->parent);
      if ($menuLinkParentUuid) {
        $menuLinkParent = \Drupal::entityTypeManager()
          ->getStorage('menu_link_content')
          ->loadByProperties(['uuid' => $menuLinkParentUuid]);
        if ($menuLinkParent && count($menuLinkParent)) {
          return array_pop($menuLinkParent);
        }
      }
    }

    return FALSE;

  }

}
