<?php

namespace Drupal\arvestbank_media_alias\Controller;

use Drupal\Core\Entity\Controller\EntityViewController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\file\Entity\File;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Defines a controller to render a file with Media Alias being used.
 */
class DisplayController extends EntityViewController {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The current path.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * The path alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The controller constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   The logger factory.
   * @param \Symfony\Component\HttpFoundation\Request $request_stack
   *   Request stack.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   The current path.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The path alias manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, RendererInterface $renderer, AccountInterface $current_user, LoggerChannelFactoryInterface $loggerFactory, Request $request_stack, CurrentPathStack $current_path, AliasManagerInterface $alias_manager) {
    parent::__construct($entity_type_manager, $renderer);
    $this->currentUser = $current_user;
    $this->loggerFactory = $loggerFactory;
    $this->request = $request_stack;
    $this->currentPath = $current_path;
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): DisplayController {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('current_user'),
      $container->get('logger.factory'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('path.current'),
      $container->get('path_alias.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $media, $view_mode = 'full', $langcode = NULL) {

    // Set the default response to the parent view.
    $response = parent::view($media, $view_mode);

    // Check the bundle.
    if ($media->bundle() === 'acquia_dam_document') {

      // This is the specific field that has the file id.
      if ($fid = $media->get('field_acquiadam_asset_doc')[0]->getValue()['target_id']) {

        // Check that the file loads.
        if ($file = File::load($fid)) {

          // Grab these variables.
          $uri = $file->getFileUri();
          $filename = $file->getFilename();

          // Create the full HTTP binary response.
          $response = new BinaryFileResponse($uri);
          $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename
          );

        }
      }

    }

    // Return default parent response or inline file.
    return $response;

  }

}
