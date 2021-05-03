<?php

namespace Drupal\arvestbank_rates\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an admin form for updating credit card rates.
 */
class RatesForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'arvestbank_rates.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'arvestbank_rates_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get config.
    $config = $this->config('arvestbank_rates.settings');


    // REWARDS CARDS CONTAINER.
    $form['rewards_card_rates'] = [
      '#type' => 'details',
      '#title' => $this->t('Rewards Card Rates'),
      '#open' => TRUE,

      // Accurate As Of Date.
      'rewards_card_rates__accurate_as_of_date' => [
        '#type' => 'date',
        '#title' => $this->t('Accurate As Of Date'),
        '#default_value' => $config->get('rewards_card_rates__accurate_as_of_date'),
      ],

      // APR for Cash Advances.
      'rewards_card_rates__apr_for_cash_advances' => [
        '#type' => 'textfield',
        '#title' => $this->t('APR for Cash Advances'),
        '#default_value' => $config->get('rewards_card_rates__apr_for_cash_advances'),
      ],

      // Rewards Cards - Platinum Container.
      'rewards_card_rates__platinum_rates' => [
        '#type' => 'details',
        '#title' => $this->t('Platinum Rates'),

        // APR for Purchases.
        'rewards_card_rates__platinum_rates__apr_for_purchases' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Purchases'),
          '#default_value' => $config->get('rewards_card_rates__platinum_rates__apr_for_purchases'),
        ],

        // APR for Balance Transfers.
        'rewards_card_rates__platinum_rates__apr_for_balance_transfers' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Balance Transfers'),
          '#default_value' => $config->get('rewards_card_rates__platinum_rates__apr_for_balance_transfers'),
        ],

      ],

      // Rewards Cards - Signature Container.
      'rewards_card_rates__signature_rates' => [
        '#type' => 'details',
        '#title' => $this->t('Signature Rates'),

        // APR for Purchases.
        'rewards_card_rates__signature_rates__apr_for_purchases' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Purchases'),
          '#default_value' => $config->get('rewards_card_rates__signature_rates__apr_for_purchases'),
        ],

        // APR for Balance Transfers.
        'rewards_card_rates__signature_rates__apr_for_balance_transfers' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Balance Transfers'),
          '#default_value' => $config->get('rewards_card_rates__signature_rates__apr_for_balance_transfers'),
        ],

      ],

    ];


    // TRUE RATE CARD RATES CONTAINER.
    $form['true_rate_card_rates'] = [
      '#type' => 'details',
      '#title' => $this->t('True Rate Card Rates'),
      '#open' => TRUE,

      // Accurate As Of Date.
      'true_rate_card_rates__accurate_as_of_date' => [
        '#type' => 'date',
        '#title' => $this->t('Accurate As Of Date'),
        '#default_value' => $config->get('true_rate_card_rates__accurate_as_of_date'),
      ],

      // APR for Purchases.
      'true_rate_card_rates__apr_for_purchases' => [
        '#type' => 'textfield',
        '#title' => $this->t('APR for Purchases'),
        '#default_value' => $config->get('true_rate_card_rates__apr_for_purchases'),
      ],

      // APR for Balance Transfers.
      'true_rate_card_rates__apr_for_balance_transfers' => [
        '#type' => 'textfield',
        '#title' => $this->t('APR for Balance Transfers'),
        '#default_value' => $config->get('true_rate_card_rates__apr_for_balance_transfers'),
      ],

      // APR for Cash Advances.
      'true_rate_card_rates__apr_for_cash_advances' => [
        '#type' => 'textfield',
        '#title' => $this->t('APR for Cash Advances'),
        '#default_value' => $config->get('true_rate_card_rates__apr_for_cash_advances'),
      ],

    ];


    // Origin Card Rates.
    $form['origin_card_rates'] = [
      '#type' => 'details',
      '#title' => $this->t('Origin Card Rates'),
      '#open' => TRUE,

      // Accurate As Of Date.
      'origin_card_rates__accurate_as_of_date' => [
        '#type' => 'date',
        '#title' => $this->t('Accurate As Of Date'),
        '#default_value' => $config->get('origin_card_rates__accurate_as_of_date'),
      ],

      // APR for Purchases.
      'origin_card_rates__apr_for_purchases' => [
        '#type' => 'textfield',
        '#title' => $this->t('APR for Purchases'),
        '#default_value' => $config->get('origin_card_rates__apr_for_purchases'),
      ],

      // APR for Balance Transfers.
      'origin_card_rates__apr_for_balance_transfers' => [
        '#type' => 'textfield',
        '#title' => $this->t('APR for Balance Transfers'),
        '#default_value' => $config->get('origin_card_rates__apr_for_balance_transfers'),
      ],

      // APR for Cash Advances.
      'origin_card_rates__apr_for_cash_advances' => [
        '#type' => 'textfield',
        '#title' => $this->t('APR for Cash Advances'),
        '#default_value' => $config->get('origin_card_rates__apr_for_cash_advances'),
      ],

    ];


    // Legacy Card Rates.
    $form['legacy_card_rates'] = [
      '#type' => 'details',
      '#title' => $this->t('Legacy Card Rates'),
      '#open' => TRUE,

      // Accurate As Of Date.
      'legacy_card_rates__accurate_as_of_date' => [
        '#type' => 'date',
        '#title' => $this->t('Accurate As Of Date'),
        '#default_value' => $config->get('legacy_card_rates__accurate_as_of_date'),
      ],


      // Legacy Cards - Classic Container.
      'legacy_card_rates__classic_rates' => [
        '#type' => 'details',
        '#title' => $this->t('Classic Rates'),

        // APR for Purchases.
        'legacy_card_rates__classic_rates__apr_for_purchases' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Purchases'),
          '#default_value' => $config->get('legacy_card_rates__classic_rates__apr_for_purchases'),
        ],

        // APR for Balance Transfers.
        'legacy_card_rates__classic_rates__apr_for_balance_transfers' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Balance Transfers'),
          '#default_value' => $config->get('legacy_card_rates__classic_rates__apr_for_balance_transfers'),
        ],

        // APR for Cash Advances.
        'legacy_card_rates__classic_rates__apr_for_cash_advances' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Cash Advances'),
          '#default_value' => $config->get('legacy_card_rates__classic_rates__apr_for_cash_advances'),
        ],
      ],


      // Legacy Cards - Gold Container.
      'legacy_card_rates__gold_rates' => [
        '#type' => 'details',
        '#title' => $this->t('Gold Rates'),

        // APR for Purchases.
        'legacy_card_rates__gold_rates__apr_for_purchases' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Purchases'),
          '#default_value' => $config->get('legacy_card_rates__gold_rates__apr_for_purchases'),
        ],

        // APR for Balance Transfers.
        'legacy_card_rates__gold_rates__apr_for_balance_transfers' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Balance Transfers'),
          '#default_value' => $config->get('legacy_card_rates__gold_rates__apr_for_balance_transfers'),
        ],

        // APR for Cash Advances.
        'legacy_card_rates__gold_rates__apr_for_cash_advances' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Cash Advances'),
          '#default_value' => $config->get('legacy_card_rates__gold_rates__apr_for_cash_advances'),
        ],

      ],

      // Legacy Cards - Platinum Container.
      'legacy_card_rates__platinum_rates' => [
        '#type' => 'details',
        '#title' => $this->t('Platinum Rates'),

        // APR for Purchases.
        'legacy_card_rates__platinum_rates__apr_for_purchases' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Purchases'),
          '#default_value' => $config->get('legacy_card_rates__platinum_rates__apr_for_purchases'),
        ],

        // APR for Balance Transfers.
        'legacy_card_rates__platinum_rates__apr_for_balance_transfers' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Balance Transfers'),
          '#default_value' => $config->get('legacy_card_rates__platinum_rates__apr_for_balance_transfers'),
        ],

        // APR for Cash Advances.
        'legacy_card_rates__platinum_rates__apr_for_cash_advances' => [
          '#type' => 'textfield',
          '#title' => $this->t('APR for Cash Advances'),
          '#default_value' => $config->get('legacy_card_rates__platinum_rates__apr_for_cash_advances'),
        ],

      ],


    ];

    // Zero Card Rates.
    $form['zero_card_rates'] = [
      '#type' => 'details',
      '#title' => $this->t('Zero Card Rates'),
      '#open' => TRUE,
      '#markup' => '<p>Zero Card rates shown are the Purchases APR for "True Rate" and "Signature or Platinum Rewards" cards.</p>',
    ];

    // Build config form.
    return parent::buildForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Run the parent classes submit function.
    parent::submitForm($form, $form_state);

    // Get submitted values.
    $formValues = $form_state->getValues();

    // Loop over submitted values.
    foreach ($formValues as $formFieldKey => $formFieldValue) {
      // Save submitted value to config.
      $this->config('arvestbank_rates.settings')
        ->set($formFieldKey, $formFieldValue)
        ->save();
    }

  }

}
