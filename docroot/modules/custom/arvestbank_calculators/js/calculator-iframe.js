var iframe = document.getElementById("CalcFrame");
iframe.onload = function (){
  iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 40 + 'px';
};
