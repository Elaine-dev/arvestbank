<?php

namespace Drupal\arvestbank_menus;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\Core\Menu\MenuActiveTrail;
use Drupal\Core\Menu\MenuLinkManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\arvestbank_menus\Services\CanonicalMenuLinkHelper;
use Drupal\Core\Menu\MenuTreeStorage;

/**
 * By default the active menu link is just the first menu link created.
 *
 * That's no good, this sets it to our canonical link.
 *
 * @package Drupal\arvestbank_menus
 */
class ArvestbankMenusActiveTrail extends MenuActiveTrail {

  /**
   * Canonical menu link helper.
   *
   * @var \Drupal\arvestbank_menus\Services\CanonicalMenuLinkHelper
   */
  protected $canonicalMenuLinkHelper;

  /**
   * {@inheritdoc}
   */
  public function __construct(MenuLinkManagerInterface $menu_link_manager, RouteMatchInterface $route_match, CacheBackendInterface $cache, LockBackendInterface $lock, CanonicalMenuLinkHelper $canonicalMenuLinkHelper) {
    parent::__construct($menu_link_manager, $route_match, $cache, $lock);
    $this->canonicalMenuLinkHelper = $canonicalMenuLinkHelper;
  }

  /**
   * Helper method for ::getActiveTrailIds().
   */
  protected function doGetActiveTrailIds($menu_name) {
    // Parent ids; used both as key and value to ensure uniqueness.
    // We always want all the top-level links with parent == ''.
    $active_trail = ['' => ''];

    // If a link in the given menu indeed matches the route, then use it to
    // complete the active trail.
    if ($active_link = $this->getActiveLink($menu_name)) {
      if ($parents = $this->menuLinkManager->getParentIds($active_link->getPluginId())) {
        $active_trail = $parents + $active_trail;
      }
    }

    return $active_trail;
  }

  /**
   * {@inheritdoc}
   */
  public function getActiveLink($menu_name = NULL) {

    // If we're on a node page.
    $node = \Drupal::routeMatch()->getParameter('node');

    if ($node) {

      // On revision pages $node is a string.
      if (is_string($node)) {
        // Get canonical link for this node.
        $canonicalLinkId = $this->canonicalMenuLinkHelper->getCanonicalMenuLinkIds($node, TRUE);
      }
      // For non-revision pages $node is an object.
      else {
        // Get canonical link for this node.
        $canonicalLinkId = $this->canonicalMenuLinkHelper->getCanonicalMenuLinkIds($node->id(), TRUE);
      }

      // If we have a canonical link for this node.
      if ($canonicalLinkId) {

        // Copied from default implementation to load all links for this route.
        $route_name = $this->routeMatch->getRouteName();
        if ($route_name) {
          $route_parameters = $this->routeMatch->getRawParameters()->all();
          // If viewing a latest revision we need to load links based on the
          // canonical node.
          if ($route_name == 'entity.node.latest_version' || $route_name == 'entity.node.revision') {
            $route_name = 'entity.node.canonical';
            $route_parameters['node'] = $node;
            if ($route_parameters['node_revision']) {
              unset($route_parameters['node_revision']);
            }
          }
          // Load links matching this route.
          $links = $this->menuLinkManager->loadLinksByRoute($route_name, $route_parameters, $menu_name);

          // Loop over links for this route.
          foreach ($links as $link) {
            // If this is the canonical link.
            if (
              isset($link->getPluginDefinition()['metadata']['entity_id'])
              && $link->getPluginDefinition()['metadata']['entity_id'] == $canonicalLinkId
            ) {
              // Return the link object.
              return $link;
            }
          }

        }
      }
    }

    // If there's no canonical let the default functionality handle it.
    return parent::getActiveLink($menu_name);

  }

}
