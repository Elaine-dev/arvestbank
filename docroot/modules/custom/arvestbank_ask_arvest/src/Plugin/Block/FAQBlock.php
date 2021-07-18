<?php

namespace Drupal\arvestbank_ask_arvest\Plugin\Block;

use Drupal\arvestbank_ask_arvest\Services\AnswersClient;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides FAQ block.
 *
 * @Block(
 *   id = "faq_block",
 *   admin_label = @Translation("Most Frequently Asked Questions Block"),
 * )
 */
class FAQBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Service providing connectivity to [24]7.ai APIs.
   *
   * @var \Drupal\arvestbank_ask_arvest\Services\AnswersClient
   */
  protected $answersClient;

  /**
   * Constructor for this Block Plugin Class.
   *
   * @param array $configuration
   *   Configuration.
   * @param string $plugin_id
   *   Plugin Id.
   * @param mixed $plugin_definition
   *   Plugin Definition.
   * @param \Drupal\arvestbank_ask_arvest\Services\AnswersClient $answers_client
   *   Service providing connectivity to [24]7.ai APIs.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AnswersClient $answers_client) {

    // Run standard block plugin construct method.
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    // Store our service for use in build().
    $this->answersClient = $answers_client;

  }

  /**
   * Allows class to be instantiated with services.
   *
   * @inheritDoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('arvestbank_ask_arvest.answers_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Instantiate render array.
    $renderArray = [];

    // Get top questions from the [24]7.ai "Answers Top Questions" endpoint.
    $topQuestions = $this->answersClient->getTopQuestions();

    // If we have top questions.
    if (isset($topQuestions['topQuestions']) && count($topQuestions['topQuestions'])) {

      $renderArray = [
        '#type'       => 'container',
        '#attributes' => [
          'class' => [
            'ask-arvest-top-questions-container',
            'ask-arvest-sidebar-block',
          ],
        ],
        '#cache'      => [
          'max-age' => 86400,
        ],
        'title' => [
          '#markup' => '<h2>Most Frequently Asked Questions</h2>',
        ],
        'questions' => [
          '#type'       => 'container',
          '#attributes' => [
            'class' => [
              'ask-arvest-top-questions',
            ],
          ],
        ],
      ];

      // Limit to 10 results.
      $topQuestions['topQuestions'] = array_slice($topQuestions['topQuestions'], 0, 10);

      foreach ($topQuestions['topQuestions'] as $topQuestion) {
        // Add a link to search this question to the render array.
        // The data-question-id isn't used right now, but could be helpful.
        $renderArray['questions'][] = [
          '#markup' => '<a href="/search?source=3&search='
          . urlencode(strip_tags($topQuestion['title']))
          . '" data-question-id="' . $topQuestion['id'] . '">'
          . $topQuestion['title'] . '</a>',
        ];
      }

    }

    // Build and return form.
    return $renderArray;

  }

  /**
   * Cache these for a day.
   *
   * @inheritDoc
   */
  public function getCacheMaxAge() {
    // Cache for a day (assuming this is seconds).
    return 86400;
  }

}
