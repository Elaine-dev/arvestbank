/**
 * @file
 * Conditionally show article fields.
 *
 * States are very complicated with paragraphs
 * and the old D8 method doesn't work in D9.
 */

(function ($, Drupal) {

  Drupal.behaviors.conditionalArticleFields = {
    attach: function (context, settings) {

      console.log('test');

      // Hide or show conditional fields on load.
      hideOrShowArticleFields();

      // Hide or show conditional fields on display field change.
      jQuery('.field--name-field-wordpress-articles .field--name-field-display select').change(function () {
        hideOrShowArticleFields();
      });

    }
  };

  function hideOrShowArticleFields() {

    // Determine weather to hide or show.
    var showFields = false;
    if (jQuery('.field--name-field-wordpress-articles .field--name-field-display select').val() == 'endpoint') {
      showFields = true;
    }

    // Get the conditional fields container elements.
    var conditionalFieldContainerElements =
      jQuery('.field--name-field-wordpress-articles .field--name-field-feed-url')
      .add('.field--name-field-wordpress-articles .field--name-field-article-')

    // If we should show the fields.
    if (showFields) {
      conditionalFieldContainerElements.show();
    }
    else {
      conditionalFieldContainerElements.hide();
    }

  }

})(jQuery, Drupal);
