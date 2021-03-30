<?php

namespace Drupal\arvestbank_calculators\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\HtmlResponse;
use Drupal\Core\Render\RendererInterface;
use Drupal\node\Entity\Node;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Renders a calculator in a complete page/response.
 */
class CalculatorController extends ControllerBase {

  /**
   * Constructs a ControllerBase object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger channel.
   */
  public function __construct(RendererInterface $renderer, LoggerInterface $logger) {
    $this->renderer = $renderer;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('logger.factory')->get('node')
    );
  }

  /**
   * Renders a calculator as a full HtmlResponse.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   *   Will be thrown if the 'hash' parameter does not match the expected hash
   *   of the 'url' parameter.
   */
  public function render(Request $request) {

    // Set up the response.
    $response = new HtmlResponse();

    // Initialize the html for the response.
    $html = ['#type' => 'html'];

    // Get the node id from the URL.
    $nid = (int) \Drupal::request()->query->get('nid');

    // Try to load the node off of the ID.
    if ($node = Node::load($nid)) {

      // Content type = calculators.
      if ($node->bundle() == 'calculators') {

        // Type is the formatter.
        $display_settings = [
          'label' => 'hidden',
          'type' => 'arvestbank_calculators_embed',
        ];
        // Render array of the calculator field.
        $element = $node->get('field_calculator')->view($display_settings);

        // Populate the page.
        $html['page'] = [
          '#type' => 'page',
          'content' => $element,
        ];

        // Renderer service to render the html.
        \Drupal::service('renderer')->renderRoot($html);
        $response->setContent($html);
        $response->setMaxAge(0);

      }

    }

    // Returns full HtmlResponse.
    return $response;

  }

}
