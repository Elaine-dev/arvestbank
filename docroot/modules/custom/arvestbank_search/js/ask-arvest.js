/**
 * @file
 * Ask Arvest js.
 */

Drupal.behaviors.arvestbankSearchAskArvest = {
  attach: function (context, settings) {

    // Input focus event.
    jQuery('.search-form input',context).bind('focus', function (event, node) {
      // Clear text on focus.
      jQuery(this).val('');
    });

    // Autocomplete select event.
    jQuery('.search-form input',context).bind('autocompleteselect', function (event, node) {

      // Force the autocomplete selection to apply before submit.
      event.target.value = node.item.value;

      // Indicate that a suggestion was selected.
      jQuery(this).parents('form').find('.suggestion-field').val(8);

      // Submit the search.
      jQuery(this).parents('form').submit();

    });

  }
}
