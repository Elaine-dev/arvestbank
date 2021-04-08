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
class RateAnswer extends ControllerBase {

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
   */
  public function rateAnswer($answer, $rating, Request $request) {

    // Build initial response array.
    $response = [
      'answer' => $answer,
    ];

    // Determine rating out of 100 from 1-5.
    $ratingMapping = [
      1 => 0,
      2 => 25,
      3 => 50,
      4 => 75,
      5 => 100,
    ];
    $rating = $ratingMapping[$rating];
    $response['rating'] = $rating;

    // Determine the question text if set.
    $question = isset($_GET['search']) ? $_GET['search'] : NULL;

    // Add question to response array.
    $response['question'] = $question;

    // Determine the "source" for the question.
    if (isset($_GET['suggestion']) && $_GET['suggestion']) {
      $response['source'] = "Suggested";
      $source = 2;
    }
    else {
      $response['source'] = "General";
      $source = 0;
    }

    // Make rating request.
    $apiResponse = $this->answersClient->rateAnswer($answer, $rating, $question, $source);

    // Add response code from API to our json response.
    $response['response_code'] = $apiResponse->getStatusCode();

    // Add response body to response.
    $response['response_body'] = $apiResponse->getBody()->__toString();

    // Log an error if the rating request seems to have failed.
    if (
      $response['response_code'] != 200
    ) {
      \Drupal::logger('arvestbank_ask_arvest')
        ->error('Unsuccessful call to answer rating API. Details: ' . print_r($response, 1));
    }

    return new JsonResponse($response);

  }

}
