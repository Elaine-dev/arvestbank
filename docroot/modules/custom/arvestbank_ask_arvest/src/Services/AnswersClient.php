<?php

namespace Drupal\arvestbank_ask_arvest\Services;

use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Drupal\Component\Serialization\Json;

/**
 * A service to facilitate "[24]7 AI" Answers API integration.
 */
class AnswersClient {

  /**
   * Helper to load config.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Config for this module.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $askArvestConfig;

  /**
   * Http client used to call APIs.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The sources we're allowing for queries.
   *
   * The key is the opperative part, the value is for reference.
   *
   * see https://engage.247.ai/docportal/Content/Answers/APIs/Set-Up-Question-Completion-Own-Servers.htm?Highlight=typeId
   *
   * @var array
   */
  public $allowedSources = [
    '0' => 'Manually entered question',
    '2' => 'From "Suggested Questions" block.',
    '8' => 'From autocomplete functionality.',
  ];

  /**
   * Constructs a new AnswersClient object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Helper to load load config.
   * @param \GuzzleHttp\Client $httpClient
   *   A drupal http client factory.
   */
  public function __construct(ConfigFactory $configFactory, Client $httpClient) {

    // Store config factory for later use.
    $this->configFactory = $configFactory;

    // Load and store config for this module.
    $this->askArvestConfig = $configFactory->get('arvestbank_ask_arvest.settings');

    // Store http client.
    $this->httpClient = $httpClient;

  }

  /**
   * Gets top questions from [24]7.ai Answers REST "GET TOP QUESTIONS".
   */
  public function getTopQuestions() {

    // Determine endpoint to request.
    $topQuestionEndpoint = $this->askArvestConfig->get('general_rest_api_endpoint');
    $topQuestionEndpoint .= '?interfaceID=2&sessionId=' . $this->getUserSessionId();
    $topQuestionEndpoint .= '&requestType=TopQuestionsRequest';

    try {
      // Make request.
      // The interfaceID and requestType could be made config if needed.
      $request = $this->httpClient->request(
        'GET',
        $topQuestionEndpoint
      );
      // Get response body.
      $data = $request->getBody();
      // Decode and return the json.
      return Json::decode($data);
    }
    catch (RequestException $e) {
      // Log error.
      \Drupal::logger('arvestbank_ask_arvest')->error($e->getMessage());
      // Return empty array.
      return [];
    }

  }

  /**
   * Gets suggestions from the intellisuggest endpoint.
   *
   * Uses the REST API "IntelliSuggest" endpoint.
   *
   * @param string $userInput
   *   String to provide autocomplete suggestions for.
   *
   * @return array
   *   Result from endpoint json decoded.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function intellisuggestQuery(string $userInput) {

    // Sanitize user input for use in url.
    $userInput = filter_var($userInput, FILTER_SANITIZE_URL);

    // Get intellisuggest endpoint.
    $intellisuggestEndpoint = $this->askArvestConfig->get('intellisuggest_endpoint');


    // Make request.
    try {
      // Make request.
      // The interfaceID and requestType could be made config if needed.
      $request = $this->httpClient->request(
        'GET',
        $intellisuggestEndpoint
              . '?interfaceID=2&requestType=8&term=' . $userInput
              . '&sessionId='.$this->getUserSessionId()
      );
      // Get response body.
      $data = $request->getBody();
      // Decode and return the json.
      return Json::decode($data);
    }
    catch (RequestException $e) {
      // Log error.
      \Drupal::logger('arvestbank_ask_arvest')->error($e->getMessage());
      // Return empty array.
      return [];
    }

  }

  /**
   * Queries the SOAP "ask" endpoint for answers.
   *
   * @param $question
   *   The question to get answers for.
   * @param int $typeId
   *   Same as "source" from REST API, whence the question came.
   *   @see https://engage.247.ai/docportal/Content/Answers/APIs/Set-Up-Question-Completion-Own-Servers.htm?Highlight=typeId
   *
   * @return mixed
   */
  public function askQuery($question, $typeId = 0) {

    // Get soap endpoint from config.
    $intelliresponseEndpoint = $this->askArvestConfig->get('intelliresponse_soap_endpoint');

    // Create Soap Client.
    $soapClient = new \nusoap_client($intelliresponseEndpoint);
    $soapClient->soap_defencoding = 'UTF-8';
    $soapClient->decode_utf8 = FALSE;

    // Create object containing arguments.
    $requestArguments = [
      'interfaceId' => 2,
      'question' => $question,
      'channelId' => 0,
      'typeId' => $typeId,
      'sessionId' => $this->getUserSessionId(),
    ];

    // Make request and return result.
    // The third argument is xml namespace and is a misnomer on [24]7.ai's side.
    return $soapClient->call('ask', $requestArguments, '/com/intelliresponse/search/user', '');

  }

  /**
   * Sends a request to the [24]7.ai Answers REST endpoint for rating.
   *
   * @param string $answerId
   *   The answer id to rate.
   * @param int $rating
   *   The rating for the answer.
   * @param string $question
   *   The "question" entered into search.
   * @param int $source
   *   Where the question came from.
   *   0 - Manual
   *   1 - Related
   *   2 - Suggested
   *   3 - Top Questions
   *   4 - Embedded
   *   ...
   *   8 - Question completion
   *   @see https://engage.247.ai/docportal/Content/Answers/APIs/Set-Up-Question-Completion-Own-Servers.htm?Highlight=typeId
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   The response returned from the API.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function rateAnswer(string $answerId, int $rating, string $question = NULL, int $source = 0) {

    // Get REST endpoint from config.
    $intelliresponseEndpoint = $this->askArvestConfig->get('general_rest_api_endpoint');

    // Build rating request.
    // phpcs:disable
    $ratingRequestUrl = $intelliresponseEndpoint
      . '?interfaceID=2'
      . '&sessionId=' . $this->getUserSessionId()
      . '&requestType=RatingRequest'
      . '&responseID=' . $answerId
      . '&uuid='
      . '&rating=' . $rating
      . '&source=' . $source
      . '&question=' . $question;
    // phpcs:enable

    // Make request and return response.
    return $this->httpClient->request(
      'GET',
      $ratingRequestUrl
    );

  }

  /**
   * Create a [24]7.ai Answers API session id for a user.
   * Categories enumerated at /json/answers.jsp?interfaceID=2&requestType=CategoryRequest.
   */
  public function getSessionId(){

    // Get REST endpoint from config.
    $intelliresponseEndpoint = $this->askArvestConfig->get('general_rest_api_endpoint');

    // Add request vars to endpoint.
    // It's unclear if category is needed, the documentation has internal inconsistencies.
    $intelliresponseEndpoint .= 'answers.jsp?interfaceID=2&category=132';

    // Make request and return session id.
    // There is also a user id returned, but we don't currently have a use.
    try {
      $response = $this->httpClient->request('GET', $intelliresponseEndpoint)->getBody()->__toString();
      $response = json_decode($response);
      return $response->sessionID;
    }
    catch (RequestException $e) {
      // Log error.
      \Drupal::logger('arvestbank_ask_arvest')->error($e->getMessage());
      // Return false.
      return FALSE;
    }



  }

  /**
   * Helper function to avoid code duplication.
   *
   * @return string|null
   */
  private function getUserSessionId() {
    // Get session id for user tracking.
    $sessionId = NULL;
    if (isset($_SESSION['ask_arvest_session_id'])) {
      $sessionId = $_SESSION['ask_arvest_session_id'];
    }
    return $sessionId;
  }

}
