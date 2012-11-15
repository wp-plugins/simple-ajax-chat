<?php // Simple Ajax Chat - JavaScript for Ajax enhancement

	header("Cache-Control: must-revalidate");
	$offset = 60*60*24*60;
	$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
	header($ExpStr);
	header('Content-Type: application/x-javascript');
	include("../../../../wp-config.php");

?>
// Fade Anything Technique (FAT) by Adam Michela

var Fat = {
	make_hex : function(r,g,b){
		r = r.toString(16); if (r.length == 1) r = '0' + r;
		g = g.toString(16); if (g.length == 1) g = '0' + g;
		b = b.toString(16); if (b.length == 1) b = '0' + b;
		return "#" + r + g + b;
	},
	fade_all : function(){
		var a = document.getElementsByTagName("*");
		for (var i = 0; i < a.length; i++){
			var o = a[i];
			var r = /fade-?(\w{3,6})?/.exec(o.className);
			if (r){
				if (!r[1]) r[1] = "";
				if (o.id) Fat.fade_element(o.id,null,null,"#"+r[1]);
			}
		}
	},
	fade_element : function(id, fps, duration, from, to){
		if (!fps) fps = 30;
		if (!duration) duration = 3000;
		if (!from || from=="#") from = "#FFFF33";
		if (!to) to = this.get_bgcolor(id);
		var frames = Math.round(fps * (duration / 1000));
		var interval = duration / frames;
		var delay = interval;
		var frame = 0;
		if (from.length < 7) from += from.substr(1,3);
		if (to.length < 7) to += to.substr(1,3);
		var rf = parseInt(from.substr(1,2),16);
		var gf = parseInt(from.substr(3,2),16);
		var bf = parseInt(from.substr(5,2),16);
		var rt = parseInt(to.substr(1,2),16);
		var gt = parseInt(to.substr(3,2),16);
		var bt = parseInt(to.substr(5,2),16);
		var r,g,b,h;
		while (frame < frames){
			r = Math.floor(rf * ((frames-frame)/frames) + rt * (frame/frames));
			g = Math.floor(gf * ((frames-frame)/frames) + gt * (frame/frames));
			b = Math.floor(bf * ((frames-frame)/frames) + bt * (frame/frames));
			h = this.make_hex(r,g,b);
			setTimeout("Fat.set_bgcolor('"+id+"','"+h+"')", delay);
			frame++;
			delay = interval * frame; 
		}
		setTimeout("Fat.set_bgcolor('"+id+"','"+to+"')", delay);
	},
	set_bgcolor : function(id, c){
		var o = document.getElementById(id);
		o.style.backgroundColor = c;
	},
	get_bgcolor : function(id){
		var o = document.getElementById(id);
		while(o){
			var c;
			if (window.getComputedStyle) c = window.getComputedStyle(o,null).getPropertyValue("background-color");
			if (o.currentStyle) c = o.currentStyle.backgroundColor;
			if ((c != "" && c != "transparent") || o.tagName == "BODY") { break; }
			o = o.parentNode;
		}
		if (c == undefined || c == "" || c == "transparent") c = "#FFFFFF";
		var rgb = c.match(/rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)/);
		if (rgb) c = this.make_hex(parseInt(rgb[1]),parseInt(rgb[2]),parseInt(rgb[3]));
		return c;
	}
}

function sac_apply_filters(s) {
	return filter_smilies(make_links((s)));
}

