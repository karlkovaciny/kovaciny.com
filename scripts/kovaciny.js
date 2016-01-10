/** @suppress {duplicate} */ var kcom = kcom || {};
kcom.HOST_NAME = kcom.HOST_NAME || "";

$( document ).ready( 
    bindSubmits
);

$(window).on("load", function() {
    if ($('body.has-conversation').length > 0) {
        console.log('kovacinyjs says body has conversation');
    } else console.log('kovacinyjs says body doesn\'t have conversation');
});

/** @suppress {deprecated} the Ajax version of .load is not deprecated */ 
function submitMarkAsRead(formdata) {
    var $oldBody = $("#bodyContent").clone();
    var toastMessage = formdata.convIds.length > 1 ? "Conversations marked as read" : "Conversation marked as read";
    var toast = new kcom.ToastWithOption(toastMessage, "Undo", 
        function() {
            var tempFormdata = formdata;
            tempFormdata.readDates = formdata.oldReadDates;
            var jqxhr = jQuery.post(kcom.HOST_NAME + "/api/conversations.php", tempFormdata);
            jqxhr.done(function() {
                $("#bodyContent").html(String($oldBody.html()));
                bindSubmits();
            });
        }, 
        kcom.ToastWithOption.LENGTH_LONG);

    var jqxhr = jQuery.post(kcom.HOST_NAME + "/api/conversations.php", formdata);
    jqxhr.fail(function( request, status, error) {
        console.log('request failed: ', request.status);
        toast.cancel();
        $(".markAsReadSubmit").prop("disabled", false);
        $(".markAsReadSubmit").val("Request failed (" + request.status + ") Try again?");
    });
    jqxhr.done(function() {
        $("#bodyContent").load("index.php #bodyContent > *", function() {
            window.scrollTo(0,0);
            window.history.pushState("", "", kcom.HOST_NAME + "/index.php");
            bindSubmits();
        });
    });
}

/** @suppress {missingProperties} To access forms with dot notation. */ 
function bindSubmits() {
    $(".markAsReadSubmit").prop("disabled", false); //TODO put this in complete()
    //picking conversations to mark as read in index.php
    $("form[name=markasread]").submit(function(e) {
        e.preventDefault();
        var form = $( this.closest("form") )[0];
        var formdata = {username: form.username.value};
        var convIds = [];
        var readDates = [];
        var oldReadDates = [];

        $("input:checkbox[name='convIds[]']:checked").each( function() {
            convIds.push($( this ).val());
            readDates.push(form.readdate.value);
            oldReadDates.push($( this ).closest("tr").find("input[name='dateRead[]']").val());
            console.log ('id, oldReadDate: ', convIds[convIds.length - 1],  oldReadDates[oldReadDates.length - 1]);
        });
        
        if (convIds.length) {
            formdata.markasread = form.markasread.value;
            formdata.convIds = convIds;
            formdata.readDates = readDates;
            formdata.oldReadDates = oldReadDates;
            submitMarkAsRead(formdata);
        } else {
            $("#markasreadsubmit").css({opacity: 0});
            $("#markasreadsubmit").animate({opacity: 1}, 80);
        }
    });
    
    //user clicked "Mark as read" at the end of a conversation    
    $("form[name=markread]").submit(function(e) {
        e.preventDefault();        
        $(".markAsReadSubmit").prop("disabled", true);
        var form = $( this )[0];
        var formdata = {
            username: form.username.value,
            markasread: form.markasread.value,
            convIds: [form.convIds.value],
            readDates: [form.readdate.value],
            oldReadDates: new Array(form.conchangedate.value)
            };
        submitMarkAsRead(formdata);
    });
}

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

/**
 * @param {string} anchorname -- href value of target location
 * @param {Promise} promise -- promise that needs to be resolved before jumping
*/
function jumpToAnchorWhen(anchorname, promise) {
    jQuery.when(promise).then(function() {jumpToAnchor(anchorname);}); //TODO make this a .call 
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

/** 
 * @constructor
 * @param text
 * @param optionText - label for the option buttion
 * @param optionCallback - what to do if user selects the option
 * @param duration - in ms, default = permanent 
*/
kcom.ToastWithOption = function(text, optionText, optionCallback, duration) {
    "use strict";
    var self = this;
    
    // function to call if the toast expires or is destroyed (but not when the option is clicked). Will run if they click a link but not if they close the window.
    this.done = function (callback)   {
        self.doAfter = callback;
        window.addEventListener('unload', self.doAfter, false);
        return this;
    };
    
    this.$toast = $( "<div></div>" )
        .addClass("toast")
        .css({display: "inline-block"})
        .append('<div class="popupMessage">' + text + '</div>')
        .append('<div class="toastOptionButton popupMessage"><img src="gfx/Arrows-Undo-icon.png" id="undoArrow">' + optionText + '</div>')
        .appendTo('body');
    
    var popupMarginLeft = -1 * (this.$toast.outerWidth() / 2);
    var popupMarginTop = -1 * (this.$toast.outerHeight() / 2);
    this.$toast.css({
        position: "fixed",
        top: "80%",
        left: "50%",
        "margin-top": popupMarginTop + "px",
        "margin-left": popupMarginLeft + "px"
    }).fadeIn(400);
    if (duration) {
        this.$toast.delay(duration - 800).fadeOut(400);
        setTimeout(function() {
            window.removeEventListener('unload', self.doAfter, false);
            if (self.doAfter) { self.doAfter();} 
        }, duration - 400);
    } else {
        console.log('no duration set, toast will stay open forever');
    }
    
    var $optionButton = $(".toastOptionButton");
    if ($optionButton.length) {
        $(document).one("click", ".toastOptionButton", function(){
            console.log("setting up to tear down toast");
            if (typeof optionCallback === "function") optionCallback();
            window.removeEventListener('unload', self.doAfter, false);
            setTimeout( function(){ 
                    console.log("finished timeout; hiding toast");
                    this.$toast.hide(); 
                },200 );				
        });
    } else console.log('option button did not exist');
};
kcom.ToastWithOption.LENGTH_LONG = 3500;
kcom.ToastWithOption.LENGTH_SHORT = 2000;

/** Destroys the toast without calling any callbacks. */
kcom.ToastWithOption.prototype.cancel = function() {
    $(this.$toast).hide();
};
