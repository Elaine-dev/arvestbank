(function ($, Drupal) {

  'use strict';

  $('input.email-destination').on('click', function (e) {

    // Uncheck and email destinations.
    $('input.email-destination').each(function () {
      $(this).prop('checked', false);
      $(this).trigger('change');
    });

    // Go back and check and un-hide this email destination.
    $(this).prop('checked', true);
    $(this).trigger('change');

  });

})(jQuery, Drupal);
