<?php

namespace Drupal\arvestbank_core\EventSubscriber;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class EntityTypeSubscriber.
 *
 * @package Drupal\carlylegirms_clickthrough\EventSubscriber
 */
class AssociatesRedirect implements EventSubscriberInterface {

  /**
   * This method is called whenever the kernel.request event is dispatched.
   *
   * @param Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The response event.
   */
  public function kernelRequest(RequestEvent $event) {
    $request = $event->getRequest();
    $requested_uri = $request->getRequestUri();

    if (str_contains($requested_uri, '.html')) {

      // Check if  associate username.
      $username_from_path = substr($requested_uri, 0, strpos($requested_uri, "."));
      $username = str_replace('/','', $username_from_path);

      $query = \Drupal::entityQuery('node')
        ->condition('type', 'associate')
        ->condition('field_username', $username);
      $results = $query->execute();

      // Only redirect if the path is a associates username.
      if ($results != NULL) {
        // Create the destination URL.
        $url = 'https://www.arvesthomeloan.com' . $requested_uri;

        // Create redirect:
        $response = new TrustedRedirectResponse($url);
        $response->send();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[KernelEvents::REQUEST][] = ['kernelRequest', 28];
    return $events;
  }

}
