<?php

namespace Drupal\arvestbank_calculators;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Calculator Code entity.
 *
 * @see \Drupal\arvestbank_calculators\Entity\CalculatorCode.
 */
class CalculatorCodeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\arvestbank_calculators\Entity\CalculatorCodeInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished calculator code entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published calculator code entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit calculator code entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete calculator code entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add calculator code entities');
  }


}
