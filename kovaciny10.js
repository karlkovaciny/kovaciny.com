function show(e){ //use if initial state is hide
	element = document.getElementById(e);
	if (element) {
		if (element.style.display == 'block') {
			element.style.display = 'none'
		} else {
			element.style.display='block';
		}
	}
}

function hide(e){ //use if initial state is show
	element = document.getElementById(e);
	if (element) {
		if (element.style.display == 'none') {
			element.style.display = 'block'
		} else {
			element.style.display='none';
		}
	}
	
}

function showonly(e){
	element = document.getElementById(e);
	if (element) //fails on comments whose parents were deleted
		{element.style.display='block';} 
}
function hideonly(e){
	element = document.getElementById(e);
	if (element) 
		{element.style.display='none';} 
}

function commenttoggle(expandcollapse) {// 0 = hide, 1 = show
//this function is called not when you call habtop, nor when you hide/show an individual comment, but yes when you click the hide/show button.
//in fact it's never called with 0.
//the difference between this and habtop is just that ac.value vs ntc.value, which are hidden items in line 223 of the commentform
//ac is "all comments" (list of comment ids), ntc is list of marked-as-read comments.
	var ac = document.forms.commentform.ac.value + '';
	var aca = new Array();
	var delaytime = 0;
	aca = ac.split(':');
	for (i = aca.length - 1; i >= 0; i--) {
		delaytime += 1;
		if (expandcollapse == 1) {
			//this is a dynamic function call: "showonly('c_4'); showonly('c_s_4'); hideonly('c_h_4');", delaytime
			//what it does is with a tiny delay, runs the show/hide function on each comment in turn.
			setTimeout("showonly('c_" + aca[i] + "'); showonly('c_s_" + aca[i] + "'); hideonly('c_h_" + aca[i] + "');", delaytime);
		} else {
			setTimeout("hideonly('c_" + aca[i] + "'); hideonly('c_s_" + aca[i] + "'); showonly('c_h_" + aca[i] + "');", delaytime);
		}
	}	
}

function habtop() {
	var ntc = document.forms.commentform.ntc.value + '';
	var aca = new Array();
	var delaytime = 0;
	aca = ntc.split(':');
	for (i = aca.length-1; i >= 0; i--) {
		delaytime += 1;
		setTimeout("hideonly('c_" + aca[i] + "'); hideonly('c_s_" + aca[i] + "'); showonly('c_h_" + aca[i] + "');", delaytime);
	}

}

function jtp(jumpto) {//jump to parent in threaded conversations
	setTimeout("document.location.href='#comment_" + jumpto + "'; window.scrollBy(0,-30);", 10);
	setTimeout("showonly('c_" + jumpto + "'); showonly('c_s_" + jumpto + "'); showonly('c_h_" + jumpto + "'); hideonly('c_h_" + jumpto + "');", 20);
	setTimeout("flashcomm('cc_" + jumpto + "', '#FFFF99');", 900);
	setTimeout("flashcomm('cc_" + jumpto + "', '#DDDDDD');", 1100);
	setTimeout("flashcomm('cc_" + jumpto + "', '#FFFF99');", 1600);
	setTimeout("flashcomm('cc_" + jumpto + "', '#DDDDDD');", 1700);
}
function flashcomm(e,bgc){document.getElementById(e).style.background=bgc;}

function quoteme(authorname, commentTextId) {
	var txt = '';
	var modtxt = '';
	if (window.getSelection) {
		txt = window.getSelection().toString();
	} else if (document.getSelection) {
		txt = document.getSelection().toString();
	} else if (document.selection) {
		txt = document.selection.createRange().text;
	}
	if (txt.length == 0) {
		txt = document.getElementById(commentTextId).innerHTML;
		txt = txt.replace(/<br>\n<br>/gi, "\n");
	} 
	
	var existingtext = document.forms.commentform.comment.value + '';
	if (existingtext.length == 0) {
		modtxt = '[quote=' + authorname + ']' + txt + '[/quote]' + '\n\n';
	} else {
		modtxt = existingtext + '\n[quote=' + authorname + ']' + txt + '[/quote]' + '\n\n';
	}
	if (modtxt.length == 0) modtxt = existingtext;
	document.forms.commentform.comment.value = modtxt;
	//tinyMCE.updateContent("comment");
	var commentForm = document.forms.commentform.comment;
	setTimeout(function() {
	  commentForm.focus();
	}, 0);
}

var http_request = false;

function makeRequest(url) {
	http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
			http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}

	if (!http_request) {
		alert('Giving up :( Cannot create an XMLHTTP instance');
		return false;
	}
	http_request.onreadystatechange = alertContents;
	http_request.open('GET', url, true);
	http_request.send(null);
}

function alertContents() {
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			var xmldoc = http_request.responseXML;
			var root_node = xmldoc.getElementsByTagName('root').item(0);
			alert(root_node.firstChild.data);
		} else {
			alert('There was a problem with the request.');
		}
	}

}

function autoHideOldComments(){
	hide('ncb1');show('ncb2');habtop();
}
	
function jumpToAnchor(anchor) {
	var baseUrl = window.location.href.split('#')[0];
	var newUrl = baseUrl + '#' + anchor;
	window.location.replace( newUrl );
}

/* Adds a highlighted span around all instances of a string in a node and its children */ 
function highlightInnerHTML(element, targetString){
	var pattern = new RegExp(targetString, "gi");
	if ( element !== null ) {
		var numChildren = element.childNodes.length;
		for (var i = 0; i < numChildren; i++) {
			var child = element.childNodes[i];
			if (child.nodeType === 3) {
				var replacementNode = document.createElement('span');
				var exploded = child.nodeValue.split(pattern);
				for (var j = 0, len = exploded.length; j < len; j++) {
					replacementNode.appendChild(document.createTextNode(exploded[j]));
					if ( j < (len - 1) ) { //there was actually an instance of the pattern found, replace it back in
						var highlighted = document.createElement("span");
						highlighted.className = "hilite_strong";
						var match = child.nodeValue.match(pattern);
						highlighted.innerHTML = match[j];
						replacementNode.appendChild(highlighted);
					}
				}
				child.parentNode.replaceChild(replacementNode, child);
			} else {
				highlightInnerHTML(child, targetString);
			}
		}
	}
}
