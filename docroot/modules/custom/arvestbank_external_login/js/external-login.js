/**
 * @file
 * Provides js functionality related to external logins.
 */

Drupal.behaviors.externalLogin = {
  attach: function (context, settings) {

    // Instantiate menu Select2, move into login block, and add closeout button.
    jQuery('.webform-submission-external-login-menu-add-form select').select2({
      dropdownParent: jQuery('.block-menu-external-login-block .top-select'),
      minimumResultsForSearch: Infinity,
      allowClear: true,
    });

    // Instantiate sidebar Select2, move into sidebar login block.
    jQuery('.webform-submission-external-login-add-form select').select2({
      dropdownParent: jQuery('.block-homepage-external-login-block .sidebar-select'),
      minimumResultsForSearch: Infinity,
    });

    jQuery('.webform-submission-external-login-menu-add-form,.webform-submission-external-login-add-form').submit(function (e) {

      // Determine if this is menu or sidebar form.
      if (jQuery(this).hasClass('.webform-submission-external-login-menu-add-form')) {
        var sidebarForm = false;
      }
      else {
        var sidebarForm = true;
      }

      // Get the active select element for the submitted form.
      // Ignore the "sidebar" in the class name, used for both.
      var $activeSelect = jQuery(this).find(
        '.active-sidebar-select select[name="login_select_non_prod"],select[name="login_select"]'
      );

      // Get the online banking username element for the submitted form.
      var $onlineBankingUsernameField = jQuery(this).find('input[name="username"]');

      // Get the mortgage username element for the submitted form.
      var $mortgageUsernameField = jQuery(this).find('input[name="userid"]');

      // Determine the domain of the corresponding online banking site.
      if ($activeSelect.attr('name') == 'login_select_non_prod') {
        var onlineBankingDomain = 'https://www-test.arvest.com';
      }
      else {
        var onlineBankingDomain = 'https://www.arvest.com';
      }

      // Ajax POST before redirect if online banking is selected.
      // or nothing is selected (meaning we're defaulting to online banking)
      // and username is filled.
      if (
        // If online banking or nothing is selected and it's the sidebar form.
        (
          $activeSelect.val() == 'arvest_online_banking'
          || (
            !Boolean($activeSelect.val())
            && sidebarForm
          )
        )
        // If username field is set.
        && $onlineBankingUsernameField.val() != null
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
          post_redirect({url: onlineBankingDomain + "/personal/signon/logon/authenticate",
            method: "post",
            data: {"username": $onlineBankingUsernameField.val()}
          });

        })
        .fail(function () {

          // Also redirect on failure, at least they'll get to that page.
          // Same as submitting form, but we already prevented that.
          post_redirect({url: onlineBankingDomain + "/personal/signon/logon/authenticate",
            method: "post",
            data: {"username": $onlineBankingUsernameField.val()}
          });

        });

      }

      // If we're submitting the mortgage form and have a username set.
      if (
        $activeSelect.val() == 'mortgage'
        // If username field is set.
        && $mortgageUsernameField.val() != ''
      ) {

        // Prevent submission.
        e.preventDefault();

        // Determine post variables to send.
        var postVariables = {
          "userid": $mortgageUsernameField.val(),
          "password": jQuery(this).find('input[name="password"]').val(),
        };

        // Add value for "remember" if checked.
        if (jQuery(this).find('input[name="remember"]').is(':checked')) {
          postVariables.rememberVal = "on";
        }

        // Send cleaner version of submission.
        post_redirect({url: jQuery(this).attr('action'),
          method: "post",
          data: postVariables,
        });
      }

      // Determine if menu login block if select is empty.
      var menuSelectIsSet = false;
      if ($activeSelect.val() != '') {
            menuSelectIsSet = true;
      }

      // If this is the menu form and that select isn't set.
      if (!menuSelectIsSet || !sidebarForm) {
        e.preventDefault();
        return false;
      }

    });

    // Add inline-form class to sidebar form on load and focus text input
    jQuery('.webform-submission-external-login-form').toggleClass('inline-form');
    jQuery('.webform-submission-external-login-form input[data-drupal-selector="edit-arvest-online-banking-username"]').focus();

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
    jQuery('.block-homepage-external-login-block [data-drupal-selector="edit-login-select-non-prod"]')
      .add('.block-homepage-external-login-block [data-drupal-selector="edit-login-select"]')
      .removeAttr('required');

    // Default sidebar form action non prod.
    if (jQuery('.block-homepage-external-login-block .active-sidebar-select-non-prod').length) {
      jQuery('.block-homepage-external-login-block form').attr(
        'action',
        'https://www-test.arvest.com/personal/signon/logon/authenticate'
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
    jQuery('.block-homepage-external-login-block [data-drupal-selector="edit-login-select-non-prod"]')
      .add('.block-homepage-external-login-block [data-drupal-selector="edit-login-select"]')
      .change(function () {

      // Require select again.
      jQuery(this).attr('required','required');

      // If the online banking container should be shown.
      if (jQuery(this).val() == 'arvest_online_banking') {
        jQuery('.block-homepage-external-login-block [data-drupal-selector="edit-arvest-online-banking"]')
          .show();
      }
      // If the online banking container should be hidden.
      else {
        // Hide container.
        jQuery('.block-homepage-external-login-block [data-drupal-selector="edit-arvest-online-banking"]')
          .hide();
        // Set value of contained fields to null.
        jQuery('.block-homepage-external-login-block [data-drupal-selector="edit-arvest-online-banking"] input')
          .each(function () {
          jQuery(this).val('');
        });
      }
    });

    // Login select change function.
    // For non-prod login select.
    jQuery('select[name="login_select_non_prod"]').change(function () {

      // Add class if Arvest Online Banking selected. Else remove inline-form class.
      var nonProdForm = jQuery(this).closest('form');
      nonProdForm.toggleClass('inline-form', jQuery(this).val() == 'arvest_online_banking');

      // After form displays, focus on visible email or text input
      setTimeout(function() {
        nonProdForm.find('.js-form-wrapper .js-form-wrapper:visible .js-form-item .form-email, .js-form-wrapper .js-form-wrapper:visible .js-form-item .form-text').first().focus();
      }, 100);

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
          'https://www-test.arvest.com/personal/signon/logon'
        );
      }
      // If "Mortgage" is selected.
      else if (jQuery(this).val() == 'mortgage') {
        // Change form action.
        jQuery(this).parents('form').attr(
          'action',
          'https://www-test.arvest.com/personal/sign-on/login'
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
    jQuery('select[name="login_select"]').change(function () {

      // Add class if Arvest Online Banking selected. Else remove inline-form class.
      var prodForm = jQuery(this).closest('form');
      prodForm.toggleClass('inline-form', jQuery(this).val() == 'arvest_online_banking');

      // After form displays, focus on visible email or text input
      setTimeout(function() {
        prodForm.find('.js-form-wrapper .js-form-wrapper:visible .js-form-item .form-email, .js-form-wrapper .js-form-wrapper:visible .js-form-item .form-text').first().focus();
      }, 100);

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

    function post_redirect(options){
      var $form = jQuery("<form />");

      $form.attr("action",options.url);
      $form.attr("method",options.method);

      for (var data in options.data) {
        $form.append('<input type="hidden" name="' + data + '" value="' + options.data[data] + '" />');
      }

      jQuery("body").append($form);
      $form.submit();
    }

  }
};
