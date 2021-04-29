<?php

namespace Drupal\arvestbank_wordpress_articles\Services;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\Client;

/**
 * A service to facilitate fetching articles from share.arvest.com.
 */
class WordpressArticlesService {

  /**
   * Http client used to call APIs.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Constructs a new AnswersClient object.
   *
   * @param \GuzzleHttp\Client $httpClient
   *   A drupal http client factory.
   */
  public function __construct(Client $httpClient) {
    // Store http client.
    $this->httpClient = $httpClient;
  }

  /**
   * Get a render array showing feed results.
   */
  public function getRenderArray(string $title, string $endpoint, int $limit = 0){

    // Instantiate render array to return.
    $renderArray = [];

    // Get results from endpoint.
    $articles = $this->getArticles($endpoint, $limit);

    // Only output anything if we got results.
    if ($articles) {

      // Add base render array with container and title.
      $renderArray = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'wordpress-articles-container',
          ],
        ],
        'title' => [
          '#markup' => '<h3>' . $title . '</h3>',
        ],
      ];

      // Loop over articles.
      foreach ($articles as $articleIndex => $article) {

        // Add link to render array.
        $renderArray['article_' . $articleIndex] = [
          '#markup' => '<a class="wordpress-article-link" href="' . $article['url'] . '">' . $article['title'] . '</a>',
        ];

      }

    }

    return $renderArray;

  }

  /**
   * Fetches and json decodes response from endpoint.
   *
   * @param string $endpoint
   *   A json endpoint containing articles.
   * @param int $limit
   *   Optionally limit the returned results.
   *
   * @return mixed
   *   The json decoded response from the endpoint.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getArticles(string $endpoint, int $limit = 0) {

    try {

      // Make request.
      $request = $this->httpClient->request(
        'GET',
        $endpoint
      );

      // Get response body.
      $data = Json::decode($request->getBody());

    }
    // If we encountered a problem.
    catch (Exception $e) {
      // Log error and return.
      \Drupal::logger('arvestbank_wordpress_articles')->error('Failed to load or parse json from ' . $endpoint);
      return FALSE;
    }

    // If we have results to return.
    if (
      isset($data['items'])
      && count($data['items'])
    ) {

      // If we need to limit our results.
      if (count($data['items']) > $limit && $limit) {
        // Limit the results.
        $data['items'] = array_slice($data['items'], 0, $limit);
      }

      // Return the results.
      return $data['items'];
    }

    // Return false if we didn't get results.
    return FALSE;

  }

}
