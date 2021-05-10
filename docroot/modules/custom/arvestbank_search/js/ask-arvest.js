/**
 * @file
 * Ask Arvest js.
 */

Drupal.behaviors.arvestbankSearchAskArvest = {
  attach: function (context, settings) {

    // Input focus event.
    jQuery('.search-form .form-autocomplete',context).bind('focus', function (event, node) {
      // Clear text on focus.
      jQuery(this).val('');
    });

    // Autocomplete select event.
    jQuery('.search-form input',context).bind('autocompleteselect', function (event, node) {

      // Force the autocomplete selection to apply before submit.
      event.target.value = node.item.label;

      // Get the id of the response and put in the hidden field for that.
      var suggestionId = node.item.value;

      // Set the value to the label, we had it as the id so we could snag it.
      node.item.value = node.item.label;

      // Indicate that a suggestion was selected.
      jQuery(this).parents('form').find('.suggestion-field').val(8);

      // Populate the field indicating the id of the selected question.
      jQuery(this).parents('form').find('.suggestion-id').val(suggestionId);

      // Submit the search.
      jQuery(this).parents('form').submit();

    });

  }
}
