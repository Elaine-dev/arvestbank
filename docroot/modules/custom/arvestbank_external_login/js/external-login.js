/**
 * @file
 * Provides js functionality related to external logins.
 */

Drupal.behaviors.externalLogin = {
  attach: function (context, settings) {

    // Use "chosen" to make selects styleable sudo-selects.
    jQuery('#edit-login-select-non-prod,#edit-login-select-non-prod--2,#edit-login-select,#edit-login-select--2').chosen({
      "disable_search": true
    });

    // Populate cashman browser data.
    jQuery('input[name=pm_fp]').each(function () {
      jQuery(this).val(encode_deviceprint());
    });
    // Set cashman javascript indicator to "OK".
    jQuery('input[name=TestJavaScript]').each(function () {
      jQuery(this).val('OK');
    });



    // Login select change function.
    // For non-prod login select.
    jQuery('#edit-login-select-non-prod').add('#edit-login-select-non-prod--2').change(function () {

      // If "Cash Manager" is selected.
      if (jQuery(this).val() == 'cash_manager') {
        // Change form action to cashman to directly post there.
        jQuery(this).parents('form').attr(
          'action',
          'https://corilliantest.arvest.com/CorporateBankingWeb/Core/SignIn.aspx'
        );
      }
      // If "Investments - Wealth" is selected.
      else if (jQuery(this).val() == 'investments_wealth') {
        // Change form action to cashman to directly post there.
        jQuery(this).parents('form').attr(
          'action',
          'https://sso-tst.arvest.com/idp/startSSO.ping?PartnerSpId=http%3A%2F%2Fwww.netxinvestor.com&ArvBrand=AWM'
        );
      }
      // If "Arvest Online Banking" is selected.
      else if (jQuery(this).val() == 'arvest_online_banking') {
        // Change form action to cashman to directly post there.
        jQuery(this).parents('form').attr(
          'action',
          'https://new17test.arvest.com/personal/signon/logon/'
        );
      }
      // If "Mortgage" is selected.
      else if (jQuery(this).val() == 'mortgage') {
        // Change form action to cashman to directly post there.
        jQuery(this).parents('form').attr(
          'action',
          'https://new17test.arvest.com/personal/sign-on/login'
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



    // Login select change function.
    // For prod login select.
    jQuery('#edit-login-select').add('#edit-login-select--2').change(function () {

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
      // If "Arvest Online Banking" is selected.
      else if (jQuery(this).val() == 'arvest_online_banking') {
        // Change form action to cashman to directly post there.
        jQuery(this).parents('form').attr(
          'action',
          'https://www.arvest.com/personal/signon/logon/'
        );
      }
      // If "Mortgage" is selected.
      else if (jQuery(this).val() == 'mortgage') {
        // Change form action to cashman to directly post there.
        jQuery(this).parents('form').attr(
          'action',
          'https://www.arvest.com/personal/sign-on/login'
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


