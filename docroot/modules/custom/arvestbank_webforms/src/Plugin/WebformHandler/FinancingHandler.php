<?php

namespace Drupal\arvestbank_webforms\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Views;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\Component\Utility\Html;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Webform validate handler.
 *
 * @WebformHandler(
 *   id = "arvestbank_webforms_financing",
 *   label = @Translation("Financing Options"),
 *   category = @Translation("Settings"),
 *   description = @Translation("Displays financing options based off of form selections."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class FinancingHandler extends WebformHandlerBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(WebformSubmissionInterface $webform_submission) {

      $build = [];

// https://www.drupal.org/docs/8/modules/webform/webform-cookbook/how-to-programmatically-skip-pages-in-wizard-forms

    $financing = views_embed_view('financing', 'default', 13391);

    $build[] = $financing;

    $this->getWebform()->setSettingOverride('confirmation_message', render($build));

  }

}
