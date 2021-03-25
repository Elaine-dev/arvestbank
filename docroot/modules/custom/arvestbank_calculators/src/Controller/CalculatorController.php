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
 * An example controller.
 */
class CalculatorController extends ControllerBase {

  /**
   * Constructs an OEmbedIframeController instance.
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
   * Renders a resource.
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

    $response = new HtmlResponse();

    $html = ['#type' => 'html'];

    $nid = (int) \Drupal::request()->query->get('nid');

    if ($node = Node::load($nid)) {

      if ($node->bundle() == 'calculators') {

        $display_settings = [
          'label' => 'hidden',
          'type' => 'arvestbank_calculators_embed',
        ];
        $element = $node->get('field_calculator')->view($display_settings);

        $html['page'] = [
          '#type' => 'page',
          'content' => $element,
        ];

        \Drupal::service('renderer')->renderRoot($html);
        $response->setContent($html);
        $response->setMaxAge(0);

      }

    }

    return $response;

  }

}
