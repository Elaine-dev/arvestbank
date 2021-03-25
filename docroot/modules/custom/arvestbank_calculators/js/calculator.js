// When the calc is in an iframe, the Drupal settings will be slightly different.
if (drupalSettings.arvestbank_calculators === undefined) {
  var settingsElement = document.querySelector('script[type="application/json"][data-drupal-selector="drupal-settings-json"]');
  var drupalSettings = JSON.parse(settingsElement.textContent);
}

TEMPLATE_ID = "www.arvest.com_2";
CALCULATORID = drupalSettings.arvestbank_calculators.calc_id;
PASSTHROUGH = "";
/* If you need to modify our calculator HTML after it has loaded, define a function called tvcAfterCalculatorsHaveLoadedFnc.  */
/* For example: tvcAfterCalculatorsHaveLoadedFnc = customerDefinedCallbackFnc() { var ele = document.getElementById('tvcPC01TitleId'); if (ele !== null) {ele.innerHTML = "How much can I afford?"; }} */

var tvcScriptElement = document.createElement('script');
var tvcCalculatorHtml = "";
var tvcHttp;
if (document.location.href.substring(0, 5) == "https") { tvcHttp = "https://"; } else { tvcHttp = "http://"; }
tvcScriptElement.src = tvcHttp + "www.TimeValueCalculators.com/timevaluecalculators/Calculator2.aspx?version=" + Math.random() + "&" + createQueryString();
tvcScriptElement.onload = tvcOnceLoaded;
document.getElementById('tvcMainCalculatorDivId').appendChild(tvcScriptElement);
