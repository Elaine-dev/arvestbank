<?php

namespace Drupal\arvestbank_ask_arvest\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\arvestbank_ask_arvest\Services\AnswersClient;

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
    $response = [
      'already_in_session' => TRUE,
    ];

    // If we don't already have a tracking id for this user.
    if (!isset($_SESSION['ask_arvest_session_id'])) {

      // Make request for tracking id.
      $sessionId = $this->answersClient->getSessionId();

      // Set tracking id in session variable.
      $_SESSION['ask_arvest_session_id'] = $sessionId;

      // Indicate that the session id wasn't already in session.
      $response['already_in_session'] = FALSE;

    }

    // Add session id to the response.
    $response['session_id'] = $_SESSION['ask_arvest_session_id'];


    return new JsonResponse($response);

  }

}
