$( document ).ready( function() {
	
	$(".deleteCommentLink").click(function(){
		//make "delete this comment" links  pop up a toast with an undo
		var pos = $( this ).position().top + $( this ).height() + 4;
		var popupMarginLeft = -1 * ($("#deleteConfirmation").outerWidth() / 2) + "px";
		var popupMarginTop = -1 * ($("#deleteConfirmation").outerHeight() / 2) + "px";
		$("#deleteConfirmation").css({
			position: "fixed",
			top: "50%",
			left: "50%",
			"margin-top": popupMarginTop,
			"margin-left": popupMarginLeft
		}).fadeIn(400).delay(3000).fadeOut(400);
		
		var convid = $( this ).attr("data-convid");
		var commentid = $( this ).attr("data-commentid");
		var comment = $( this ).parents().closest('.commentContainer').slideUp();
		
		//delete post
		var deleteCountdown = setTimeout( function(){ 
				url = 'conversations.php?id=' + convid + '&comid=' + commentid + '&action=delete';
				jQuery.ajax(url);			
			}, 3800 );
		$("#deleteConfirmationUndoButton").click(function(){
			window.clearTimeout(deleteCountdown);
			comment.slideDown();
			setTimeout( function(){ 
				$("#deleteConfirmation").hide(); 
				},200 );				
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


$(window).load( function(){	//wait till all images are loaded
	
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
