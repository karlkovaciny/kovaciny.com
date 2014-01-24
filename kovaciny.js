function show(e){ //use if initial state is hide
	element = document.getElementById(e).style;
	element.display == 'block' ? element.display = 'none' : element.display='block';
}
function hide(e){ //use if initial state is show
	element = document.getElementById(e).style;
	element.display == 'none' ? element.display = 'block' : element.display='none';
}

function showonly(e){document.getElementById(e).style.display='block';} //will not hide if already shown
function hideonly(e){document.getElementById(e).style.display='none';} //will not hide if already shown

function commenttoggle(expandcollapse) {// 0 = hide, 1 = show
//this function is called not when you call habtop, nor when you hide/show an individual comment, but yes when you click the hide/show button.
//in fact it's never called with 0.
//the difference between this and habtop is just that ac.value vs ntc.value, which are hidden items in line 223 of the commentform
//ac is "all comments" (list of comment ids), ntc is list of marked-as-read comments.
	var ac = document.forms.commentform.ac.value + '';
	var aca = new Array();
	var delaytime = 0;
	aca = ac.split(':');
	for (i = aca.length; i >= 0; i--) {
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
	for (i = aca.length; i >= 0; i--) {
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

function quoteme(authorname) {
	var txt = '';
	var modtxt = '';
	if (window.getSelection) {
		txt = window.getSelection();
	} else if (document.getSelection) {
		txt = document.getSelection();
	} else if (document.selection) {
		txt = document.selection.createRange().text;
	}
	txt = txt + '';
	if (txt.length == 0) {
		confirm('Please select the text you wish to quote and click this link again.');
	} else {
		var existingtext = document.forms.commentform.comment.value + '';
		var txtsnip = '';
		if (txt.length > 45) {txtsnip = txt.substr(0,45) + '...';} else {txtsnip = txt}
		if (existingtext.length == 0) {
			if (confirm('Add the following quote to the comment box?\n\n' + authorname.toUpperCase() + ': ' + txtsnip)) {modtxt = '[quote=' + authorname + ']' + txt + '[/quote]' + '\n\n';}
		} else {
			if (confirm('Append the following quote to the comment box?\n\n' + authorname.toUpperCase() + ': ' + txtsnip)) {modtxt = existingtext + '\n[quote=' + authorname + ']' + txt + '[/quote]' + '\n\n';}
		}
		if (modtxt.length == 0) modtxt = existingtext;
		document.forms.commentform.comment.value = modtxt;
		//tinyMCE.updateContent("comment");
		document.forms.commentform.comment.focus();
	}
}

function quoteentire(authorname) {
	var txt = document.forms.commentform.comment.value + '';
	var qe = document.forms.commentform.qe.value + '';
	var pronoun = 'his';
	if (txt.length == 0) {
		txt = qe + '\n\n';
	} else {
		if (authorname == 'Anna' || authorname == 'Monica' || authorname == 'Rachel' || authorname == 'Rae' || authorname == 'Ruth') pronoun = 'her';
		if (confirm('There is already text in the comment box.\n\nClick OK to prefix it with ' + authorname + '\'s comment, or click Cancel to append ' + pronoun + ' comment to the existing text.')) {txt = qe + '\n\n' + txt;} else {txt = txt + '\n\n' + qe + '\n\n';}
	}
	document.forms.commentform.comment.value = txt;
	document.forms.commentform.comment.focus();
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

function highlightInnerHTML(element_id, targetString){
	if ( e_id = document.getElementById(element_id) ) {
		var newHTML = e_id.innerHTML;
		var pattern = new RegExp(targetString, "gi");
		newHTML = newHTML.replace(pattern, "<span class=\'hilite\'>$&</span>");
		e_id.innerHTML = newHTML;
	}
}
