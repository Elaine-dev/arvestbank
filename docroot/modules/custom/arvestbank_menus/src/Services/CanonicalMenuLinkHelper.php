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
   *
   * @return bool|mixed
   *   Returns a menu link id or FALSE.
   */
  public function getCanonicalMenuLinkId(int $node_id) {

    // Entity query to get canonical menu link.
    // Based on menu_ui_get_menu_link_defaults().
    $query = \Drupal::entityQuery('menu_link_content')
      ->condition('link.uri', 'entity:node/' . $node_id)
      ->condition('field_canonical_menu_item', 1)
      ->sort('id', 'ASC')
      ->range(0, 1);
    $result = $query->execute();

    // Return a menu link id or false.
    return (!empty($result)) ? reset($result) : FALSE;

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
    ];

  }

}
