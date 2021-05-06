<?php

namespace Drupal\arvestbank_ask_arvest\Plugin\Block;

use Drupal\arvestbank_ask_arvest\Services\AnswersClient;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides suggested questions in a block.
 *
 * @Block(
 *   id = "suggested_questions_block",
 *   admin_label = @Translation("Suggested Questions Block"),
 * )
 */
class SuggestedQuestionsBlock extends BlockBase implements ContainerFactoryPluginInterface {

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

    // Only show suggested questions if we have a search.
    if (isset($_GET['search']) && $_GET['search']) {

      // Get search term.
      $searchTerm = $_GET['search'];

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

      // If we haven't already made an ask query yet this request.
      // This is to avoid duplicate calls showing up in [24]7.ai tracking.
      if (!isset($GLOBALS['ask_query_response'][$searchTerm][$source])) {
        // Get Answers.
        $answers = $this->answersClient->askQuery($searchTerm, $source);
        $GLOBALS['ask_query_response'][$searchTerm][$source] = $answers;
      }
      // If we've already made this ask query this request.
      else {
        $answers = $GLOBALS['ask_query_response'][$searchTerm][$source];
      }

      // If we have suggestions to offer.
      if (isset($answers['suggested']) && count($answers['suggested'])) {

        // Add container and title to returned render array.
        $renderArray = [
          '#type'       => 'container',
          '#attributes' => [
            'class' => [
              'ask-arvest-suggested-questions-container',
              'ask-arvest-sidebar-block',
            ],
          ],
          '#cache'      => [
            'contexts' => [
              'url.query_args:search',
            ],
          ],
          'title' => [
            '#markup' => '<h2>Suggested Questions</h2>',
          ],
          'questions' => [
            '#type'       => 'container',
            '#attributes' => [
              'class' => [
                'ask-arvest-suggested-questions',
              ],
            ],
          ],
        ];

        // Limit to 10 results.
        $answers['suggested'] = array_slice($answers['suggested'], 0, 10);

        // Loop over suggested questions.
        foreach ($answers['suggested'] as $suggested_answer) {
          // Add a link to search this question to the render array.
          // The data-question-id isn't used right now, but could be helpful.
          $renderArray['questions'][] = [
            '#markup' => '<a href="/search?source=2&search='
            . urlencode(strip_tags($suggested_answer['title']))
            . '" data-question-id="' . $suggested_answer['id'] . '">'
            . $suggested_answer['title'] . '</a>',
          ];
        }

      }

    }

    // Build and return form.
    return $renderArray;

  }

}
