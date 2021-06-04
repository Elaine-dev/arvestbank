/**
 * @file
 * Provides js functionality related to external logins.
 */

Drupal.behaviors.externalLogin = {
  attach: function (context, settings) {

    // Use "chosen" to make selects styleable sudo-selects.
    jQuery('select[name="login_select_non_prod"],select[name="login_select"]').chosen({
      "disable_search": true,
      allow_single_deselect: true,
      width: "100%"
    });

    // Form submit function.
    jQuery('.webform-submission-external-login-menu-add-form,.webform-submission-external-login-add-form').submit(function (e) {

      // Determine if this is menu or sidebar form.
      if (jQuery(this).hasClass('.webform-submission-external-login-menu-add-form')) {
        var sidebarForm = false;
      }
      else {
        var sidebarForm = true;
      }

      // Get the active select element for the submitted form.
      var $activeSelect = jQuery(this).find(
        '.active-sidebar-select select[name="login_select_non_prod"],select[name="login_select"]'
      );

      // Get the online banking username element for the submitted form.
      var $onlineBankingUsernameField = jQuery(this).find('input[name="username"]');

      // Determine the domain of the corresponding online banking site.
      if ($activeSelect.attr('name') == 'login_select_non_prod') {
        var onlineBankingDomain = 'https://new17test.arvest.com';
      }
      else {
        var onlineBankingDomain = 'https://www.arvest.com';
      }

      // Ajax POST before redirect if online banking is selected.
      // or nothing is selected (meaning we're defulting to online banking)
      // and username is filled.
      if (
        // If online banking or nothing is selected and it's the sidebar form.
        (
          $activeSelect.val() == 'arvest_online_banking'
          || (
            $activeSelect.val() == null
            && sidebarForm
          )
        )
        // If username field is set.
        && $onlineBankingUsernameField.val()
      ) {
        // Prevent submission.
        e.preventDefault();

        jQuery.ajax({
          async: false,
          url: onlineBankingDomain + "/personal/signon/logon",
          type: "POST",
          data: {"username": $onlineBankingUsernameField.val(), "q": ''  },
        }).done(function () {

          // Redirect, same as submitting form, but we already prevented that.
          /*url_redirect({url: onlineBankingDomain + "/personal/signon/logon/authenticate",
            method: "post",
            data: {"usererr": username}
          });*/

        });;

      }

      // Prevent submission of menu login block if select is empty.
      var selectIsSet = false;
      jQuery('.webform-submission-external-login-menu-add-form select').each(function () {
        if (jQuery(this).value() != '') {
          selectIsSet = true;
        }
      });
      if (!selectIsSet) {
        e.preventDefault();
        return false;
      }

    });

    // Populate cashman browser data.
    jQuery('input[name=pm_fp]').each(function () {
      jQuery(this).val(encode_deviceprint());
    });
    // Set cashman javascript indicator to "OK".
    jQuery('input[name=TestJavaScript]').each(function () {
      jQuery(this).val('OK');
    });

    // Unrequire sidebar login select on load
    // To be re-required on change.
    jQuery('.block-homepage-external-login-block #edit-login-select-non-prod')
      .add('.block-homepage-external-login-block #edit-login-select-non-prod--2')
      .add('.block-homepage-external-login-block #edit-login-select')
      .add('.block-homepage-external-login-block #edit-login-select--2')
      .removeAttr('required');

    // Default sidebar form action non prod.
    if (jQuery('.block-homepage-external-login-block .active-sidebar-select-non-prod').length) {
      jQuery('.block-homepage-external-login-block form').attr(
        'action',
        'https://new17test.arvest.com/personal/signon/logon/authenticate'
      );
    }

    // Default sidebar form action prod.
    if (jQuery('.block-homepage-external-login-block .active-sidebar-select-prod').length) {
      jQuery('.block-homepage-external-login-block form').attr(
        'action',
        'https://www.arvest.com/personal/signon/logon/authenticate'
      );
    }


    // Login select change function
    // For prod or non prod sidebar select
    // To hide/show online banking field container and re-require select
    // Doing this here to prevent hiding on load.
    jQuery('.block-homepage-external-login-block #edit-login-select-non-prod')
      .add('.block-homepage-external-login-block #edit-login-select-non-prod--2')
      .add('.block-homepage-external-login-block #edit-login-select')
      .add('.block-homepage-external-login-block #edit-login-select--2')
      .change(function () {

      // Require select again.
      jQuery(this).attr('required','required');

      // If the online banking container should be shown.
      if (jQuery(this).val() == 'arvest_online_banking') {
        jQuery('.block-homepage-external-login-block #edit-arvest-online-banking')
          .add('.block-homepage-external-login-block #edit-arvest-online-banking--2')
          .show();
      }
      // If the online banking container should be hidden.
      else {
        // Hide container.
        jQuery('.block-homepage-external-login-block #edit-arvest-online-banking')
          .add('.block-homepage-external-login-block #edit-arvest-online-banking--2')
          .hide();
        // Set value of contained fields to null.
        jQuery('.block-homepage-external-login-block #edit-arvest-online-banking input')
          .add('.block-homepage-external-login-block #edit-arvest-online-banking--2 input')
          .each(function () {
          jQuery(this).val('');
        });
      }

    });

    // Login select change function.
    // For non-prod login select.
    jQuery('#edit-login-select-non-prod').add('#edit-login-select-non-prod--2').change(function () {

      // If "Cash Manager" is selected.
      if (jQuery(this).val() == 'cash_manager') {
        // Change form action.
        jQuery(this).parents('form').attr(
          'action',
          'https://corilliantest.arvest.com/CorporateBankingWeb/Core/SignIn.aspx'
        );
      }
      // If "Investments - Wealth" is selected.
      else if (jQuery(this).val() == 'investments_wealth') {
        // Change form action.
        jQuery(this).parents('form').attr(
          'action',
          'https://sso-tst.arvest.com/idp/startSSO.ping?PartnerSpId=http%3A%2F%2Fwww.netxinvestor.com&ArvBrand=AWM'
        );
      }
      // If "Arvest Online Banking" is selected.
      else if (jQuery(this).val() == 'arvest_online_banking') {
        // Change form action.
        jQuery(this).parents('form').attr(
          'action',
          'https://www.arvest.com/personal/signon/logon'
        );
      }
      // If "Mortgage" is selected.
      else if (jQuery(this).val() == 'mortgage') {
        // Change form action.
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
        // Change form action.
        jQuery(this).parents('form').attr(
          'action',
          'https://ecash.arvest.com/CorporateBankingWeb/Core/Signin.aspx'
        );
      }
      // If "Investments - Wealth" is selected.
      else if (jQuery(this).val() == 'investments_wealth') {
        // Change form action.
        jQuery(this).parents('form').attr(
          'action',
          'https://sso.arvest.com/idp/startSSO.ping?PartnerSpId=http%3A%2F%2Fwww.netxinvestor.com&ArvBrand=AWM'
        );
      }
      // If "Arvest Online Banking" is selected.
      else if (jQuery(this).val() == 'arvest_online_banking') {
        // Change form action.
        jQuery(this).parents('form').attr(
          'action',
          'https://www.arvest.com/personal/signon/logon'
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
