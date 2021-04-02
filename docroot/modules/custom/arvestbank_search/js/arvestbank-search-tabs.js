/**
 * @file
 * Search tabs js.
 */

Drupal.behaviors.arvestbankSearch = {
  attach: function (context, settings) {

    // Remove active class from all tabs.
    jQuery("#search-tab input").each(function() {
      jQuery(this).removeClass('active-tab');
    });

    // Add active class to selected tab.
    jQuery("#search-tab input[type='radio']:checked")
      .siblings('label')
      .addClass('active-tab');

    // Submit form when tab changes.
    jQuery('#search-tab input').change(function () {
      jQuery(this).parents('form').submit();
    });

  }
};
