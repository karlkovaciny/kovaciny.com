/** @suppress {duplicate} */ var kcom = kcom || {};

$( document ).ready( function() {
    kcom.conv = new kcom.Conversation('show');
    
    $("#newCommentsToggle").click(function() {
        
    });
    
    $(".deleteCommentLink").click(function(){
		var comment = $( this ).parents().closest('.commentContainer');
        var convid = $( this ).attr("data-convid");
		var commentid = $( this ).attr("data-commentid");
		
		//delete post 
		comment.slideUp();
        var toast = new kcom.ToastWithOption("Deleting post...", 
            "Undo", 
            function() { comment.slideDown(); toast.done(null);}, 
            kcom.ToastWithOption.LENGTH_LONG);
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
    console.time('time to hide old comments');
    $('#ncb1').hide();//the "show new comments only" button
    $('#ncb2').show();//the "show all comments" button
    if (callback) {
        jQuery.when( habtop() ). then( 
            function() {
                console.log('defered succeded');
                console.log('called autoHOC callback');
                callback();                 
            }, 
            function() {console.log('defered faild');}
        );
        console.log('there was a autoHOC callback');
    } 
    else { console.log('autoHideOldComments got no callback'); }    
    console.timeEnd('time to hide old comments');
}

/**
 * returns Promise
 */
function habtop() {
    var comments = kcom.conv.getComments();
    var len = comments.length;
    var promises = [];
    for (var i = 0; i < len; i++) {
        if (comments[i].isRead) {
            comments[i].hide();
        } else {
            var promise = comments[i].show();
            promises.push(promise);
        }
    }
    var dfd = jQuery.Deferred();
    //when I stop here, it works.
    var allPromises = $.when.apply($, promises);
    console.time("waiting for promises");
    return jQuery.when(allPromises).then(
        function() {
            console.log("all promises success!");
            dfd.resolve( "hurray" );
            console.timeEnd("waiting for promises"); //2.42 ms
            console.log('promise in whenthen in habtop', dfd.promise());
            return dfd.promise();
        }, 
        function() {
            console.log("some promise failed");
            dfd.reject( "boo" );
            return dfd.promise(); //TODO needs handler
        }
    );
}

/**
 *   @param {number} expandcollapse - 0 = hide, 1 = show
 *   called only when you click "show all comments".
 */
function commentToggle(expandcollapse) {
    var comments = kcom.conv.getComments();
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

$(window).on( "load", function(){	//wait till all images are loaded

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
