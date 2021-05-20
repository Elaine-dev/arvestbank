<?php

namespace Drupal\arvestbank_search\EventSubscriber;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Displays status error message for a 404.
 *
 * @package Drupal\arvestbank_search\EventSubscriber
 */
class SearchNotFoundEventSubscriber implements EventSubscriberInterface {

  /**
   * Displays message upon NotFoundHttpException.
   *
   * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
   *   The response for exception event.
   */
  public function onNotFoundException(ExceptionEvent $event) {
    // Displays this message at the top of search results, when there is a 404.
    \Drupal::messenger()->addError('We were unable to find the page you requested.');
  }

  /**
   * Registers the methods in this class that should be listeners.
   *
   * @return array
   *   An array of event listener definitions.
   */
  public static function getSubscribedEvents(): array {
    $events[KernelEvents::EXCEPTION][] = ['onNotFoundException', 0];
    return $events;
  }

}
