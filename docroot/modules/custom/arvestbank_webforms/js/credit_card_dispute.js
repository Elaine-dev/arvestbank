(function ($, Drupal) {

  'use strict';

  $('input.nature-of-dispute').on('click', function (e) {

    // Uncheck and hide all dispute type form and sections.
    $('input.nature-of-dispute').each(function () {
      $(this).prop('checked', false);
      $(this).trigger('change');
    });

    // Go back and check and un-hide this dispute form.
    $(this).prop('checked', true);
    $(this).trigger('change');

  });

  $('input.no-merch-cancel').on('click', function (e) {

    // Uncheck and hide all dispute type form and sections.
    $('input.no-merch-cancel').each(function () {
      $(this).prop('checked', false);
    });

    // Go back and check and un-hide this dispute form.
    $(this).prop('checked', true);

  });

})(jQuery, Drupal);
