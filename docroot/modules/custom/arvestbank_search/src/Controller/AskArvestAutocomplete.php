<?php

namespace Drupal\arvestbank_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\arvestbank_ask_arvest\Services\AnswersClient;

/**
 * Defines a route controller for watches autocomplete form elements.
 */
class AskArvestAutocomplete extends ControllerBase {

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
   * Handler for autocomplete request.
   */
  public function handleAutocomplete(Request $request) {

    // Instantiate results array.
    $results = [];

    // Get user input.
    $userInput = $request->query->get('q');

    // Query "[24]7 AI" Answers API Intellisuggest endpoint.
    $rawSuggestions = $this->answersClient->intellisuggestQuery($userInput);

    // Loop over raw suggestions.
    foreach ($rawSuggestions as $rawSuggestion) {
      // Add to autocomplete suggestions.
      $results[] = [
        'value' => strip_tags($rawSuggestion['label']),
        'label' => $rawSuggestion['label'],
      ];
    }

    // Return autocomplete suggestions.
    return new JsonResponse($results);

  }

}
