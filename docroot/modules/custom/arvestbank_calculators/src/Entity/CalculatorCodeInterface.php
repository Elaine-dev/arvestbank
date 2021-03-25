<?php

namespace Drupal\arvestbank_calculators\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Calculator Code entities.
 *
 * @ingroup arvestbank_calculators
 */
interface CalculatorCodeInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Calculator Code name.
   *
   * @return string
   *   Name of the Calculator Code.
   */
  public function getName();

  /**
   * Sets the Calculator Code name.
   *
   * @param string $name
   *   The Calculator Code name.
   *
   * @return \Drupal\arvestbank_calculators\Entity\CalculatorCodeInterface
   *   The called Calculator Code entity.
   */
  public function setName($name);

  /**
   * Gets the Calculator Code creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Calculator Code.
   */
  public function getCreatedTime();

  /**
   * Sets the Calculator Code creation timestamp.
   *
   * @param int $timestamp
   *   The Calculator Code creation timestamp.
   *
   * @return \Drupal\arvestbank_calculators\Entity\CalculatorCodeInterface
   *   The called Calculator Code entity.
   */
  public function setCreatedTime($timestamp);

}
