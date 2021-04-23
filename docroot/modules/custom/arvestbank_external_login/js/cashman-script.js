/**
 * @file
 * Copied from https://ecash.arvest.com/CorporateBankingWeb/themes/TopTabMenu/Script/pm_fp.js.
 *
 * Contains script to compile browser data to send to cashman on login.
 */

var SEP='|';var PAIR='=';var DEV='~';var ver=1;ua=navigator.userAgent.toLowerCase();opera=ua.indexOf("opera")>=0;ie=ua.indexOf("msie")>=0&&!opera;iemac=ie&&ua.indexOf("mac")>=0;moz=ua.indexOf("mozilla")&&!ie&&!opera;os=navigator.platform;function activeXDetect(componentClassID)
{componentVersion=document.body.getComponentVersion('{'+componentClassID+'}','ComponentID');return(componentVersion!=null)?componentVersion:false;}
function extractVersions(s)
{extractedVersions="";for(var i=0;i<s.length;i++){charAtValue=s.charAt(i);if((charAtValue>='0'&&charAtValue<='9')||charAtValue=='.'||charAtValue=='_'||charAtValue==',')
  extractedVersions+=charAtValue;}
  return extractedVersions;}
function stripIllegalChars(value)
{t="";value=value.toLowerCase();for(i=0;i<value.length;i++){if(value.charAt(i)!='\n'&&value.charAt(i)!='/'&&value.charAt(i)!="\\")
{t+=value.charAt(i);}
else if(value.charAt(i)=='\n')
{t+="n";}}
  return t;}
function stripFullPath(tempFileName,lastDir)
{fileName=tempFileName;filenameStart=0;filenameStart=fileName.lastIndexOf(lastDir);if(filenameStart<0)
  return tempFileName;filenameFinish=fileName.length;fileName=fileName.substring(filenameStart+lastDir.length,filenameFinish);return fileName;}
function deviceprint_browser()
{t=ua+SEP+navigator.appVersion+SEP+navigator.platform;if(ie)
{t+=SEP+navigator.appMinorVersion+SEP+navigator.cpuClass+SEP+navigator.browserLanguage;t+=SEP+ScriptEngineBuildVersion();}
else if(moz)
{t+=SEP+navigator.language;}
  return t;}
function deviceprint_display()
{t="";if(self.screen)
{t+=screen.colorDepth+SEP+screen.width+SEP+screen.height+SEP+screen.availHeight;}
  return t;}
