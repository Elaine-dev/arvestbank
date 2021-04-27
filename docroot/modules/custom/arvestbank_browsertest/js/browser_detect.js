//
// This file determines the browser

osName = false;
winFlag = false;
macFlag = false;
IEWinFlag = false;
browserName = "";
browserVersion = "";
bdMessage = "";

if ((navigator.userAgent.indexOf("Windows") > -1) ||
 (navigator.userAgent.indexOf("WinNT") > -1)) {
winFlag=true;

if ( matches = navigator.userAgent.match(/Opera\/([\d\.]+)\b/) ) {
  //console.log('matches',matches);
  browserName = 'Opera';
  browserVersion = matches[1];
/*
} else if ((navigator.userAgent.indexOf("Opera 8") > -1) || (navigator.userAgent.indexOf("Opera/8") > -1)) {
  browserName="Opera";
  browserVersion="8";

} else if ((navigator.userAgent.indexOf("Opera 7") > -1) || (navigator.userAgent.indexOf("Opera/7") > -1)) {
  browserName="Opera";
  browserVersion="7";

} else if ((navigator.userAgent.indexOf("Opera 6") > -1) || (navigator.userAgent.indexOf("Opera/6") > -1)) {
  browserName="Opera";
  browserVersion="6";

} else if ((navigator.userAgent.indexOf("Opera") > -1)) {
  browserName="Opera";
  browserVersion="Unknown";
*/

} else if (navigator.userAgent.indexOf("Edge") > -1) {
  browserName="Microsoft Edge";
  if (navigator.userAgent.indexOf("/12") > -1) {
    browserVersion="12.246";
  }

} else if (navigator.userAgent.indexOf("AOL/7") > -1) {
  browserName="America Online";
  browserVersion="7";

} else if (navigator.userAgent.indexOf("AOL\s8") > -1) {
  browserName="America Online (Internet Explorer)";
  browserVersion="8";

} else if (navigator.userAgent.indexOf("AOL\s9") > -1) {
  browserName="America Online (Internet Explorer)";
  browserVersion="9";

} else if (navigator.userAgent.indexOf("MSIE 5") > -1) {
  IEWinFlag = true;
  if (navigator.userAgent.indexOf("MSIE 5.5") > -1) {
    browserName="Microsoft Internet Explorer";
    browserVersion="5.5";
  } else {
    browserName="Microsoft Internet Explorer";
    browserVersion="5.x";
  }

} else if (navigator.userAgent.indexOf("MSIE 6") > -1) {
  IEWinFlag = true;
  browserName="Microsoft Internet Explorer";
  browserVersion="6";
} else if (navigator.userAgent.indexOf("MSIE 7") > -1) {
  IEWinFlag = true;
  browserName="Microsoft Internet Explorer";
  browserVersion="7";
  var re = new RegExp("Trident\/([0-9]{1,}[\.0-9]{0,})");
  var rv = -1;
  if (re.exec(navigator.userAgent) != null)
      rv = parseFloat(RegExp.$1);
    if ( rv == 4 )
      browserVersion = "8 Running in compat mode";
    else if ( rv == 5 )
      browserVersion = "9 Running in compat mode";
    else if ( rv == 6 )
      browserVersion = "10 Running in compat mode";
} else if (navigator.userAgent.indexOf("MSIE 8") > -1) {
  IEWinFlag = true;
  browserName="Microsoft Internet Explorer";
  browserVersion="8";
} else if (navigator.userAgent.indexOf("MSIE 9") > -1) {
  IEWinFlag = true;
  browserName="Microsoft Internet Explorer";
  browserVersion="9";
  var re = new RegExp("Trident\/([0-9]{1,}[\.0-9]{0,})");
  var rv = -1;
  if (re.exec(navigator.userAgent) != null)
      rv = parseFloat(RegExp.$1);
    if ( rv == 6 )
      browserVersion = "10";

} else if (navigator.userAgent.indexOf("MSIE 10") > -1) {
    IEWinFlag = true;
      browserName="Microsoft Internet Explorer";
      browserVersion="10";

} else if (navigator.userAgent.indexOf("rv:11.0") > -1) {
    IEWinFlag = true;
      browserName="Microsoft Internet Explorer";
      browserVersion="11";

/*
else if (navigator.userAgent.indexOf("Netscape/6.2") > -1) {
  browserName="Netscape Navigator";
  browserVersion="6.2.x";

} else if (navigator.userAgent.indexOf("Netscape6/6.2") > -1) {
  browserName="Netscape Navigator";
  browserVersion="6.2.x";

} else if (navigator.userAgent.indexOf("Netscape/7.2") > -1) {
  browserName="Netscape Navigator";
  browserVersion="7.2";

} else if (navigator.userAgent.indexOf("Netscape/7.1") > -1) {
  browserName="Netscape Navigator";
  browserVersion="7.1";

} else if (navigator.userAgent.indexOf("Netscape/7") > -1) {
  browserName="Netscape Navigator";
  browserVersion="7.x";

} else if (navigator.userAgent.indexOf("Netscape/8") > -1) {
  browserName="Netscape Navigator";
  browserVersion="8.x";

} else if (navigator.userAgent.indexOf("Mozilla/4") > -1) {
  browserName="Netscape Navigator";
  browserVersion="4.x";

} else if (navigator.userAgent.indexOf("Mozilla/4.76") > -1) {
  browserName="Netscape Navigator";
  browserVersion="4.76";
}
*/
}
else if ( matches = navigator.userAgent.match(/Firefox\/([\d\.]+)\b/) ) {
  //console.log('matches',matches);
  browserName = 'Mozilla Firefox';
  browserVersion = matches[1];
}

else if ( matches = navigator.userAgent.match(/Chrome\/([\d\.]+)\b/) ) {
  browserName="Google Chrome";
  browserVersion = matches[1];
}

else if (navigator.userAgent.indexOf("Safari") > -1) {
  browserName="Apple Safari";
  if (navigator.userAgent.indexOf("/531") > -1) {
    browserVersion="4.0.5";
  } else if (navigator.userAgent.indexOf("/533") > -1) {
    browserVersion="5.0";
  } else if (navigator.userAgent.indexOf("/534") > -1) {
    browserVersion="5.1";
  } else if (navigator.userAgent.indexOf("/536") > -1) {
    browserVersion="6.0";
  } else {
    browserVersion="Unknown";
  }
}

else if ( matches = navigator.userAgent.match(/(?:Netscape|Mozilla)\/([\d\.]+)\b/) ) {
  //console.log('matches',matches);
  browserName = 'Netscape Navigator';
  browserVersion = matches[1];
}

else if ((navigator.userAgent.indexOf("Mozilla") > -1) && (navigator.userAgent.indexOf("Gecko") > -1) ) {
  if (navigator.userAgent.indexOf("rv:1.5") > -1) {
    browserName="Mozilla";
    browserVersion="1.5";
  } else if (navigator.userAgent.indexOf("rv:1.6") > -1) {
    browserName="Mozilla";
    browserVersion="1.6";
  } else if (navigator.userAgent.indexOf("rv:1.7") > -1) {
    browserName="Mozilla";
    browserVersion="1.7";
  }

}

} else if ( (navigator.userAgent.indexOf("PowerPC") > -1) || (navigator.userAgent.indexOf("Macintosh") > -1) || (navigator.userAgent.indexOf("Intel Mac") > -1)) {

macFlag=true;
osName="Macintosh OS";

if ( matches = navigator.userAgent.match(/Opera\/([\d\.]+)\b/) ) {
  //console.log('matches',matches);
  browserName = 'Opera';
  browserVersion = matches[1];

/*
if ((navigator.userAgent.indexOf("Opera 9") > -1) || (navigator.userAgent.indexOf("Opera/9") > -1)) {
  browserName="Opera";
  browserVersion="9";

} else if ((navigator.userAgent.indexOf("Opera 8") > -1) || (navigator.userAgent.indexOf("Opera/8") > -1)) {
  browserName="Opera";
  browserVersion="8";

} else if ((navigator.userAgent.indexOf("Opera 7") > -1) || (navigator.userAgent.indexOf("Opera/7") > -1)) {
  browserName="Opera";
  browserVersion="7";

} else if ((navigator.userAgent.indexOf("Opera 6") > -1) || (navigator.userAgent.indexOf("Opera/6") > -1)) {
  browserName="Opera";
  browserVersion="6";

} else if ((navigator.userAgent.indexOf("Opera") > -1)) {
  browserName="Opera";
  browserVersion="Unknown";
*/

} else if ((navigator.userAgent.indexOf("MSIE") == -1) && ((navigator.userAgent.indexOf("Mac OS X") == -1) && (navigator.userAgent.indexOf("68k") > -1) || (navigator.userAgent.indexOf("68K") > -1))) {
  bdMessage="We suggest upgrading to OS X!";
  browserName="Old Mac 68K Browser";
  browserVersion="";
} else if (navigator.userAgent.indexOf("MSIE 5") > -1) {
  browserName="Microsoft Internet Explorer";
  browserVersion="5.x";

} else if ( matches = navigator.userAgent.match(/Chrome\/([\d\.]+)\b/) ) {
  browserName="Google Chrome";
  browserVersion = matches[1];

} else if (navigator.userAgent.indexOf("Netscape/6.2") > -1) {
  browserName="Netscape Navigator";
  browserVersion="6.2.x";

} else if (navigator.userAgent.indexOf("Netscape/7.2") > -1) {
  browserName="Netscape Navigator";
  browserVersion="7.2";

} else if (navigator.userAgent.indexOf("Netscape/7.1") > -1) {
  browserName="Netscape Navigator";
  browserVersion="7.1";

} else if (navigator.userAgent.indexOf("Netscape/7") > -1) {
  browserName="Netscape Navigator";
  browserVersion="7.x";

} else if (navigator.userAgent.indexOf("Netscape/8") > -1) {
  browserName="Netscape Navigator";
  browserVersion="8.x";

} else if (navigator.userAgent.indexOf("Safari") > -1) {
  browserName="Apple Safari";
  if (navigator.userAgent.indexOf("/85") > -1) {
    browserVersion="1.0";
  } else if (navigator.userAgent.indexOf("/100") > -1) {
    browserVersion="1.1";
  } else if (navigator.userAgent.indexOf("/125") > -1) {
    browserVersion="1.2";
  } else if (navigator.userAgent.indexOf("/312") > -1) {
    browserVersion="1.3";
  } else if (navigator.userAgent.indexOf("/412") > -1) {
    browserVersion="2.0";
  } else if (navigator.userAgent.indexOf("/416") > -1) {
    browserVersion="2.02";
  } else if (navigator.userAgent.indexOf("/417") > -1) {
    browserVersion="2.03";
  } else if (navigator.userAgent.indexOf("/419") > -1) {
    browserVersion="2.04";
  } else if (navigator.userAgent.indexOf("/525") > -1) {
    browserVersion="3.1.1";
  } else if (navigator.userAgent.indexOf("/531") > -1) {
    browserVersion="4.0.3";
  } else if (navigator.userAgent.indexOf("/533") > -1) {
    browserVersion="5.0";
  } else if (navigator.userAgent.indexOf("/534") > -1) {
    browserVersion="5.1";
  } else if (navigator.userAgent.indexOf("/536") > -1) {
    browserVersion="6.0";
  } else if (navigator.userAgent.indexOf("/537") > -1) {
    browserVersion="7.0";
  } else {
    browserVersion="Unknown";
  }
}
/*
else if ((navigator.userAgent.indexOf("Firefox/0") > -1)) {
  browserName="Mozilla Firefox";
  browserVersion="0.x";

} else if ((navigator.userAgent.indexOf("Firefox/1.5") > -1)) {
  browserName="Mozilla Firefox";
  browserVersion="1.5.x";

} else if ((navigator.userAgent.indexOf("Firefox/1") > -1)) {
  browserName="Mozilla Firefox";
  browserVersion="1.x";

} else if ((navigator.userAgent.indexOf("Firefox/2") > -1)) {
  browserName="Mozilla Firefox";
  browserVersion="2.x";

} else if ((navigator.userAgent.indexOf("Firefox/3") > -1)) {
  browserName="Mozilla Firefox";
  browserVersion="3.x";
}
*/
else if ( matches = navigator.userAgent.match(/Firefox\/([\d\.]+)\b/) ) {
  //console.log('matches',matches);
  browserName = 'Mozilla Firefox';
  browserVersion = matches[1];
}
else if ((navigator.userAgent.indexOf("Mozilla") > -1) && (navigator.userAgent.indexOf("Gecko") > -1) ) {
  if (navigator.userAgent.indexOf("rv:1.5") > -1) {
    browserName="Mozilla";
    browserVersion="1.5";
  } else if (navigator.userAgent.indexOf("rv:1.6") > -1) {
    browserName="Mozilla";
    browserVersion="1.5";
  } else if (navigator.userAgent.indexOf("rv:1.7") > -1) {
    browserName="Mozilla";
    browserVersion="1.5";
  }
}

}

