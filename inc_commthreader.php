<?php
	// handles threading of the comments
	
//	$cb_id[] = $commentid;
//	$cb_irt[] = $inreplyto;
//	$cb_ccc[] = $ccc;
//	$cb_cd[] = 0;

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
	echo "here's cb_id: (top levels)<BR><BR>"; //debug
	print_r($cb_id);
	echo "<BR>---<BR>";
	echo "here's the commentorder array: (top levels)<BR><BR>"; //debug
	print_r($commentorder);
	echo "<BR>---<BR>";
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
	echo "here's the commentorder object: (post explode)<BR><BR>"; //debug
	print_r($commentorder);
	echo "<BR>---<BR>";
	}

	//display
	if (implode("",$cb_id) == "") {
		echo "<p>This conversation is empty.<br>&nbsp;</p>";
	} else {
		foreach ($commentorder as $cbt) {
			echo "here's the commentorder array:<BR><BR>"; //debug
			print_r($cbt);
			echo "<BR>";	
			$cbt = explode(":", trim(str_replace("::",":",$cbt),":")); // clean up and explode threading results
			foreach ($cbt as $node) {
				$selfkey = array_search($node,$cb_id); //find array key
				$irt = $cb_irt[$selfkey]; //find parent comment id
				$indent = $cb_cd[$selfkey] * 26;
				if ($indent == 0 || $hideallexcept > 0) {$indent = "";} else {$indent = "padding-left: ".$indent."px;";}
				$commhtml = $cb_ccc[$selfkey];
				if ($cb_cd[$selfkey] > 0) $commhtml = str_replace("<XXXYYYZZZYYYXXX>", "<td><a href=\"javascript://\" onclick=\"jtp($irt);\" title=\"Jump to parent comment\"><img src=\"gfx/up.gif\" border=1 width=6 height=6 hspace=7 vspace=3></a></td>", $commhtml);
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"100%\" style=\"max-width: 850px; $indent padding-bottom: 15px\">$commhtml</table>";
			}
		}
	}
?>