function deviceprint_software()
{t="";isFirst=true;var ht=new Hashtable();ht.put('npnul32.dll','def');ht.put('npqtplugin6.dll','qt6');ht.put('npqtplugin5.dll','qt5');ht.put('npqtplugin4.dll','qt4');ht.put('npqtplugin3.dll','qt3');ht.put('npqtplugin2.dll','qt2');ht.put('npqtplugin.dll','qt1');ht.put('nppdf32.dll','pdf');ht.put('NPSWF32.dll','swf');ht.put('NPJava11.dll','j11');ht.put('NPJava12.dll','j12');ht.put('NPJava13.dll','j13');ht.put('NPJava32.dll','j32');ht.put('NPJava14.dll','j14');ht.put('npoji600.dll','j61');ht.put('NPJava131_16.dll','j16');ht.put('NPOFFICE.DLL','mso');ht.put('npdsplay.dll','wpm');ht.put('npwmsdrm.dll','drm');ht.put('npdrmv2.dll','drn');ht.put('nprjplug.dll','rjl');ht.put('nppl3260.dll','rpl');ht.put('nprpjplug.dll','rpv');ht.put('npchime.dll','chm');ht.put('npCortona.dll','cor');ht.put('np32dsw.dll','dsw');ht.put('np32asw.dll','asw');if(navigator.plugins.length>0)
{temp="";moz="";key="";lastDir="Plugins";for(i=0;i<navigator.plugins.length;i++)
{plugin=navigator.plugins[i];moz=stripFullPath(plugin.filename,lastDir);if(isFirst==true)
{key=ht.containsKey(moz);if(key)
{temp+=ht.get(moz);isFirst=false;}
else
{temp="";isFirst=false;}}
else
{key=ht.containsKey(moz);if(key)
{temp+=SEP+ht.get(moz);}
else
{temp+="";}}}
  t=stripIllegalChars(temp);}
else if(navigator.mimeTypes.length>0)
{key="";for(i=0;i<navigator.mimeTypes.length;i++)
{mimeType=navigator.mimeTypes[i];if(isFirst==true)
{key=ht.containsKey(mimeType);if(key)
{t+=ht.get(mimeType)+PAIR+mimeType;isFirst=false;}
else
{t+="unknown"+PAIR+mimeType;isFirst=false;}}
else
{key=ht.containsKey(mimeType);if(key)
{t+=SEP+ht.get(mimeType)+PAIR+mimeType;}
else
{temp+="";}}}}
else if(ie)
{names=new Array("abk","wnt","aol","arb","chs","cht","dht","dhj","dan","dsh","heb","ie5","icw","ibe","iec","ieh","iee","jap","krn","lan","swf","shw","msn","wmp","obp","oex","net","pan","thi","tks","uni","vtc","vnm","mvm","vbs","wfd");components=new Array("7790769C-0471-11D2-AF11-00C04FA35D02","89820200-ECBD-11CF-8B85-00AA005B4340","47F67D00-9E55-11D1-BAEF-00C04FC2D130","76C19B38-F0C8-11CF-87CC-0020AFEECF20","76C19B34-F0C8-11CF-87CC-0020AFEECF20","76C19B33-F0C8-11CF-87CC-0020AFEECF20","9381D8F2-0288-11D0-9501-00AA00B911A5","4F216970-C90C-11D1-B5C7-0000F8051515","283807B5-2C60-11D0-A31D-00AA00B92C03","44BBA848-CC51-11CF-AAFA-00AA00B6015C","76C19B36-F0C8-11CF-87CC-0020AFEECF20","89820200-ECBD-11CF-8B85-00AA005B4383","5A8D6EE0-3E18-11D0-821E-444553540000","630B1DA0-B465-11D1-9948-00C04F98BBC9","08B0E5C0-4FCB-11CF-AAA5-00401C608555","45EA75A0-A269-11D1-B5BF-0000F8051515","DE5AED00-A4BF-11D1-9948-00C04F98BBC9","76C19B30-F0C8-11CF-87CC-0020AFEECF20","76C19B31-F0C8-11CF-87CC-0020AFEECF20","76C19B50-F0C8-11CF-87CC-0020AFEECF20","D27CDB6E-AE6D-11CF-96B8-444553540000","2A202491-F00D-11CF-87CC-0020AFEECF20","5945C046-LE7D-LLDL-BC44-00C04FD912BE","22D6F312-B0F6-11D0-94AB-0080C74C7E95","3AF36230-A269-11D1-B5BF-0000F8051515","44BBA840-CC51-11CF-AAFA-00AA00B6015C","44BBA842-CC51-11CF-AAFA-00AA00B6015B","76C19B32-F0C8-11CF-87CC-0020AFEECF20","76C19B35-F0C8-11CF-87CC-0020AFEECF20","CC2A9BA0-3BDD-11D0-821E-444553540000","3BF42070-B3B1-11D1-B5C5-0000F8051515","10072CEC-8CC1-11D1-986E-00A0C955B42F","76C19B37-F0C8-11CF-87CC-0020AFEECF20","08B0E5C0-4FCB-11CF-AAA5-00401C608500","4F645220-306D-11D2-995D-00C04F98BBC9","73FA19D0-2D75-11D2-995D-00C04F98BBC9");document.body.addBehavior("#default#clientCaps")
  for(i=0;i<components.length;i++)
  {ver=activeXDetect(components[i]);var name=names[i];if(ver)
  {if(isFirst==true)
  {t+=name+PAIR+ver;isFirst=false;}
  else
  {t+=SEP+name+PAIR+ver;}}
  else
  {t+="";isFirst=false;}}}
  return t;}
