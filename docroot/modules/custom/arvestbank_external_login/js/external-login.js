/**
 * @file
 * Provides js functionality related to external logins.
 */

Drupal.behaviors.externalLogin = {
  attach: function (context, settings) {

    // Populate cashman browser data.
    jQuery('input[name=pm_fp]').each(function () {
      jQuery(this).val(encode_deviceprint());
    });
    // Set cashman javascript indicator to "OK".
    jQuery('input[name=TestJavaScript]').each(function () {
      jQuery(this).val('OK');
    });

    // Login select change function.
    jQuery('#edit-login-select').change(function () {

      // If "Cash Manager" is selected.
      if (jQuery(this).val() == 'cash_manager') {
        // Change form action to cashman to directly post there.
        jQuery(this).parents('form').attr(
          'action',
          'https://ecash.arvest.com/CorporateBankingWeb/Core/Signin.aspx'
        );
      }
      // If "Investments - Wealth" is selected.
      else if (jQuery(this).val() == 'investments_wealth') {
        // Change form action to cashman to directly post there.
        jQuery(this).parents('form').attr(
          'action',
          'https://sso.arvest.com/idp/startSSO.ping?PartnerSpId=http%3A%2F%2Fwww.netxinvestor.com&ArvBrand=AWM'
        );
      }
      else {
        // Set action to default.
        jQuery(this).parents('form').attr(
          'action',
          '/'
        );
      }

    });

  }
};


