var kcom = kcom || {};

/**
 * @constructor
 */
kcom.Comment = function(id) {
    this.id = id;
    var self = this;
    this.displayArea = new kcom.CommentDisplayArea(self);
    this.showHideControl = new kcom.ShowHideControl(self);
    this.isRead = false;
};

kcom.Comment.prototype.show = function() {
    this.showHideControl.changeState('visible');
    this.displayArea.show();
};

kcom.Comment.prototype.hide = function() {
    this.showHideControl.changeState('hidden');
    this.displayArea.hide();
};

kcom.Comment.prototype.getId = function() {
    return this.id;
};

/**
 *  A ShowHideControl provides a control that toggles the state of a Hideable, and
 *  changes its own UI in response.
 *  @constructor
**/
kcom.ShowHideControl = function(comment) {
    this.parent = comment;
};

kcom.ShowHideControl.prototype.changeState = function(state) {
    console.log('changeState running', state);
    if (state == 'hidden') {
        showonly('c_h_' + this.parent.getId()); // show for hidden posts
        hideonly('c_s_' + this.parent.getId()); // show for shown posts, hide hidden
    } else if (state == 'visible') {
        console.log("this comment's show-Hide control is being made visible: ", this.parent.id); 
        showonly('c_s_' + this.parent.getId());
        hideonly('c_h_' + this.parent.getId());
    } else console.log (this, 'Invalid parameter: ' + state);
};

/**
    CommentDisplayArea provides a place to show the user picture and comment text.
    It can be collapsed.
    @constructor
    @param {Object} comment - The comment that owns this display area.
**/
kcom.CommentDisplayArea = function (comment) {
    this.parent = comment;
    this.isHidden = false;
};

kcom.CommentDisplayArea.prototype.show = function() {
    showonly(this.getElementId());
    this.isHidden = true;
};

kcom.CommentDisplayArea.prototype.hide = function() {
    hideonly(this.getElementId());
    this.isHidden = false;
};

kcom.CommentDisplayArea.prototype.getElementId = function() {
    return 'c_' + this.parent.getId();
};

/**
 * @constructor
 */
kcom.conversation = function() {
    "use strict";
    var comments = [];
    function getComments() {
        if (!comments.length) {
            var allCommentIds = [];
            allCommentIds = document.forms.commentform.ac.value.toString().split(':');
            var markedAsReadIds = document.forms.commentform.ntc.value.toString().split(':');
            for (var i = 0; i < allCommentIds.length; i++) {
                var id = allCommentIds[i];
                var com = new kcom.Comment(id);
                if (markedAsReadIds.indexOf(id) > -1) {
                    com.isRead = true;
                }
                comments.push(com);
            }
        }
        return comments;
    }
    
    return {
        getComments: getComments
    };
};

kcom.conv = new kcom.conversation();

$( document ).ready( function() {
	
	$(".deleteCommentLink").click(function(){
		var comment = $( this ).parents().closest('.commentContainer');
        var convid = $( this ).attr("data-convid");
		var commentid = $( this ).attr("data-commentid");
		
		//delete post 
		comment.slideUp();
        var toast = new ToastWithOption("Deleting post...", 
            "Undo", 
            function() { comment.slideDown(); toast.done(null);}, 
            ToastWithOption.LENGTH_LONG);
        toast.done(function() {
            var request = 'conversations.php?id=' + convid + '&comid=' + commentid + '&action=delete';
            jQuery.ajax(request);
        });
		return false;
	});

    
	//Don't warn user when submitting a new post 
	$("#commentform").submit(function () {
		window.onbeforeunload = null;
	});
	
	$("#commentform").one('input propertychange', function() {
		document.forms.commentform.modified = true;
	});
    
});

function autoHideOldComments(callback){
    console.log('autoHideOldComments running');
    console.time('time to hide old comments');
    $('#ncb1').hide();//the "show new comments only" button
    $('#ncb2').show();//the "show all comments" button
    habtop();
    console.timeEnd('time to hide old comments');
    if (callback){ callback(); } 
    else { console.log('autoHideOldComments got no callback'); }    
}

/**
 *   @param {number} expandcollapse - 0 = hide, 1 = show
 *   called only when you click "show all comments".
 */
function commentToggle(expandcollapse) {
    var comments = kcom.conv.getComments();
    console.log('this should print, commenttoggling');
    var delaytime = 0;
    for (var i = comments.length - 1; i >= 0; i--) {
        delaytime += 1;
        if (expandcollapse == 1) {
            setTimeout(comments[i].show(), delaytime);
        } else {
            setTimeout(comments[i].hide(), delaytime);
        }
    }    
}

function habtop() {
    var comments = kcom.conv.getComments();
    var len = comments.length;
    for (var i = 0; i < len; i++) {
        if (comments[i].isRead) {
            comments[i].hide();
        }
    }
}

$(window).on( "load", function(){	//wait till all images are loaded
	
	//shrink large images and embedded videos inside commentContainers
	$(".commentContents").find("img, iframe, embed").each( function() {
		var parent = $( this ).closest( ".commentContainer" );
		var availableWidth = 
			window.innerWidth - $( this ).offset().left - 
			parseInt(parent.css("padding-right"), 10) - 24 - 6; 
				// 24 = 2 * sidepad. 6 = fudge.
				
		if ( $( this ).is("img") ) {	//add click-to-expand
			if ( $( this )[0].naturalWidth > availableWidth ) {
				$( this ).addClass("squashed");
				$( this ).click( function() {
					$( this ).toggleClass("squashed");
					$( this )[0].scrollIntoView();
				});
			} 
		} else { 
			if ( $( this ).width() > availableWidth ) {
				$( this ).width(availableWidth);
			}
		}
	});
});

window.onbeforeunload=function() {
	var form = document.forms.commentform;
	if (form) {
		if (form.modified && form.comment.value.trim()) {
			return 'This page is asking you to confirm that you want to leave - data you have entered may not be saved.';
		}
	}
};

function showonly(e){
    var element = document.getElementById(e);
    if (element) { //fails on comments whose parents were deleted
        element.style.display='block';
    }
}

function hideonly(e){
    var element = document.getElementById(e);
    if (element) {
        element.style.display='none';
    }
}

function show(e){ //use if initial state is hide
    var element = document.getElementById(e);
    if (element) {
        if (element.style.display == 'block') {
            element.style.display = 'none';
        } else {
            element.style.display='block';
        }
    }
}

function hide(e){ //use if initial state is show
    var element = document.getElementById(e);
    if (element) {
        if (element.style.display == 'none') {
            element.style.display = 'block';
        } else {
            element.style.display='none';
        }
    }
}
