<?php

namespace Drupal\arvestbank_migrate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\Entity\Media;

/**
 * Provides a form for deleting a batch_import_example entity.
 * /admin/arvestbank-migrate/cc-update-batch
 * @ingroup batch_import_example
 */
class CCUpdateBatch extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return 'cc_update_batch_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#prefix'] = '<p>This batch update all recently imported media.</p>';

    $form['actions'] = array(
      '#type' => 'actions',
      'submit' => array(
        '#type' => 'submit',
        '#value' => 'Proceed',
      ),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $batch = [
      'title' => t('Updating Media Entities'),
      'operations' => [],
      'init_message' => t('The process is starting...'),
      'progress_message' => t('Processed @current out of @total. Estimated time: @estimate.'),
      'error_message' => t('The process has encountered an error.'),
      'finished' => '\Drupal\arvestbank_migrate\Form\CCUpdateBatch::finishedCallback',
    ];

    $database = \Drupal::database();

    $queryString = "SELECT destid1 FROM migrate_map_cc";
    $query = $database->query($queryString);
    $results = $query->fetchAll();
    $operations = [];

    foreach ($results as $result) {
      $mid = $result->destid1;
      $operations[] = [
        [$this, 'updateMedia'], [$mid],
      ];
    }

    $batch['operations'] = $operations;

    batch_set($batch);
    \Drupal::messenger()->addMessage('Updated ' . count($results) . ' media entities!');

    $form_state->setRebuild(TRUE);

  }

  /**
   * @param $mid
   * @param $context
   *
   * @throws \Exception
   */
  public function updateMedia($mid, &$context) {

    $media = Media::load($mid);
    $media->save();

    $context['results'][] = $media->getName();
    $context['message'] = t('Updating @title', array('@title' => $media->getName()));

  }

  public static function finishedCallback($success, $results, $operations) {
      if ($success) {
          $message = \Drupal::translation()->formatPlural(
             count($results),
              'One post processed.', '@count posts processed.'
            );
          \Drupal::messenger()->addStatus($message);
        }
    else {
          \Drupal::messenger()->addError(t('Finished with an error.'));
  }
   }

}
