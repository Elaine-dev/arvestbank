<?php

namespace Drupal\arvestbank_revisions\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Disallow access to revision delete confirmation page.
    if ($route = $collection->get('node.revision_delete_confirm')) {
      $route->setRequirement('_access', 'FALSE');
    }
  }

}
