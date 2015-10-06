function jtp(jumpto) {//jump to parent in threaded conversations
    setTimeout("document.location.href='#comment_" + jumpto + "';window.scrollBy(0,-30);", 10);
    setTimeout("showonly('c_" + jumpto + "');showonly('c_s_" + jumpto + "');showonly('c_h_" + jumpto + "');hideonly('c_h_" + jumpto + "');", 20);
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
    if (txt.length === 0) {
        txt = document.getElementById(commentTextId).innerHTML;
        txt = txt.replace(/<br>\n<br>/gi, "\n");
    }
    
    var existingtext = document.forms.commentform.comment.value + '';
    if (existingtext.length === 0) {
        modtxt = '[quote=' + authorname + ']' + txt + '[/quote]' + '\n\n';
    } else {
        modtxt = existingtext + '\n[quote=' + authorname + ']' + txt + '[/quote]' + '\n\n';
    }
    if (modtxt.length === 0) modtxt = existingtext;
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
            } catch (ex) {}
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

function jumpToAnchor(anchorname) {
    var baseUrl = window.location.href.split('#')[0];
    var newUrl = baseUrl + '#' + anchorname;
    window.location.replace( newUrl );
}

/** 
 * @param {Array} tokens - array of tokens to highlight 
 * Highlights tokens inside the node _this_ and all of its children
*/
function highlightInnerHTML(tokens) {
    console.count('highlightinner called');
    var element = this;
    if (!element) {
        return "";
    }
    var childNodes = element.hasChildNodes() ? element.childNodes : [];
    tokens = Array.prototype.slice.apply(arguments);
    var pattern = new RegExp(tokens.join("|"), "gi");
    var unhighlightedNodes = [];
    
    for (var i = 0; i < childNodes.length; i++) {
        var child = childNodes[i];
        if (child.nodeType === 3 && child.length > 0) {    //text, not empty
            var highlightSpan = document.createElement("span");
            highlightSpan.className = "hilite_strong";
            wrapMatchesInTag(child, pattern, highlightSpan);
        } else if (unhighlightedNodes.indexOf(child) == -1) {
                unhighlightedNodes.push(child);
        }
    }

    for (var j = 0; j < unhighlightedNodes.length; j++) {
        highlightInnerHTML.apply(unhighlightedNodes[j], tokens);
    }
}

//Searches a textNode for a pattern match (RegExps allowed), wraps all instances in clones of a wrapperNode, and replaces the original node.
function wrapMatchesInTag(textNode, pattern, wrapperNode)  {
    if (!textNode) {
        console.log('wrapMatchesInTag was called with a blank textNode. Returning.');
        return;
    }
    
    var matches = textNode.nodeValue.match(pattern);
    if (!matches) return;
    
    var exploded = textNode.nodeValue.split(pattern);
    
    var replacementNodes = [];
    for (var j = 0; j < exploded.length; j++) {
        replacementNodes.push(document.createTextNode(exploded[j]));
        if (j < matches.length) {
            var wrapped = wrapperNode.cloneNode(false);
            wrapped.innerHTML = matches[j];
            replacementNodes.push(wrapped);
        }
    }

    var parent = textNode.parentNode;
    if (!parent) {
        console.log("wrapMatchesInTag: textNode parameter did not have a parent for some reason. Aborting highlight.");
        return textNode;
    }
    
    //I chose to replace the text node without creating a new parent for the new nodes
    var lastReplacement = replacementNodes.slice(-1)[0];
    parent.replaceChild(lastReplacement, textNode);
    for (var k = replacementNodes.length - 2; k >= 0; k--) {
        parent.insertBefore(replacementNodes[k], replacementNodes[k+1]);
    }
    parent.normalize();
}