var smilies = [
	[':\\)',      'icon_smile.gif'], 
	[':\\-\\)',   'icon_smile.gif'], 
	[':D',        'icon_biggrin.gif'], 
	[':\\-D',     'icon_biggrin.gif'], 
	[':grin:',    'icon_biggrin.gif'], 
	[':smile:',   'icon_smile.gif'],
	[':\\(',      'icon_sad.gif'], 
	[':\\-\\(',   'icon_sad.gif'], 
	[':sad:',     'icon_sad.gif'], 
	[':o',        'icon_surprised.gif'], 
	[':\\-o',     'icon_surprised.gif'], 
	['8o',        'icon_eek.gif'], 
	['8\\-o',     'icon_eek.gif'], 
	['8\\-0',     'icon_eek.gif'], 
	[':eek:',     'icon_surprised.gif'], 
	[':s',        'icon_confused.gif'],
	[':\\-s',     'icon_confused.gif'],
	[':lol:',     'icon_lol.gif'],
	[':cool:',    'icon_cool.gif'],
	['8\\)',      'icon_cool.gif'],
	['8\\-\\)',   'icon_cool.gif'],
	[':x',        'icon_mad.gif'],
	[':-x',       'icon_mad.gif'],
	[':mad:',     'icon_mad.gif'],
	[':p',        'icon_razz.gif'],
	[':\\-p',     'icon_razz.gif'],
	[':razz:',    'icon_razz.gif'],
	[':\\$',      'icon_redface.gif'],
	[':\\-\\$',   'icon_redface.gif'],
	[':\'\\(',    'icon_cry.gif'],
	[':evil:',    'icon_evil.gif'],
	[':twisted:', 'icon_twisted.gif'],
	[':cry:',     'icon_cry.gif'],
	[':roll:',    'icon_rolleyes.gif'],
	[':wink:',    'icon_wink.gif'],
	[';\\)',      'icon_wink.gif'],
	[';\\-\\)',   'icon_wink.gif'],
	[':!:',       'icon_exclaim.gif'],
	[':\\?',      'icon_question.gif'],
	[':\\-\\?',   'icon_question.gif'],
	[':idea:',    'icon_idea.gif'],
	[':arrow:',   'icon_arrow.gif'],
	[':\\|',      'icon_neutral.gif'],
	[':neutral:', 'icon_neutral.gif'],
	[':\\-\\|',   'icon_neutral.gif'],
	[':mrgreen:', 'icon_mrgreen.gif']
];

function make_links(s){
	var re = /((http|https|ftp):\/\/[^ ]*)/gi;
	text = s.replace(re,"<a href=\"$1\" target=\"_blank\" class=\"sac-chat-link\">&laquo;link&raquo;</a>");
	return text;
}

function filter_smilies(s){
	for (var i = 0; i < smilies.length; i++){
		var search = smilies[i][0];
		var replace = '<img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/smilies/' + smilies[i][1] + '" class="wp-smiley" alt="' + smilies[i][0].replace(/\\/g, '') + '" />';
		re = new RegExp(search, 'gi');
		s = s.replace(re, replace);
	}
	return s;
}

// Generic onload by Brothercake @ http://www.brothercake.com/site/resources/scripts/onload/

if(typeof window.addEventListener != 'undefined'){
	//.. gecko, safari, konqueror and standard
	window.addEventListener('load', initJavaScript, false);
} else if(typeof document.addEventListener != 'undefined'){
	//.. opera 7
	document.addEventListener('load', initJavaScript, false);
} else if(typeof window.attachEvent != 'undefined'){
	//.. win/ie
	window.attachEvent('onload', initJavaScript);
}

// XHTML live Chat by Alexander Kohlhofer

var sac_loadtimes;
var sac_org_timeout = <?php $sac_options = get_option('sac_options'); echo $sac_options['sac_update_seconds']; ?>;
var sac_timeout = sac_org_timeout;
var GetChaturl = "<?php echo plugins_url('simple-ajax-chat/simple-ajax-chat.php?sacGetChat=yes'); ?>";
var SendChaturl = "<?php echo plugins_url('simple-ajax-chat/simple-ajax-chat.php?sacSendChat=yes'); ?>";
var httpReceiveChat;
var httpSendChat;

function initJavaScript(){
	if (!document.getElementById('sac_chat')) { return; }
		document.forms['sac-form'].elements['sac_chat'].setAttribute('autocomplete','off');
		checkStatus('');
		checkName();
		checkUrl();
		sac_loadtimes = 1;
		httpReceiveChat = getHTTPObject();
		httpSendChat = getHTTPObject();
		setTimeout('receiveChatText()', sac_timeout);
		document.getElementById('sac_name').onblur = checkName;
		document.getElementById('sac_url').onblur = checkUrl;
		document.getElementById('sac_chat').onfocus = function () { checkStatus('active'); }	
		document.getElementById('sac_chat').onblur = function () { checkStatus(''); }
		document.getElementById('submitchat').onclick = sendComment;
		document.getElementById('sac-form').onsubmit = function () { return false; }
		document.getElementById('sac-output').onmouseover = function () {
		if (sac_loadtimes > 9) {
			sac_loadtimes = 1;
			receiveChatText();
		}
		sac_timeout = sac_org_timeout;
	}
}

function receiveChatText(){
	sac_lastID = parseInt(document.getElementById('sac_lastID').value) - 1;
	if (httpReceiveChat.readyState == 4 || httpReceiveChat.readyState == 0) {
		httpReceiveChat.open("GET",GetChaturl + '&sac_lastID=' + sac_lastID + '&rand='+Math.floor(Math.random() * 1000000), true);
		httpReceiveChat.onreadystatechange = handlehHttpReceiveChat; 
		httpReceiveChat.send(null);
		sac_loadtimes++;
		if (sac_loadtimes > 9) sac_timeout = sac_timeout * 5 / 4;
	}
	setTimeout('receiveChatText()', sac_timeout);
}

