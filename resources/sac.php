<?php // Simple Ajax Chat > JavaScript

	header("Cache-Control: must-revalidate");
	$offset = 60*60*24*60;
	$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
	header($ExpStr);
	header('Content-Type: application/javascript');
	
	include("../../../../wp-config.php");
	
	$sac_options = get_option('sac_options'); 
	
	$use_username = $sac_options['sac_logged_name'];
	$current_user = wp_get_current_user();
	$logged_username = sanitize_text_field($current_user->display_name);
	
?>
// Fade Anything Technique by Adam Michela
var Fat={make_hex:function(d,c,a){d=d.toString(16);if(d.length==1){d="0"+d}c=c.toString(16);if(c.length==1){c="0"+c}a=a.toString(16);if(a.length==1){a="0"+a}return"#"+d+c+a},fade_all:function(){var b=document.getElementsByTagName("*");for(var c=0;c<b.length;c++){var e=b[c];var d=/fade-?(\w{3,6})?/.exec(e.className);if(d){if(!d[1]){d[1]=""}if(e.id){Fat.fade_element(e.id,null,null,"#"+d[1])}}}},fade_element:function(m,c,a,o,d){if(!c){c=30}if(!a){a=3000}if(!o||o=="#"){o="#FFFF33"}if(!d){d=this.get_bgcolor(m)}var i=Math.round(c*(a/1000));var s=a/i;var w=s;var j=0;if(o.length<7){o+=o.substr(1,3)}if(d.length<7){d+=d.substr(1,3)}var n=parseInt(o.substr(1,2),16);var u=parseInt(o.substr(3,2),16);var e=parseInt(o.substr(5,2),16);var f=parseInt(d.substr(1,2),16);var l=parseInt(d.substr(3,2),16);var t=parseInt(d.substr(5,2),16);var k,q,v,p;while(j<i){k=Math.floor(n*((i-j)/i)+f*(j/i));q=Math.floor(u*((i-j)/i)+l*(j/i));v=Math.floor(e*((i-j)/i)+t*(j/i));p=this.make_hex(k,q,v);setTimeout("Fat.set_bgcolor('"+m+"','"+p+"')",w);j++;w=s*j}setTimeout("Fat.set_bgcolor('"+m+"','"+d+"')",w)},set_bgcolor:function(d,b){var a=document.getElementById(d);a.style.backgroundColor=b},get_bgcolor:function(e){var b=document.getElementById(e);while(b){var d;if(window.getComputedStyle){d=window.getComputedStyle(b,null).getPropertyValue("background-color")}if(b.currentStyle){d=b.currentStyle.backgroundColor}if((d!=""&&d!="transparent")||b.tagName=="BODY"){break}b=b.parentNode}if(d==undefined||d==""||d=="transparent"){d="#FFFFFF"}var a=d.match(/rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)/);if(a){d=this.make_hex(parseInt(a[1]),parseInt(a[2]),parseInt(a[3]))}return d}};

// smilies
var smilies=[[":\\)","icon_smile.gif"],[":\\-\\)","icon_smile.gif"],[":D","icon_biggrin.gif"],[":\\-D","icon_biggrin.gif"],[":grin:","icon_biggrin.gif"],[":smile:","icon_smile.gif"],[":\\(","icon_sad.gif"],[":\\-\\(","icon_sad.gif"],[":sad:","icon_sad.gif"],[":o","icon_surprised.gif"],[":\\-o","icon_surprised.gif"],["8o","icon_eek.gif"],["8\\-o","icon_eek.gif"],["8\\-0","icon_eek.gif"],[":eek:","icon_surprised.gif"],[":s","icon_confused.gif"],[":\\-s","icon_confused.gif"],[":lol:","icon_lol.gif"],[":cool:","icon_cool.gif"],["8\\)","icon_cool.gif"],["8\\-\\)","icon_cool.gif"],[":x","icon_mad.gif"],[":-x","icon_mad.gif"],[":mad:","icon_mad.gif"],[":p","icon_razz.gif"],[":\\-p","icon_razz.gif"],[":razz:","icon_razz.gif"],[":\\$","icon_redface.gif"],[":\\-\\$","icon_redface.gif"],[":'\\(","icon_cry.gif"],[":evil:","icon_evil.gif"],[":twisted:","icon_twisted.gif"],[":cry:","icon_cry.gif"],[":roll:","icon_rolleyes.gif"],[":wink:","icon_wink.gif"],[";\\)","icon_wink.gif"],[";\\-\\)","icon_wink.gif"],[":!:","icon_exclaim.gif"],[":\\?","icon_question.gif"],[":\\-\\?","icon_question.gif"],[":idea:","icon_idea.gif"],[":arrow:","icon_arrow.gif"],[":\\|","icon_neutral.gif"],[":neutral:","icon_neutral.gif"],[":\\-\\|","icon_neutral.gif"],[":mrgreen:","icon_mrgreen.gif"]];
//
function sac_apply_filters(s) { return filter_smilies(make_links((s))); }
//
function filter_smilies(s){
	for (var i = 0; i < smilies.length; i++){
		var search = smilies[i][0];
		var replace = '<img src="<?php echo site_url(); ?>/wp-includes/images/smilies/' + smilies[i][1] + '" class="wp-smiley" border="0" style="border:none;" alt="' + smilies[i][0].replace(/\\/g, '') + '" />';
		re = new RegExp(search, 'gi');
		s = s.replace(re, replace);
	}
	return s;
}