function deviceprint_timezone(){var gmtHours=(new Date().getTimezoneOffset()/60)*(-1);return gmtHours;}
function deviceprint_language(){var lang;if(typeof(navigator.language)!="undefined")
{lang="lang"+PAIR+navigator.language+SEP;}
else if(typeof(navigator.browserLanguage)!="undefined")
{lang="lang"+PAIR+navigator.browserLanguage+SEP;}
else
{lang="lang"+PAIR+""+SEP;}
  (typeof(navigator.systemLanguage)!="undefined")?lang+="syslang"+PAIR+navigator.systemLanguage+SEP:lang+="syslang"+PAIR+""+SEP;(typeof(navigator.userLanguage)!="undefined")?lang+="userlang"+PAIR+navigator.userLanguage:lang+="userlang"+PAIR+"";return lang;}
function deviceprint_java()
{var javaEnabled;javaEnabled=(navigator.javaEnabled())?1:0
  return javaEnabled;}
function deviceprint_cookie()
{var cookieEnabled=(navigator.cookieEnabled)?1:0
  if(typeof navigator.cookieEnabled=="undefined"&&!cookieEnabled)
  {document.cookie="testcookie"
    cookieEnabled=(document.cookie.indexOf("testcookie")!=-1)?1:0}
  return cookieEnabled;}
function form_add_data(fd,name,value){if(fd&&fd.length>0){fd+="&";}
else{fd="";}
  fd+=name+'='+escape(value);return fd;}
