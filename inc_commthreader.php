<?php
	// Handles threading of the comments.
    
    // Assign comments their depth for nesting.
	$threaddepth = 0;
	for ($i=0; $i < $commentsretrieved; $i++) {
		$cb_commentDepth[$i] = 0;
        if ($cb_inReplyTo[$i] > 0) {
			$parentIndex = array_search($cb_inReplyTo[$i], $cb_id);
            if ($parentIndex === FALSE) {    
                //should only happen when parent was deleted but not children
                //TODO delete child comments when delete parent
                if (DEBUG) error_log("Comment #$cb_id[$i] had depth $cb_commentDepth[$i] but it still found no parent (because that was deleted). It had irt $cb_inReplyTo[$i] and text " . substr($cb_commentHTML[$i], strpos($cb_commentHTML[$i], "commentContents") + 17, 20) . "<br>Somehow cbid didn't have it: " . implode(", ", $cb_id));
            }
			$cb_commentDepth[$i] = $cb_commentDepth[$parentIndex] + 1; 
			$threaddepth = max($threaddepth, $cb_commentDepth[$i]);
		}
	}
	
	// sort
	$topLevel = 0;
	$orderedCommentIds = array();
	for ($i=0; $i < $commentsretrieved; $i++) { //find top level keys
		if ($cb_commentDepth[$i] == 0) {
			$orderedCommentIds[] = $cb_id[$i];
			$topLevel++;
		}
	} 
	for ($level=1; $level<=$threaddepth; $level++) { //for each level of comments...
		$newTopLevel = 0;
		$p = "";
		for ($t=0; $t<$topLevel; $t++) {	//we iterate over the toplevel posts
			$parentCandidate = $orderedCommentIds[$t];	
			$p .= "$parentCandidate:";
			$newTopLevel++;
			for ($i=0; $i < $commentsretrieved; $i++) {
                if ( $parentCandidate == $cb_inReplyTo[$i] && $cb_commentDepth[$i] >= $level) {
					//without the comment depth check some comments would repost once per iteration
					$p .= "$cb_id[$i]:"; 
					$newTopLevel++;
				}
			}
		}
		$topLevel = $newTopLevel;	//now the next level down is the new top level
        $orderedCommentIds = explode(":",rtrim($p,":"));
	}

	//display
	if (implode("",$cb_id) == "") {
		echo "<p>This conversation is empty.<br>&nbsp;</p>";
	} else {
		foreach ($orderedCommentIds as $ocIds) {
            $ocIds = explode(":", trim(str_replace("::",":",$ocIds),":")); // clean up and explode threading results
			foreach ($ocIds as $ocId) {
				$selfkey = array_search($ocId, $cb_id); //find array key
				$irt = $cb_inReplyTo[$selfkey]; //find parent comment id
				if ( empty($topnewid) && strlen($cb_isnew[$selfkey]) ) {
					$topnewid = $cb_id[$selfkey];
					$topnewparentCandidate = $irt;
				}
				
                $indent = $cb_commentDepth[$selfkey] * 26;
				if ($indent == 0 || $hideallexcept > 0) {
                    $indent = "";
                } else { $indent = "padding-left: ".$indent."px;"; }
                
				$commhtml = $cb_commentHTML[$selfkey];
				if ($cb_commentDepth[$selfkey] > 0) $commhtml = str_replace(
                    "<XXXYYYZZZYYYXXX>", 
                    "<a href=\"javascript://\" onclick=\"jumpToAnchor('" 
					. getCommentAnchor($irt) 
					. "');\" title=\"Jump to parent comment\"><img src=\"gfx/up.gif\" border=1 width=6 height=6 hspace=7 vspace=1></a>", $commhtml);
				echo "<div class=\"commentContainer\" style=\"$indent\" >";
				echo 	"<table border=0 cellpadding=3 cellspacing=0 style=\"table-layout:fixed; max-width: 850px;\">$commhtml</table>";
				echo "</div>";
			}
		}
	}
?>
