/**
 * @file
 * Provides js functionality related to external logins.
 */

Drupal.behaviors.externalLogin = {
  attach: function (context, settings) {

    // Populate cashman browser data.
    jQuery('input[name=pm_fp]').val(encode_deviceprint());
    // Set cashman javascript indicator to "OK".
    jQuery('input[name=TestJavaScript]').val('OK');

    // Login select change function.
    jQuery('#edit-login-select').change(function () {

      // If cash manager is selected.
      if (jQuery(this).val() == 'cash_manager') {
        // Change form action to cashman to directly post there.
        jQuery(this).parents('form').attr(
          'action',
          'https://ecash.arvest.com/CorporateBankingWeb/Core/Signin.aspx'
        );
      }
      else {
        // Set action to default in case it was switched to cash manager.
        jQuery(this).parents('form').attr(
          'action',
          '/'
        );
      }

    });

  }
};


