<?php

namespace Drupal\arvestbank_wordpress_articles\Services;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\Client;

/**
 * A service to facilitate fetching articles from share.arvest.com.
 */
class WordpressArticlesService {

  /**
   * Defines the category titles and endpoints to use for summary.
   *
   * @var array
   */
  const SUMMARY_CATEGORIES = [
    'News' => 'https://share.arvest.com/category/newsroom/feed/json',
    'Community' => 'https://share.arvest.com/category/community/feed/json',
    'Spin On Spending' => 'https://share.arvest.com/category/spin-on-spending/feed/json',
    'Business' => 'https://share.arvest.com/category/business/feed/json',
  ];

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
  public function getRenderArray(string $title, string $endpoint, int $limit = 0, bool $showSummary = FALSE) {

    // Instantiate render array to return.
    $renderArray = [];

    // If we're not showing the summary and we have an entered endpoint.
    if ($endpoint && !$showSummary) {
      // Get results from client entered endpoint.
      $articles = $this->getArticles($endpoint, $limit);
    }

    // If we're showing a summary.
    if ($showSummary) {
      // Loop over categories compiling list of articles, one from each.
      $articles = [];
      foreach ($this::SUMMARY_CATEGORIES as $summaryCategoryTitle => $summaryCategoryEndpoint) {
        // Get articles.
        $categoryArticles = $this->getArticles($summaryCategoryEndpoint, 1);
        if (count($categoryArticles)) {
          $articles[$summaryCategoryTitle] = array_pop($categoryArticles);
        }
      }
    }

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

        // Determine render array index for article.
        $articleRenderArrayIndex = 'article-' . str_replace(' ', '_', strtolower($articleIndex));

        // Add link container and link tags container to render array.
        $renderArray[$articleRenderArrayIndex] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => [
              'wordpress-article-' . $articleRenderArrayIndex,
            ],
          ],
        ];

        // Add Category to Render array if applicable.
        if (!is_numeric($articleIndex)) {
          $renderArray[$articleRenderArrayIndex]['category'] = [
            '#markup' => '<span class="wordpress-category">' . $articleIndex . '</span>',
          ];
        }

        // Add link to render array.
        $renderArray[$articleRenderArrayIndex]['link'] = [
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
