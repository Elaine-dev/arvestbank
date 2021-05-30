<?php

namespace Drupal\arvestbank_ads\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber for custom tweaks to routes.
 *
 * @package Drupal\arvestbank_ads\Routing
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * Alter routes.
   *
   * @param \Symfony\Component\Routing\RouteCollection $collection
   *   Route collection.
   */
  protected function alterRoutes(RouteCollection $collection) {

    // Allow users that can edit ads to run cron.
    $perm = 'edit any ad content';

    // Route for the admin toolbar.
    if ($route = $collection->get('admin_toolbar.run.cron')) {
      $route->setRequirement('_permission', $perm);
    }

  }

}
