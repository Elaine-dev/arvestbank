<?php

namespace Drupal\arvestbank_menus;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Switches out menu.active_trail service for our own service.
 *
 * @package Drupal\arvestbank_menus
 */
class ArvestbankMenusServiceProvider implements ServiceModifierInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Get the service we want to modify.
    $definition = $container->getDefinition('menu.active_trail');
    // Inject an additional service, our canonical menu link helper.
    $definition->addArgument(new Reference('arvestbank_menus.canonical_menu_link_helper'));
    // Make the active trail use our service.
    $definition->setClass(ArvestbankMenusActiveTrail::class);
  }

}
