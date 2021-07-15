<?php

namespace Drupal\arvestbank_core\Form;

use Drupal\content_moderation\ModerationInformationInterface;
use Drupal\content_moderation\StateTransitionValidation;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NodeStatusForm node local task to change the workflow state / status.
 */
class NodeStatusForm extends FormBase {

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected EntityStorageInterface $nodeStorage;

  /**
   * Current user service.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $currentUser;

  /**
   * The moderation information service.
   *
   * @var \Drupal\content_moderation\ModerationInformationInterface
   */
  protected ModerationInformationInterface $moderationInfo;

  /**
   * Returns valid workflow state options for the current user.
   *
   * @return array
   *   Options array.
   */
  protected function getStateOptions(EntityInterface $revision): array {

    // Initialize the return options.
    $options = [];

    // Create a transistion class.
    $transition = new StateTransitionValidation($this->moderationInfo);

    // This will return the valid transitions for this user and current state.
    $valid_transitions = $transition->getValidTransitions($revision, $this->currentUser);

    // Continue if we have valid transitions.
    if (!empty($valid_transitions)) {

      // Create options from valid states to transition to.
      foreach ($valid_transitions as $valid_transition) {
        $options[$valid_transition->to()->id()] = $valid_transition->to()->label();
      }
    }

    // Returns array of valid states.
    return $options;

  }

  /**
   * Returns the current revision for a node nid.
   *
   * @param int $nid
   *   Node ID.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The current revision.
   */
  protected function getCurrentRevision(int $nid) {
    // Need to load the latest revision this way.
    $vid = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->getLatestRevisionId($nid);
    return \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadRevision($vid);
  }

  /**
   * NodeStatusForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   Node storage.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user.
   * @param \Drupal\content_moderation\ModerationInformationInterface $moderation_info
   *   Moderation information.
   */
  public function __construct(EntityStorageInterface $node_storage, AccountInterface $current_user, ModerationInformationInterface $moderation_info) {
    $this->nodeStorage = $node_storage;
    $this->currentUser = $current_user;
    $this->moderationInfo = $moderation_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('node'),
      $container->get('current_user'),
      $container->get('content_moderation.moderation_information')
    );

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return 'node_status_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL): array {

    // Initialize the return form array.
    $form = [];

    // Get the current revision for this node.
    $revision = $this::getCurrentRevision($node->id());

    // Continue if there is a moderation state.
    if (
      $revision->hasField('moderation_state') &&
      !$revision->get('moderation_state')->isEmpty() &&
      !empty($revision->get('moderation_state')->getString())
    ) {

      // Current state.
      $state = $revision->get('moderation_state')->getString();
      $state_label = \Drupal::config("workflows.workflow.editorial")->get("type_settings.states.{$state}.label");

      // Pass the node through the form.
      $form['#node'] = $node;

      // Helpful markup.
      $form['#prefix'] = '<h3>Current State: ' . $state_label . '</h3>';

      // Get valid states for this revision.
      $state_options = $this::getStateOptions($revision);

      // Dropdown of status to change to.
      $form['update_status'] = [
        '#title' => 'Update Status To:',
        '#type' => 'select',
        '#default_value' => $state,
        '#options' => $state_options,
      ];

      // Submit.
      $form['actions'] = [
        '#type' => 'actions',
        'submit' => [
          '#type' => 'submit',
          '#value' => 'Proceed',
        ],
      ];

    }

    // Return form render array.
    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Current revision.
    $revision = $this::getCurrentRevision($form['#node']->id());

    // Grab the old/current and new states.
    $status_old = $revision->get('moderation_state')->getValue()[0]['value'];
    $status_new = $form_state->getValue('update_status');

    // If there was a change proceed.
    if ($status_old != $status_new) {
      // Set and save the state/status.
      $revision->set('moderation_state', $status_new);
      $revision->save();
      // Return a helpful message.
      \Drupal::messenger()->addMessage('Updated status to: ' . $status_new);
    }

  }

}
