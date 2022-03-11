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

    // If we have a source and it's an allowed value.
    if (
      isset($_GET['source'])
      && isset($this->answersClient->allowedSources[$_GET['source']])
    ) {
      // Use the source from the GET variable.
      $source = $_GET['source'];
    }
    // Default to the "manually entered" source.
    else {
      // Indicates manual question.
      $source = 0;
    }

    // Get search term if applicable.
    $searchTerm = '';
    if (isset($_GET['search'])) {
      $searchTerm = $_GET['search'];
    }

    // If a suggested question was used.
    if (isset($_GET['suggestion_id']) && $_GET['suggestion_id'] != '') {
      $answers = $this->answersClient->getResponse($_GET['suggestion_id'], $source);
    }

    // If the question was manually entered or the suggestion id was invalid.
    if (!isset($answers) || !$answers) {
      // If we haven't already made an ask query yet this request.
      // This is to avoid duplicate calls showing up in [24]7.ai tracking.
      if (!isset($GLOBALS['ask_query_response'][$searchTerm][$source])) {
        // Get Answers.
        if (empty($searchTerm)) {
          $searchTerm = 'Have a question? Ask it here.';
        }
        $answers = $this->answersClient->askQuery($searchTerm, $source);
        // Save ask response in globals for use later in the request.
        $GLOBALS['ask_query_response'][$searchTerm][$source] = $answers;
      }
      // If we've already made this ask query this request.
      else {
        $answers = $GLOBALS['ask_query_response'][$searchTerm][$source];
      }
    }

    // Add "Top Question" to render array.
    $renderArray['question_answer']['top_question'] = $this->getTopQuestion($answers);

    // Add "Best Answer" to render array.
    $renderArray['question_answer']['best_answer'] = $this->getBestAnswer($answers);

    // Add wrapper around "Top Question" and "Best Answer".
    $renderArray['question_answer']['#prefix'] = '<dl class="question-and-answer">';
    $renderArray['question_answer']['#suffix'] = '</dl>';

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

    // If there are multiple answers.
    if (isset($answers[0]['id'])) {
      $bestAnswer = $answers[0];
    }
    // If there is not multiple answers.
    else {
      $bestAnswer = $answers;
    }

    // If we have a Best Answer to rate.
    if (isset($bestAnswer['id'])) {

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
            'url.query_args:suggestion_id',
          ],
        ],
      ];

      // Add a title.
      $renderArray['title'] = [
        '#markup' => '<h3>Please rate this response</h3>',
      ];

      // Add a div to use for the rating widget.
      $renderArray['rating_widget_div'] = [
        '#markup' => '<div class="rating-widget" data-id="' . $bestAnswer['id'] . '"></div>',
      ];

      // Add help text.
      $renderArray['help_text'] = [
        '#markup' => '<span class="rating-help-text">Click on a star to rate</span>',
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

    // If there are multiple answers.
    if (isset($answers[0]['id'])) {
      $bestAnswer = $answers[0];
    }
    // If there is not multiple answers.
    else {
      $bestAnswer = $answers;
    }

    // Add best answer label.
    $renderArray['best_answer_label'] = [
      '#type' => 'html_tag',
      '#tag' => 'dt',
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
    if (isset($bestAnswer['body'])) {
      $bestAnswerBody = strip_tags($bestAnswer['body'], '<img><a><b><strong><ul><ol><li><br><p>');
    }
    else {
      $bestAnswerBody = 'We did not find a best response. Please try rephrasing your question. If that doesn\'t help, please call our Customer Service at (866) 952-9523 for assistance.';
    }

    // Add response "body" to the render array.
    $renderArray['best_answer_content'] = [
      '#type' => 'html_tag',
      '#tag' => 'dd',
      '#value' => $bestAnswerBody,
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

    // If we got more than one answer.
    if (isset($answers[0]['id'])) {
      $answer = $answers[0];
    }
    // If we only got one answer.
    else {
      $answer = $answers;
    }

    // Add top question label.
    $renderArray['top_question_label'] = [
      '#type' => 'html_tag',
      '#tag' => 'dt',
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
    if (isset($answer['id'])) {
      $questionText = $answer['title'];
    }
    // If we didn't get a best answer output a message indicating that.
    else {
      $questionText = 'No answer at this time';
    }

    $renderArray['top_question_content'] = [
      '#type' => 'html_tag',
      '#tag' => 'dd',
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
