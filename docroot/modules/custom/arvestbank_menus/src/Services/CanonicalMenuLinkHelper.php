<?php

namespace Drupal\arvestbank_menus\Services;

use Drupal\menu_link_content\Entity\MenuLinkContent;

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

      // Get current canonical field value.
      $currentCanonicalFieldValue = $menuLink->get('field_canonical_menu_item')->getValue();
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

}
