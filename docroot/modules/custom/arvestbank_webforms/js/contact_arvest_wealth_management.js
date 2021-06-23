(function ($, Drupal) {

  'use strict';

  // This should be hidden, but issue with chosen.
  $('#edit-form-fields').hide();

  $('input.email-destination').on('click', function (e) {

    // Uncheck and email destinations.
    $('input.email-destination').each(function () {
      $(this).prop('checked', false);
      $(this).trigger('change');
    });

    // Go back and check and un-hide this email destination.
    $(this).prop('checked', true);
    $(this).trigger('change');

    // Show the form if one of the options was clicked.
    $('#edit-form-fields').show();

  });

})(jQuery, Drupal);
