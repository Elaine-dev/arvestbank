<?php

namespace Drupal\arvestbank_core\EventSubscriber;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    $current_url = Url::fromRoute('<current>');
    $path = $current_url->toString();

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

    // If user makes a selection the deselects without a new selection then
    // redirect to view without query arguments in url.
    $invalid_paths = [
      '/personal/invest/trust-and-estate-services/find-a-trust-professional?field_location_value=&title=&source=&suggestion_id=',
      '/personal/invest/find-a-client-advisor?field_location_value=&title=&source=&suggestion_id=',
      '/personal/borrow/home-loans/servicing-center/find-a-lender?field_location_value=&title=&source=&suggestion_id='
    ];

    if (in_array($requested_uri, $invalid_paths)) {
      // Create redirect:
      $response = new RedirectResponse($path);
      $response->send();
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