if (winFlag) {
if (navigator.userAgent.indexOf("Windows NT 4.0") > -1) {
  osName="Windows NT 4.0";
} else if (navigator.userAgent.indexOf("WinNT") > -1) {
  osName="Windows NT 4.0";
} else if (navigator.userAgent.indexOf("Windows 98") > -1) {
  osName="Windows 98";
} else if (navigator.userAgent.indexOf("Win98") > -1) {
  osName="Windows 98";
} else if (navigator.userAgent.indexOf("Windows NT 5.0") > -1) {
  osName="Windows 2000";
} else if (navigator.userAgent.indexOf("Windows NT 5.1") > -1) {
  osName="Windows XP";
  if (navigator.userAgent.indexOf("SV1") > -1) {
    bdMessage='<p>You appear to be running Service Pack 2.  If so, make certain that the new pop-up blocker is not enabled.  Look on the Privacy tab of your Internet Options control panel for this setting.</p>';
    osName=osName+" SP2";
  }
} else if (navigator.userAgent.indexOf("Windows NT 6.0") > -1) {
  osName="Windows Vista";
} else if (navigator.userAgent.indexOf("Windows NT 6.1") > -1) {
  osName="Windows 7";
} else if (navigator.userAgent.indexOf("Windows NT 6.3") > -1) {
  osName="Windows 8";
} else if (navigator.userAgent.indexOf("Windows NT 10.0") > -1) {
  osName="Windows 10";
}
}
