<?php

namespace Drupal\arvestbank_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\views\Views;
use Drupal\Core\Form\FormState;

/**
 * Provides exposed filters in a block.
 *
 * @Block(
 *   id = "alt_search_bar_exposed_filters_block",
 *   admin_label = @Translation("Alternate Search Bar Exposed Filters Block"),
 * )
 */
class AlternateSearchBarExposedFilterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Get exposed form from search view.
    $view = Views::getView('database_search');
    $view->setDisplay('page_1');
    $view->initHandlers();

    // Build form state.
    $form_state = new FormState();
    $form_state->setFormState([
      'view' => $view,
      'display' => $view->display_handler->display,
      'exposed_form_plugin' => $view->display_handler->getPlugin('exposed_form'),
      'method' => 'GET',
      'rerender' => NULL,
      'no_redirect' => TRUE,
      'always_process' => TRUE,
      'redirect' => '/search',
    ]);

    // Set variable in form state indicating this is the alt search bar block.
    // As opposed to header search block or alt search. For use in form_alter.
    $storage = $form_state->getStorage();
    $storage['alt_search_block'] = TRUE;
    $form_state->setStorage($storage);

    // Build and return form.
    return \Drupal::formBuilder()->buildForm('Drupal\views\Form\ViewsExposedForm', $form_state);

  }

}
