<?php

namespace Drupal\arvestbank_migrate\Feeds\Processor;

use Drupal\feeds\FeedInterface;
use Drupal\feeds\Feeds\Item\ItemInterface;
use Drupal\feeds\Feeds\Processor\EntityProcessorBase;
use Drupal\feeds\StateInterface;

/**
 * Class CreditCardProcessor for custom migration.
 *
 * @package Drupal\arvestbank_migrate\Feeds\Processor
 */
class CreditCardProcessor extends EntityProcessorBase {

  public function process(FeedInterface $feed, ItemInterface $item, StateInterface $state) {

    dump($feed);
    dump($item);
    dump($state);

    die();

  }

}