// links
function make_links(s){ var re = /((http|https|ftp):\/\/[^ ]*)/gi; text = s.replace(re,"<a href=\"$1\" target=\"_blank\" class=\"sac-chat-link\">&laquo;link&raquo;</a>"); return text; }

// sound alerts
var myBox = new Object();
myBox.onInit = function(){}

// Generic onload @ http://www.brothercake.com/site/resources/scripts/onload/
if(typeof window.addEventListener!="undefined"){window.addEventListener("load",initJavaScript,false)}else{if(typeof document.addEventListener!="undefined"){document.addEventListener("load",initJavaScript,false)}else{if(typeof window.attachEvent!="undefined"){window.attachEvent("onload",initJavaScript)}}};

// XHTML live Chat by Alexander Kohlhofer
var sac_loadtimes;
var sac_org_timeout = <?php echo $sac_options['sac_update_seconds']; ?>;
var sac_timeout = sac_org_timeout;
var GetChaturl = "<?php echo plugins_url('simple-ajax-chat/simple-ajax-chat.php?sacGetChat=yes'); ?>";
var SendChaturl = "<?php echo plugins_url('simple-ajax-chat/simple-ajax-chat.php?sacSendChat=yes'); ?>";
var httpReceiveChat;
var httpSendChat;
function initJavaScript(){if(!document.getElementById("sac_chat")){return}document.forms["sac-form"].elements.sac_chat.setAttribute("autocomplete","off");checkStatus("");checkName();checkUrl();sac_loadtimes=1;httpReceiveChat=getHTTPObject();httpSendChat=getHTTPObject();setTimeout("receiveChatText()",sac_timeout);document.getElementById("sac_name").onblur=checkName;document.getElementById("sac_url").onblur=checkUrl;document.getElementById("sac_chat").onfocus=function(){checkStatus("active")};document.getElementById("sac_chat").onblur=function(){checkStatus("")};document.getElementById("submitchat").onclick=sendComment;document.getElementById("sac-form").onsubmit=function(){return false};document.getElementById("sac-output").onmouseover=function(){if(sac_loadtimes>9){sac_loadtimes=1;receiveChatText()}sac_timeout=sac_org_timeout}};
function receiveChatText(){sac_lastID=parseInt(document.getElementById("sac_lastID").value)-1;if(httpReceiveChat.readyState==4||httpReceiveChat.readyState==0){httpReceiveChat.open("GET",GetChaturl+"&sac_lastID="+sac_lastID+"&rand="+Math.floor(Math.random()*1000000),true);httpReceiveChat.onreadystatechange=handlehHttpReceiveChat;httpReceiveChat.send(null);sac_loadtimes++;if(sac_loadtimes>9){sac_timeout=sac_timeout*5/4}}setTimeout("receiveChatText()",sac_timeout)}function handlehHttpReceiveChat(){if(httpReceiveChat.readyState==4){results=httpReceiveChat.responseText.split("---");if(results.length>4){for(i=0;i<(results.length-1);i=i+5){insertNewContent(results[i+1],results[i+2],results[i+3],results[i+4],results[i]);document.getElementById("sac_lastID").value=parseInt(results[i])+1}sac_timeout=sac_org_timeout;sac_loadtimes=1}}};
function sendComment(){currentChatText=document.forms["sac-form"].elements.sac_chat.value;if(httpSendChat.readyState==4||httpSendChat.readyState==0){if(currentChatText==""){return}currentName=document.getElementById("sac_name").value;currentUrl=document.getElementById("sac_url").value;param="n="+encodeURIComponent(currentName)+"&c="+encodeURIComponent(currentChatText)+"&u="+encodeURIComponent(currentUrl);httpSendChat.open("POST",SendChaturl,true);httpSendChat.setRequestHeader("Content-Type","application/x-www-form-urlencoded");httpSendChat.onreadystatechange=receiveChatText;httpSendChat.send(param);document.forms["sac-form"].elements.sac_chat.value=""}};
//
function insertNewContent(liName,liText,lastResponse, liUrl, liId){
	response = document.getElementById("responseTime");
	response.replaceChild(document.createTextNode(lastResponse), response.firstChild);
	insertO = document.getElementById("sac-messages");

	var audio = document.getElementById("TheBox");
	if (audio) audio.play();

	oLi = document.createElement('li');
	oLi.setAttribute('id','comment-new'+liId);
	oSpan = document.createElement('span');
	oSpan.setAttribute('class','name');
	// date
	Date.prototype.today = function() {
		return this.getFullYear() + "/" + (((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) + "/" + ((this.getDate() < 10)?"0":"") + this.getDate();
	};
	// time
	Date.prototype.timeNow = function() {
		return ((this.getHours() < 10)?"0":"") + this.getHours() + ":" + ((this.getMinutes() < 10)?"0":"") + this.getMinutes() + ":" + ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
	};
	var newDate = new Date();
	var datetime = "<?php _e('Posted:', 'sac'); ?> " + newDate.today() + " @ " + newDate.timeNow();
	oSpan.setAttribute('title', datetime);
	oName = document.createTextNode(liName);
	
	<?php $use_url = $sac_options['sac_use_url']; if ($use_url) { ?>
	if (liUrl != "http://" && liUrl != '') {
		oURL = document.createElement('a');
		oURL.href = liUrl;
		oURL.target = "_blank";
		oURL.appendChild(oName);
	} else {
		oURL = oName;
	}
	oSpan.appendChild(oURL);
	<?php } else { ?>
	oURL = oName;
	oSpan.appendChild(oURL);
	<?php } ?>
	
	name_class = liName.replace(/[\s]+/g, "-");
	oLi.className = 'sac-chat-message sac-live sac-user-' + name_class;
	oSpan.appendChild(document.createTextNode(' : '));
	oLi.appendChild(oSpan);
	oLi.innerHTML += sac_apply_filters(liText);
	
	<?php $chat_order = $sac_options['sac_chat_order'];
	if ($chat_order) $child = 'last';
	else $child = 'first'; ?>
	
	insertO.insertBefore(oLi, insertO.<?php echo $child; ?>Child);

	<?php if ($chat_order) : ?>
	jQuery('#sac-output').animate({ scrollTop: jQuery('#sac-output').prop('scrollHeight') }, 300);
	<?php endif; ?>
	
	Fat.fade_element("comment-new"+liId, 30, <?php echo $sac_options['sac_fade_length']; ?>, "<?php echo $sac_options['sac_fade_from']; ?>", "<?php echo $sac_options['sac_fade_to']; ?>");
}

// textarea enter @ http://www.codingforums.com/showthread.php?t=63818
function pressedEnter(b,a){var c=a.keyCode?a.keyCode:a.which?a.which:a.charCode;if(c==13){sendComment();return false}else{return true}};

// chat status
function checkStatus(a){currentChatText=document.forms["sac-form"].elements.sac_chat;oSubmit=document.forms["sac-form"].elements.submit;if(currentChatText.value!=""||a=="active"){oSubmit.disabled=false}else{oSubmit.disabled=true}};

// get cookie
function sac_getCookie(c){var b=document.cookie;var e=c+"=";var d=b.indexOf("; "+e);if(d==-1){d=b.indexOf(e);if(d!=0){return null}}else{d+=2;var a=document.cookie.indexOf(";",d);if(a==-1){a=b.length}return unescape(b.substring(d+e.length,a))}};

// check name
function checkName(){
	sacCookie = sac_getCookie('sacUserName');
	currentName = document.getElementById('sac_name');
	<?php if (isset($use_username) && $use_username && !empty($logged_username)) : ?>
	
	chat_name = '<?php echo $logged_username; ?>';
	<?php else : ?>
	
	chat_name = currentName.value;
	<?php endif; ?>
	
	if (currentName.value != chat_name) {
		currentName.value = chat_name;
	}
	if (chat_name != sacCookie) {
		document.cookie = "sacUserName="+ chat_name +"; expires=<?php echo gmdate("D, d M Y H:i:s",time() + $offset)." UTC"; ?>;"
	}
	if (sacCookie && currentName.value == '') {
		currentName.value = sacCookie;
		return;
	}
	if (currentName.value == '') {
		currentName.value = 'guest_' + Math.floor(Math.random() * 10000);
	}
}

// check url
function checkUrl(){
	sacCookie = sac_getCookie("sacUrl");
	currentName = document.getElementById('sac_url');
	if (currentName.value == '') {
		return;
	}
	if (currentName.value != sacCookie) {
		document.cookie = "sacUrl="+currentName.value+"; expires=<?php echo gmdate("D, d M Y H:i:s",time() + $offset)." UTC"; ?>;"
		return;
	}
	if (sacCookie && (currentName.value == '' || currentName.value == "http://")) {
		currentName.value = sacCookie;
		return;
	}	
}

// ajax
function getHTTPObject(){var xmlhttp;
/*@cc_on
		@if (@_jscript_version >= 5)
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (E) {
				xmlhttp = false;
			}
		}
		@else
		xmlhttp = false;
	@end @*/
;if(!xmlhttp&&typeof XMLHttpRequest!="undefined"){try{xmlhttp=new XMLHttpRequest()}catch(e){xmlhttp=false}}return xmlhttp};
