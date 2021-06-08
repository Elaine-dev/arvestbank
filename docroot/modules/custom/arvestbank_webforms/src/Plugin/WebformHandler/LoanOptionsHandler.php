<?php

namespace Drupal\arvestbank_webforms\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\Component\Utility\Html;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Webform validate handler.
 *
 * @WebformHandler(
 *   id = "arvestbank_webforms_loanoptions",
 *   label = @Translation("Loan Options"),
 *   category = @Translation("Settings"),
 *   description = @Translation("Displays loan options based off of form selections."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class LoanOptionsHandler extends WebformHandlerBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(WebformSubmissionInterface $webform_submission) {

    $build = [];

    dump($webform_submission);
    die();

    $entity_type = 'node';
    $entity_id = '1';
    $view_mode = 'full';

    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
    $pre_render = $view_builder->view($entity, $view_mode);
    $build[] = $pre_render;

    $this->getWebform()->setSettingOverride('confirmation_message', render($build));

  }

}
