<?php

namespace Drupal\arvestbank_ask_arvest\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\arvestbank_ask_arvest\Services\AnswersClient;

/**
 * Provides block to show the "Top Question" related to any search.
 *
 * @Block(
 *   id = "ask_arvest_top_question_block",
 *   admin_label = @Translation("Ask Arvest Top Question Block"),
 * )
 */
class AskArvestTopQuestionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The [24]7.ai Answers API Wrapper.
   *
   * @var \Drupal\arvestbank_ask_arvest\Services\AnswersClient
   */
  private $answersClient;

  /**
   * Constructor for block plugin.
   *
   * @param array $configuration
   *   Default for block plugin.
   * @param string $plugin_id
   *   Default for block plugin.
   * @param mixed $plugin_definition
   *   Default for block plugin.
   * @param \Drupal\arvestbank_ask_arvest\Services\AnswersClient $answers_client
   *   Answers client.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AnswersClient $answers_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->answersClient = $answers_client;
  }

  /**
   * Handles autoloading.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   Default for block plugin.
   * @param array $configuration
   *   Default for block plugin.
   * @param string $plugin_id
   *   Default for block plugin.
   * @param mixed $plugin_definition
   *   Default for block plugin.
   *
   * @return static
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

    // Instantiate render array to return.
    $renderArray = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ask-arvest-block'],
      ],
    ];

    // Determine if this question is from a suggestion.
    if (
      isset($_GET['suggestion'])
      && $_GET['suggestion']
    ) {
      // Indicates suggested question.
      $source = 2;
    }
    else {
      // Indicates manual question.
      $source = 0;
    }

    // Get Answers.
    $answers = $this->answersClient->askQuery($_GET['search'], $source);

    // Add "Top Question" to render array.
    $renderArray['top_question'] = $this->getTopQuestion($answers);

    // Add "Best Answer" to render array.
    $renderArray['best_answer'] = $this->getBestAnswer($answers);

    // Add Rating Widget.
    $renderArray['rating_widget'] = $this->getRatingWidget($answers);

    return $renderArray;

  }

  /**
   * Creates a render array for a "Best Answer" rating widget.
   *
   * @param array $answers
   *   Response from SOAP Answers "ask" API.
   *
   * @return mixed
   *   Render array.
   */
  private function getRatingWidget(array $answers) {

    // If we have a Best Answer to rate.
    if (isset($answers['id'])) {

      // Attach "raty" rating js library.
      $renderArray = [
        '#type'       => 'container',
        '#attributes' => [
          'class' => ['ask-arvest-rating'],
        ],
        '#attached'   => [
          'library' => [
            'arvestbank_ask_arvest/raty',
          ],
        ],
        '#cache'      => [
          'contexts' => [
            'url.query_args:search',
          ],
        ],
      ];

      // Add a div to use for the rating widget.
      $renderArray['rating_widget_div'] = [
        '#markup' => '<div class="rating-widget" data-id="' . $answers['id'] . '"></div>',
      ];

      return $renderArray;
    }

    return [];

  }

  /**
   * Creates a render array for "Best Answer".
   *
   * @param array $answers
   *   Response from SOAP Answers "ask" API.
   *
   * @return mixed
   *   Render array.
   */
  private function getBestAnswer(array $answers) {

    // Add best answer label.
    $renderArray['best_answer_label'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => $this->t('Best Answer:'),
      '#attributes' => [
        'class' => [
          'ask-arvest-label ask-arvest-best-answer-label',
        ],
      ],
      '#cache' => [
        'contexts' => [
          'url.query_args:search',
        ],
      ],
    ];

    // The body contains a no results message.
    // Only providing a fallback in case SOAP request failed.
    if (isset($answers['body'])) {
      $bestAnswer = strip_tags($answers['body']);
    }
    else {
      $bestAnswer = 'We did not find a best response. Please try rephrasing your question. If that doesn\'t help, please call our Customer Service at (866) 952-9523 for assistance.';
    }

    // Add response "body" to the render array.
    $renderArray['best_answer_content'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => $bestAnswer,
      '#attributes' => [
        'class' => [
          'ask-arvest-value ask-arvest-best-answer-value',
        ],
      ],
      '#cache' => [
        'contexts' => [
          'url.query_args:search',
        ],
      ],
    ];

    return $renderArray;

  }

  /**
   * Creates a render array for "Top Question".
   *
   * @param array $answers
   *   Response from SOAP Answers "ask" API.
   *
   * @return array
   *   Render array.
   */
  private function getTopQuestion(array $answers) {

    // Add top question label.
    $renderArray['top_question_label'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => $this->t('Top Question:'),
      '#attributes' => [
        'class' => [
          'ask-arvest-label ask-arvest-top-question-label',
        ],
      ],
      '#cache' => [
        'contexts' => [
          'url.query_args:search',
        ],
      ],
    ];

    // If we got a top Question.
    if (isset($answers['id'])) {
      $questionText = $answers['title'];
    }
    // If we didn't get a best answer output a message indicating that.
    else {
      $questionText = 'No answer at this time';
    }

    $renderArray['top_question_content'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => $questionText,
      '#attributes' => [
        'class' => [
          'ask-arvest-value ask-arvest-top-question-value',
        ],
      ],
      '#cache' => [
        'contexts' => [
          'url.query_args:search',
        ],
      ],
    ];

    return $renderArray;

  }

}
