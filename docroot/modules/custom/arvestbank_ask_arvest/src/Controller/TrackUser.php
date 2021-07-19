<?php

namespace Drupal\arvestbank_ask_arvest\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\arvestbank_ask_arvest\Services\AnswersClient;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Defines a route controller for rating an answer.
 */
class TrackUser extends ControllerBase {

  /**
   * The answers API wrapper.
   *
   * @var \Drupal\arvestbank_ask_arvest\Services\AnswersClient
   */
  protected $answersClient;

  /**
   * {@inheritdoc}
   */
  public function __construct(AnswersClient $answersClient) {
    $this->answersClient = $answersClient;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    // Load the answers API wrapper.
    $answersClient = $container->get('arvestbank_ask_arvest.answers_client');
    return new static($answersClient);

  }

  /**
   * Handler for rate answer request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   */
  public function trackUser(Request $request) {

    // Response just for debugging.
    $responseBody = [
      'already_in_cookie' => TRUE,
    ];

    // If we don't already have a tracking id for this user.
    if (!isset($_COOKIE['ask_arvest_session_id'])) {

      // Make request for tracking id.
      $sessionId = $this->answersClient->getSessionId();
      // Create cookie with tracking id.
      $trackingIdCookie = new Cookie('ask_arvest_session_id', $sessionId);
      // Indicate that the session id wasn't already in session.
      $responseBody['already_in_cookie'] = FALSE;
      // Put the session id in the response.
      $responseBody['session_id'] = $sessionId;

    }
    // If we already have the session id.
    else {
      // Add session id to the response.
      $responseBody['session_id'] = $_COOKIE['ask_arvest_session_id'];
    }

    // Create response object.
    $response = new JsonResponse($responseBody);

    // If we have a new tracking cookie to set.
    if (isset($trackingIdCookie)) {
      // Set the cookie.
      $response->headers->setCookie($trackingIdCookie);
    }


    return $response;

  }

}
