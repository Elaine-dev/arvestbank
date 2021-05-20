<?php

namespace Drupal\arvestbank_media_alias\Routing;

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

    if ($route = $collection->get('entity.media.canonical')) {
      $route->setDefaults([
        '_controller' => '\Drupal\arvestbank_media_alias\Controller\DisplayController::view',
      ]);
    }
  }

}
