<?php

namespace Drupal\arvestbank_wordpress_articles\Services;

use Drupal\Component\Serialization\Json;
use Drupal\views\Views;
use GuzzleHttp\Client;

/**
 * A service to facilitate fetching articles from share.arvest.com.
 */
class WordpressArticlesService {

  /**
   * Defines the category titles and endpoints to use for category summary.
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
   * Defines the category titles and endpoints to use for tag summary.
   *
   * @var array
   */
  const SUMMARY_TAGS = [
    'News' => 'https://share.arvest.com/tag/news/feed/json',
    'Community' => 'https://share.arvest.com/tag/community/feed/json',
    'Spin On Spending' => 'https://share.arvest.com/tag/spin-on-spending/feed/json',
    'Business' => 'https://share.arvest.com/tag/business/feed/json',
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
  public function getRenderArray(string $title, string $endpoint, int $limit = 0, string $display = 'categories') {

    // Instantiate render array to return.
    $renderArray = [];

    // If we're not showing the summary and we have an entered endpoint.
    if ($endpoint && $display == 'endpoint') {
      // Get results from client entered endpoint.
      $articles = $this->getArticles($endpoint, $limit);
    }

    // If we're showing a summary.
    if (
      $display == 'categories'
      || $display == 'tags'
    ) {

      // Decide on endpoints in summary.
      $summaryEndpoints = $this::SUMMARY_CATEGORIES;
      if ($display == 'tags') {
        $summaryEndpoints = $this::SUMMARY_TAGS;
      }

      // Loop over categories compiling list of articles, one from each.
      $articles = [];
      foreach ($summaryEndpoints as $summaryCategoryTitle => $summaryCategoryEndpoint) {
        // Get articles.
        $categoryArticles = $this->getArticles($summaryCategoryEndpoint, 1);
        if (count($categoryArticles)) {
          $articles[$summaryCategoryTitle] = array_pop($categoryArticles);
        }
      }

    }

    // Get the News Alert.
    $news_alert = $this->getNewsAlert();

    // Output the block header if we have an alert or articles.
    if (!empty($news_alert) || !empty($articles)) {

      // Add base render array with container and title.
      $renderArray[] = [
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

    }

    // Add in the alert if there is one.
    if (!empty($news_alert)) {
      $renderArray[] = $news_alert;
    }

    // Only output anything if we got results.
    if (!empty($articles)) {

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
   * Returns a render array of the news alert block.
   *
   * @return array
   *   News alert render array.
   */
  private function getNewsAlert() : array {

    // Initialize the render array.
    $news_alert = [];

    // See first if there is an alert.
    $alert_view = Views::getView('stage_page_sidebar_news_alert');
    $alert_view->setDisplay('default');
    $alert_view->execute();

    // If there is an active alert embed the view.
    if (!empty($alert_view->result)) {
      $alert = views_embed_view('stage_page_sidebar_news_alert', 'default');
      $news_alert['alert'] = $alert;
      $news_alert['alert']['#prefix'] = '<div class="coh-style-alert">';
      $news_alert['alert']['#suffix'] = '</div>';
    }

    // Return the news alert render array.
    return $news_alert;

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
