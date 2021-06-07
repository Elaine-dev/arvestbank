(function ($, Drupal) {

  'use strict';

  $('input.request-type').on('click', function (e) {

    // Uncheck and hide all dispute type form and sections.
    $('input.request-type').each(function () {
      $(this).prop('checked', false);
      $(this).trigger('change');
    });

    // Go back and check and un-hide this dispute form.
    $(this).prop('checked', true);
    $(this).trigger('change');

  });

})(jQuery, Drupal);
