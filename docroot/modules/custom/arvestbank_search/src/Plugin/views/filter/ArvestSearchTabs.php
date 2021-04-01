<?php

namespace Drupal\arvestbank_search\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filters by phase or status of project.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("arvest_search_tabs")
 */
class ArvestSearchTabs extends FilterPluginBase {

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function canBuildGroup() {
    return FALSE;
  }

  /**
   * This method returns the ID of the fake field which contains this plugin.
   *
   * It is important to put this ID to the exposed field
   * of this plugin for the following reasons:
   * a) To avoid problems with FilterPluginBase::acceptExposedInput function
   * b) To allow this filter to be printed on
   * twig templates with {{ form.custom_az_filter }}
   *
   * @return string
   *   ID of the field which contains this plugin.
   */
  private function getFilterId() {
    return $this->options['expose']['identifier'];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {

    // Get exposed filter values.
    $exposedFilterValues = $this->view->getExposedInput();

    // If we have a non-null and non-all value for the tab.
    if (
      isset($exposedFilterValues['search-tab'])
      && $exposedFilterValues['search-tab'] != ''
      && $exposedFilterValues['search-tab'] != 'all'
    ) {

      // Get query fields to ensure the fields we want to filter with are there.
      $queryFields = $this->query->getIndex()->getFields();

      // Documents tab.
      if (
        isset($queryFields['media_type'])
        && $exposedFilterValues['search-tab'] == 'documents'
      ) {
        $this->query->addCondition('media_type', 'acquia_dam_document');
      }

      // Financial Insights tab.
      if (
        isset($queryFields['type'])
        && $exposedFilterValues['search-tab'] == 'financial_insights'
      ) {
        $this->query->addCondition('type', 'article_education_article');
      }

      // Services tab.
      // Currently this is everything not in insights and documents.
      // I expect the client to come back with better specifications.
      if (
        isset($queryFields['type'])
        && isset($queryFields['media_type'])
        && $exposedFilterValues['search-tab'] == 'services'
      ) {
        $this->query->addCondition('type', 'article_education_article', '<>');
        $this->query->addCondition('media_type', 'acquia_dam_document', '<>');
      }

    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildExposedForm(&$form, FormStateInterface $form_state) {

    // Get the active tab.
    $activeSearchTab = 'all';
    if (
      isset($_GET['search-tab'])
      && $_GET['search-tab'] != ''
    ) {
      $activeSearchTab = $_GET['search-tab'];
    }

    // Define our search tabs.
    $form['search-tab'] = [
      '#type' => 'radios',
      '#default_value' => $activeSearchTab,
      '#id' => 'search-tab',
      '#options' => [
        'all' => $this->t('All Results'),
        'services' => $this->t('Services'),
        'documents' => $this->t('Documents'),
        'financial_insights' => $this->t('Financial Insights'),
      ],
      '#attributes' => ['class' => ['search-tabs']],
      '#attached' => ['library' => ['arvestbank_search/search-tabs']],
      '#cache' => ['contexts' => ['url.query_args']],
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function acceptExposedInput($input) {
    return TRUE;
  }

}
