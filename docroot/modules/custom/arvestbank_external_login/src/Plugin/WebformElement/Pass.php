<?php

namespace Drupal\arvestbank_external_login\Plugin\WebformElement;

use Drupal\webform\Plugin\WebformElement\TextField;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Provides a 'password' element.
 *
 * The webform module is annoying about password fields.
 *
 * @see https://www.drupal.org/project/webform/issues/2947991.
 *
 * @WebformElement(
 *   id = "pass",
 *   label = @Translation("Password field"),
 *   description = @Translation("Provides a form element for input of a password."),
 *   category = @Translation("Basic elements"),
 * )
 */
class Pass extends TextField {

  /**
   * {@inheritdoc}
   */
  public function prepare(array &$element, WebformSubmissionInterface $webform_submission = NULL) {
    parent::prepare($element, $webform_submission);
    // Change type to password.
    $element['#type'] = 'password';
  }

}
