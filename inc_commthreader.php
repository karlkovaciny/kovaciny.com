<?php
	// handles threading of the comments

	// find commentlevel
	$threaddepth = 0;
	for ($i=0; $i<$cb; $i++) {// loop through all
		$infocus = $cb_irt[$i];
		if ($infocus == 0) {
			$cb_cd[$i] = 0; // no parents? you're top-level
		} else {
			$parent = array_search($infocus,$cb_id); //find parent
			$cb_cd[$i] = $cb_cd[$parent]+1; //add one to level to find current level
			if ($threaddepth < $cb_cd[$i]) $threaddepth = $cb_cd[$i]; //figure out maximum thread depth
		}
	}
	
	// sort
	$tl = 0;
	for ($i=0; $i<$cb; $i++) {if ($cb_cd[$i] == 0) {$commentorder[] = $cb_id[$i];$tl++;}} //find top level keys
	for ($l=1; $l<=$threaddepth; $l++) {
		$ntl = 0;
		$p = "";
		for ($t=0; $t<=$tl; $t++) {
			$parentid = $commentorder[$t];
			$p .= "$parentid:";
			$ntl++;
			for ($i=0; $i<$cb; $i++) {
				if ($cb_irt[$i] == $parentid && $cb_cd[$i] >= $l) {$p .= "$cb_id[$i]:"; $ntl++;}
			}
		}
		$tl = $ntl;
		$commentorder = explode(":",rtrim($p,":"));
	}

	//display
	if (implode("",$cb_id) == "") {
		echo "<p>This conversation is empty.<br>&nbsp;</p>";
	} else {
		foreach ($commentorder as $cbt) {
			$cbt = explode(":", trim(str_replace("::",":",$cbt),":")); // clean up and explode threading results
			foreach ($cbt as $node) {
				$selfkey = array_search($node,$cb_id); //find array key
				$irt = $cb_irt[$selfkey]; //find parent comment id
				if ( ($topnew == "") && strlen($cb_isnew[$selfkey]) ) {
					$topnew = "comment_" . $cb_id[$selfkey];
				}
				$indent = $cb_cd[$selfkey] * 26;
				if ($indent == 0 || $hideallexcept > 0) {$indent = "";} else {$indent = "padding-left: ".$indent."px;";}
				$commhtml = $cb_ccc[$selfkey];
				if ($cb_cd[$selfkey] > 0) $commhtml = str_replace("<XXXYYYZZZYYYXXX>", "<td><a href=\"javascript://\" onclick=\"jtp($irt);\" title=\"Jump to parent comment\"><img src=\"gfx/up.gif\" border=1 width=6 height=6 hspace=7 vspace=3></a></td>", $commhtml);
				echo "<div class=\"commentContainer\" style=\"$indent\" >";
				echo 	"<table border=0 cellpadding=3 cellspacing=0 style=\"table-layout:fixed; max-width: 850px;\">$commhtml</table>";
				echo "</div>";
			}
		}
	}
?>
