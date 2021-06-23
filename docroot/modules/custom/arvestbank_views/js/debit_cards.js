(function ($) {
  Drupal.behaviors.debitCards = {
    attach: function (context, settings) {
      // Reset other exposed filters when one is selected/changed.
      $(document).once('debit-cards-ajax').ajaxComplete(function (e, xhr, settings) {
        // Select Category/Theme.
        $('select[name="field_debit_card_category_target_id"]').change(function () {
          $('input[name="field_debit_card_keywords_value"]').val('');
          $('input[name="geolocation_zip"]').val('');
          // Auto-submit - hopefully just on one submit button, we will use the extra-submit.
          $('#views-exposed-form-specialty-debit-cards-specialty-debit-card-block input.extra-submit.form-submit').trigger('click');
        });
        // Change Keywords.
        $('input[name="field_debit_card_keywords_value"]').change(function () {
          $('select[name="field_debit_card_category_target_id"]').children("option[value='All']").prop('selected',true).trigger("chosen:updated");
          $('input[name="geolocation_zip"]').val('');
        });
        // Change School Zip Code.
        $('input[name="geolocation_zip"]').change(function () {
          $('select[name="field_debit_card_category_target_id"]').children("option[value='All']").prop('selected',true).trigger("chosen:updated");
          $('input[name="field_debit_card_keywords_value"]').val('');
        });
      });
    }
  }
})(jQuery);