function handlehHttpReceiveChat(){
	if (httpReceiveChat.readyState == 4) {
		results = httpReceiveChat.responseText.split('---');
		if (results.length > 4) {
			for(i=0;i < (results.length-1);i=i+5) {
				insertNewContent(results[i+1],results[i+2],results[i+3],results[i+4], results[i]);
				document.getElementById('sac_lastID').value = parseInt(results[i]) + 1;
			}
			sac_timeout = jal_org_timeout;
			sac_loadtimes = 1;
		}
	}
}

function insertNewContent(liName,liText,lastResponse, liUrl, liId){
	response = document.getElementById("responseTime");
	response.replaceChild(document.createTextNode(lastResponse), response.firstChild);
	insertO = document.getElementById("sac-messages");
	oLi = document.createElement('li');
	oLi.setAttribute('id','comment-new'+liId);
	oSpan = document.createElement('span');
	oSpan.setAttribute('class','name');
	oName = document.createTextNode(liName);
	if (liUrl != "http://" && liUrl != '') {
		oURL = document.createElement('a');
		oURL.href = liUrl;
		oURL.target = "_blank";
		oURL.appendChild(oName);
	} else {
		oURL = oName;
	}
	oSpan.appendChild(oURL);
	oSpan.appendChild(document.createTextNode(' : '));
	oLi.appendChild(oSpan);
	oLi.innerHTML += sac_apply_filters(liText);
	insertO.insertBefore(oLi, insertO.firstChild);
	Fat.fade_element("comment-new"+liId, 30, <?php echo $sac_options['sac_fade_length']; ?>, "<?php echo $sac_options['sac_fade_from']; ?>", "<?php echo $sac_options['sac_fade_to']; ?>");
}

function sendComment() {
	currentChatText = document.forms['sac-form'].elements['sac_chat'].value;
	if (httpSendChat.readyState == 4 || httpSendChat.readyState == 0) {
		if (currentChatText == '') return;
		currentName = document.getElementById('sac_name').value;
		currentUrl = document.getElementById('sac_url').value;
		param = 'n='+ encodeURIComponent(currentName)+'&c='+ encodeURIComponent(currentChatText) +'&u='+ encodeURIComponent(currentUrl);	
		httpSendChat.open("POST", SendChaturl, true);
		httpSendChat.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		httpSendChat.onreadystatechange = receiveChatText;
		httpSendChat.send(param);
		document.forms['sac-form'].elements['sac_chat'].value = '';
	}
}

// http://www.codingforums.com/showthread.php?t=63818
function pressedEnter(field,event){
	var theCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (theCode == 13) {
		sendComment();
		return false;
	} 
	else return true;
}

function checkStatus(focusState) {
	currentChatText = document.forms['sac-form'].elements['sac_chat'];
	oSubmit = document.forms['sac-form'].elements['submit'];
	if (currentChatText.value != '' || focusState == 'active') {
		oSubmit.disabled = false;
	} else {
		oSubmit.disabled = true;
	}
}

function sac_getCookie(name) {
	var dc = document.cookie;
	var prefix = name + "=";
	var begin = dc.indexOf("; " + prefix);
	if (begin == -1) {
		begin = dc.indexOf(prefix);
		if (begin != 0) return null;
	} else {
		begin += 2;
		var end = document.cookie.indexOf(";", begin);
		if (end == -1)
		end = dc.length;
		return unescape(dc.substring(begin + prefix.length, end));
	}
}

function checkName(){
	sacCookie = sac_getCookie("sacUserName");
	currentName = document.getElementById('sac_name');
	if (currentName.value != sacCookie) {
		document.cookie = "sacUserName="+currentName.value+"; expires=<?php echo gmdate("D, d M Y H:i:s",time() + $offset)." UTC"; ?>;"
	}
	if (sacCookie && currentName.value == '') {
		currentName.value = sacCookie;
		return;
	}
	if (currentName.value == '') {
		currentName.value = 'guest_'+ Math.floor(Math.random() * 10000);
	}
}

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

function getHTTPObject() {
	var xmlhttp;
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
	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
		try {
			xmlhttp = new XMLHttpRequest();
		} catch (e) {
			xmlhttp = false;
		}
	}
	return xmlhttp;
}