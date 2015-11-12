var kcom = kcom || {};

/**
 * @constructor
 * @param {Object} opts:
 *      id (int, required)
 *      TODO: data (can be omitted and loaded later)
 */
kcom.Comment = function(opts) {
    this.id = opts.id;
    this.data = opts.data || "<div>placeholder</div>";
    var _this = this;    
    this.displayArea = new kcom.CommentDisplayArea(_this);
    this.showHideControl = new kcom.ShowHideControl(_this);
    this.isRead = false;
};

kcom.Comment.prototype.show = function() {
    if (this.data === null) {
        this.load();
    }
    this.showHideControl.changeState('visible');
    return this.displayArea.show();
};

kcom.Comment.prototype.hide = function() {
    this.showHideControl.changeState('hidden');
    this.displayArea.hide();
};

kcom.Comment.prototype.load = function() {
    //TODO this.data = $.get('phpscript that returns this comment\'s data')
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
    var _this = this;
    var control = this.getControl();
    $(control).one('click', clik);
    function clik() {
        _this.parent.show(); 
        $(control).one('click', unclik);
        return false;
    }
    function unclik() {
       _this.parent.hide(); 
       $(control).one('click', clik);
       return false;
   }
};

kcom.ShowHideControl.prototype.changeState = function(state) {
    if (state == 'hidden') {
        $(this.getTarget()).children().hide(); //FIXME should this move to the comment? if so how will it not hide the control too?
        $(this.getControl()).text("Show comment").show();
    } else if (state == 'visible') {
        $(this.getTarget()).children().show();
        $(this.getControl()).text("Hide comment").show();
    } else console.log (this, 'Invalid parameter: ' + state);
};

kcom.ShowHideControl.prototype.getTarget = function() {
  return document.getElementById('showHideTarget_' + this.parent.getId());
};

kcom.ShowHideControl.prototype.getControl = function() {
  return document.getElementById('showHideControl_' + this.parent.getId());
};

/**
    CommentDisplayArea provides a place to show the user picture and comment text.
    It can be collapsed.
    @constructor
    @param {Object} comment - The comment that owns this display area.
**/
kcom.CommentDisplayArea = function (comment) {
    this.parent = comment;
    this.isHidden = true;
};
/* returns a promise? */
kcom.CommentDisplayArea.prototype.shrinkEmbeds = function($commentContainer) {
    //shrink large images and embedded videos inside commentContainers
    var loaded_embeds_count = 0;
    var $embeds = $commentContainer.find("img, iframe, embed");
    console.time("waiting for embeds to load");
    var dafferd = jQuery.Deferred();
    if ($embeds.length) {
        $embeds.one('load', function() {
            var parent = $commentContainer;
            var availableWidth = 
                window.innerWidth - $( this ).offset().left - 
                parseInt(parent.css("padding-right"), 10) - 24 - 6; 
                    // 24 = 2 * sidepad. 6 = fudge.
                    
            if ( $( this ).is("img") && !$( this ).hasClass("squashed")) {	//add click-to-expand
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
            loaded_embeds_count++;
            if (loaded_embeds_count == $embeds.length) {
                console.timeEnd("waiting for embeds to load");
                dafferd.resolve('finally');
            }
        });
    } else { console.log('no embeds, no promise'); dafferd.resolve(); }
    
    return dafferd.promise();
    
};

/** returns promise */        
kcom.CommentDisplayArea.prototype.show = function() {
    var deferd = jQuery.Deferred();
    var el = this.getElement();
    if (el) { //TODO: stop calling on comments whose parents were deleted
        var $contents = $(el).find(".commentContents");
        if (!$contents[0].innerHTML) {
            $contents.html($contents[0].getAttribute("data-comment-html"));
        }
        $.when(this.shrinkEmbeds($contents)).then(function() {
            console.log('resolving deferd because shrink done running');
            deferd.resolve('ok');
        });
        el.style.display='block';         
    } else {
        deferd.reject("false");
    }
    this.isHidden = true;
    console.time("how long it takes for comment.show deferd to return");
    console.timeEnd("how long it takes for comment.show deferd to return");
    console.log("returning promise", deferd.promise());
    debugger;
    return deferd.promise();
};

kcom.CommentDisplayArea.prototype.hide = function() {
    var el = this.getElement();
    if (el) {
        el.style.display = 'none';
    }
    this.isHidden = false;
};

kcom.CommentDisplayArea.prototype.getElement = function() {
    return document.getElementById('c_' + this.parent.getId());
};

kcom.CommentDisplayArea.prototype.getElementId = function() {
    return 'c_' + this.parent.getId();
};

/**
 * @constructor
 * @param {string} show - 'all' to show all comments, 'new' to show *   only new comments
 */
kcom.Conversation = function(show) {
    "use strict";
    var _this = this;
    this.comments = [];
    getComments();
    if (show === 'new') { showAll(); } //TODO this.showAll
    else if (show === 'all') {hideNew();}
    
    function showAll() {
        for (var i = 0; i < _this.comments.length; i++) {
//            if (this.comments[i].
        }
    }
    function hideNew() {
    
    }
    function getComments() {
        if (!_this.comments.length) {
            var allCommentIds = [];
            allCommentIds = document.forms.commentform.ac.value.toString().split(':');
            var markedAsReadIds = document.forms.commentform.ntc.value.toString().split(':');
            for (var i = 0; i < allCommentIds.length; i++) {
                var id = allCommentIds[i];
                var com = new kcom.Comment({"id": id});
                if (markedAsReadIds.indexOf(id) > -1) {
                    com.isRead = true;
                }
                _this.comments.push(com);
            }
        }
        return _this.comments;
    }
    
    return {
        getComments: getComments
    };
};

kcom.Conversation.prototype.comments = [];

