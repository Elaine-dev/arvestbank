(function($, Drupal, drupalSettings) {

  // Initialize the browsertest object.
  var browsertest = {};

  // Function to get the IP address.
  browsertest.getIPaddress = function () {
    let $client = drupalSettings.arvestbank_browsertest.client;
    let $forward = drupalSettings.arvestbank_browsertest.forwarded;
    let $remote = drupalSettings.arvestbank_browsertest.remote_addr;
    let $ip;
    if ($forward) {
      $ip = $forward;
    } else if ($client) {
      $ip = $client;
    } else {
      $ip = $remote;
    }
    return $ip;
  }

  // Displays one row of the browser test.
  browsertest.displayRow = function (label, value, valclass = '') {
    let row = '';
    row += '<div class="browsertest-row">';
    row += '<span class="browsertest-label"><b>' + label + '</b></span> ';
    row += '<span class="browsertest-value';
    // Adds an extra class to the value, if passed.
    if (valclass !== '') {
      row += ' ' + valclass;
    }
    row += '">';
    // Strip tags on the value to prevent malicious doings.
    value = value.replace(/(<([^>]+)>)/ig,"");
    row += value;
    row += '</span>';
    row += '</div> ';
    return row;
  }

  // Displays a message.
  browsertest.displayMessage = function (message) {
    return '<div class="browsertest-message">' + message + '</div>';
  }

  // Displays the full browser test.
  browsertest.displayDetails = function () {

    let details = '';
    let val = '';
    let valclass = '';

    // Set JS enabled to YES - NO is the default.
    details += browsertest.displayRow('JavaScript Enabled', 'YES');

    // Display a user-friendly form of the O/S name.
    if (osName) {
      valclass = 'affirm';
      let val = osName;
      details += browsertest.displayRow('Operating System', val, valclass);
    }

    // Display a user-friendly form of the web browser's name and version number.
    // This info is pulled from the browser_detect.js file.
    valclass = 'negative';
    if (browserName) {
      valclass = 'affirm';
      val = browserName + ' ' + browserVersion;
    } else {
      val = '* Not Recognized *';
    }
    details += browsertest.displayRow('Browser Name and Version', val, valclass);

    // Check user's screen resolution
    val = screen.width + ' x ' + screen.height;
    details += browsertest.displayRow('Screen Resolution', val);

    // Ip Address.
    details += browsertest.displayRow('IP Address', browsertest.getIPaddress());

    // Referring URL.
    details += browsertest.displayRow('Came to arvest.com via', drupalSettings.arvestbank_browsertest.referer);

    // Check to see if the user has pop-ups blocked
    let myPopup = window.open("","test","width=40,height=40,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0");
    valclass = 'negative';
    if ((myPopup) && (typeof(myPopup) == "object")) {
      myPopup.close();
      valclass = 'affirm';
      val = 'YES';
    }
    else {
      val = 'NO';
    }
    details += browsertest.displayRow('Pop-up Windows Enabled', val, valclass);

    // Check to see if the user has session-based cookies blocked.
    valclass = 'negative';
    if (navigator.cookieEnabled) {
      valclass = 'affirm';
      val = 'YES';
    }
    else {
      val = 'NO';
      if ((browserName.indexOf("Microsoft") > -1) && (osName.indexOf("Mac") > -1)) {
        val += '<p><a target="_new" href="https://www.aboutcookies.org/Default.aspx?page=1">How to enable cookies on Macintosh Internet Explorer Browsers</a></p>' + "\n";
      }
      else if ((browserName.indexOf("Microsoft") > -1) && (osName.indexOf("Win") > -1)) {
        val += '<p><a target="_new" href="https://www.aboutcookies.org/Default.aspx?page=1">How to enable cookies on Windows Internet Explorer Browsers</a></p>' + "\n";
      }
      else if (browserName.indexOf("Netscape") > -1) {
        val += '<p><a target="_new" href="https://www.aboutcookies.org/Default.aspx?page=1">How to enable cookies on Netscape Browsers</a></p>' + "\n";
      }
    }
    details += browsertest.displayRow('Cookies Enabled', val, valclass);

    return details;

  }

  // Store the browsertest details in a variable.
  let browsertest_details = browsertest.displayDetails();

  // Replace the div id="browsertest" with the complete browser test details.
  $('#browsertest').html(browsertest_details);

  // Replace the hidden div for the browsertest form, if available.
  let webform_diagnostics_field = $('#webform-submission-send-browser-diagnostics-add-form input[name="browser_diagnostics"]');
  if (webform_diagnostics_field.length) {
    let browsertest_details_txt = browsertest_details;
    // Puts some nice carraige returns in.
    browsertest_details_txt = browsertest_details_txt.replaceAll('<div class="browsertest-row">', "\r\n");
    // Colon after label.
    browsertest_details_txt = browsertest_details_txt.replaceAll('</b></span>', ": ");
    // Strip remaining HTML.
    browsertest_details_txt = browsertest_details_txt.replace(/(<([^>]+)>)/gi, "");
    // Set the webform value.
    webform_diagnostics_field.val(browsertest_details_txt);
  }

})(jQuery, Drupal, drupalSettings);