function form_add_deviceprint(fd,name,value){fd=form_add_data(fd,name+"d",value);return fd;}
function asyncpost_deviceprint(url){var xmlhttp=false;/*@cc_on @*/
  /*@if (@_jscript_version >= 5)
      // JScript gives us Conditional compilation, we can cope with old IE versions.
      // and security blocked creation of the objects.
       try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
       } catch (e) {
        try {
         xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
         xmlhttp = false;
        }
       }
      @end @*/
  if(!xmlhttp&&typeof XMLHttpRequest!='undefined'){xmlhttp=new XMLHttpRequest();}
  if(!xmlhttp)return false;xmlhttp.open("POST",url,true);xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");var fd;fd=form_add_deviceprint(fd,"fp_browser",deviceprint_browser());fd=form_add_deviceprint(fd,"fp_display",deviceprint_display());fd=form_add_deviceprint(fd,"fp_software",deviceprint_software());fd=form_add_deviceprint(fd,"fp_timezone",deviceprint_timezone());fd=form_add_deviceprint(fd,"fp_language",deviceprint_language());fd=form_add_deviceprint(fd,"fp_java",deviceprint_java());fd=form_add_deviceprint(fd,"fp_cookie",deviceprint_cookie());xmlhttp.send(fd);return true;}
function post_deviceprint(){document.forms[0].pm_fp.value=encode_deviceprint();return true;}
function URLencode(text)
{encodedString=escape(text).replace(/\*/g,"%2A").replace(/\+/g,"%2B").replace(/-/g,"%2D").replace(/\./g,"%2E").replace(/\//,"%2F").replace(/_/g,"%5F").replace(/@/g,"%40");return encodedString;}
function encode_deviceprint()
{var t="version="+ver+"&pm_fpua="+deviceprint_browser("")+"&pm_fpsc="+deviceprint_display("")+"&pm_fpsw="+deviceprint_software("")+"&pm_fptz="+deviceprint_timezone("")+"&pm_fpln="+deviceprint_language("")+"&pm_fpjv="+deviceprint_java("")+"&pm_fpco="+deviceprint_cookie("");return URLencode(t);}
function decode_deviceprint()
{var t="version="+ver+"&pm_fpua="+deviceprint_browser("")+"&pm_fpsc="+deviceprint_display("")+"&pm_fpsw="+deviceprint_software("")+"&pm_fptz="+deviceprint_timezone("")+"&pm_fpln="+deviceprint_language("")+"&pm_fpjv="+deviceprint_java("")+"&pm_fpco="+deviceprint_cookie("");return unescape(URLencode(t));}
function add_deviceprint()
{var t="version="+ver+"&pm_fpua="+deviceprint_browser("")+"&pm_fpsc="+deviceprint_display("")+"&pm_fpsw="+deviceprint_software("")+"&pm_fptz="+deviceprint_timezone("")+"&pm_fpln="+deviceprint_language("")+"&pm_fpjv="+deviceprint_java("")+"&pm_fpco="+deviceprint_cookie("");return t;}
function Hashtable()
{var keysToIndex={__indexToValue:[],__indexToKeys:[]};var activeEnum=[];var tableLength=0;var self=this;function Enumeration(arrNm)
{var lastIndex=null;var enumIndex=0;while(typeof activeEnum[enumIndex]=='number')enumIndex+=1;activeEnum[enumIndex]=0;this.hasNext=this.hasMoreElements=function(){if(activeEnum[enumIndex]<tableLength){return true;}else{if(typeof activeEnum[enumIndex]=='number'){activeEnum[enumIndex]=null;}
  return false;}};this.next=this.nextElement=function(){if(this.hasNext){lastIndex=activeEnum[enumIndex];return keysToIndex[arrNm][activeEnum[enumIndex]++];}else{return null;}};this.remove=function(){if(typeof lastIndex=='number'){self.remove(keysToIndex.__indexToKeys[lastIndex]);lastIndex=null;}};};this.get=function(key){if(typeof keysToIndex[key]=='number'){return keysToIndex.__indexToValue[keysToIndex[key]];}else{return null;}};this.put=function(key,value){if(typeof keysToIndex[key]=='number'){keysToIndex.__indexToValue[keysToIndex[key]]=value;}else{keysToIndex[key]=tableLength;keysToIndex.__indexToValue[tableLength]=value;keysToIndex.__indexToKeys[tableLength++]=key;}};this.remove=function(key){var remIndex=keysToIndex[key];if(typeof remIndex=='number'){delete keysToIndex[key];tableLength-=1;for(var c=remIndex;c<tableLength;c++){keysToIndex.__indexToValue[c]=keysToIndex.__indexToValue[c+1];keysToIndex[(keysToIndex.__indexToKeys[c]=keysToIndex.__indexToKeys[c+1])]=c;}
  for(var c=0;c<activeEnum.length;c++){if((activeEnum[c])&&(remIndex<activeEnum[c])){activeEnum[c]-=1;}}}};this.size=function(){return tableLength;};this.__enumerate=function(type){return new Enumeration(type);};Hashtable.prototype.elements=function(){return this.__enumerate('__indexToValue');}
  Hashtable.prototype.keys=function(){return this.__enumerate('__indexToKeys');}
  Hashtable.prototype.clear=function(){var e=this.keys();while(e.hasNext()){this.remove(e.next());}}
  Hashtable.prototype.toString=function(){var n,e=this.keys();var st='';while(e.hasNext()){n=e.next();st+=n+' =&gt; '+this.get(n)+'\r\n';}
    return st;}
  Hashtable.prototype.contains=function(testVal){var e=this.elements();while(e.hasNext()){if(e.next()==testVal)return true;}
    return false;}
  Hashtable.prototype.containsValue=Hashtable.prototype.contains;Hashtable.prototype.containsKey=function(testKey){return(this.get(testKey)!=null);}
  Hashtable.prototype.isEmpty=function(){return(this.size()==0);}
  Hashtable.prototype.putAll=function(hTable){if(hTable.constructor==Hashtable){var n,e=hTable.keys();while(e.hasNext()){n=e.next();this.put(n,hTable.get(n));}}}
  Hashtable.prototype.clone=function(){var ht=new Hashtable();ht.putAll(this);return ht;}
  Hashtable.prototype.equals=function(o){return(o==this);}}
