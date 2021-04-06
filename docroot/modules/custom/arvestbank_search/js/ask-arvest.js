/**
 * @file
 * Ask Arvest js.
 */

Drupal.behaviors.arvestbankSearchAskArvest = {
  attach: function (context, settings) {

    // Autocomplete select event.
    jQuery('.top-search-form input',context).bind('autocompleteselect', function (event, node) {

      // Force the autocomplete selection to apply before submit.
      event.target.value = node.item.value;

      // Submit the search.
      jQuery(this).parents('form').submit();

    });

  }
}
