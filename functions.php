<?php
function format_interval($timestamp, $granularity = 2) {
	$units = array('1 year|years' => 31536000, '1 week|weeks' => 604800, '1 day|days' => 86400, '1 hr|hrs' => 3600, '1 min|min' => 60, '1 sec|sec' => 1);
	$output = '';
	foreach ($units as $key => $value) {
		$key = explode('|', $key);
		if ($timestamp >= $value) {
			$output .= ($output ? ' ' : '') . format_plural(floor($timestamp / $value), $key[0], $key[1]);
			$timestamp %= $value;
			$granularity--;
		}
		if ($granularity == 0) {break;}
	}
	return $output ? $output : "0 sec";
}

function format_plural($count, $singular, $plural) {
	if ($count == 1) {return $singular;} else {return $count . " " . $plural;}
}

function pluralize($count, $singular, $plural = false) {if (!$plural) {$plural = $singular . 's';} return ($count == 1 ? $singular : $plural);}

/*====================
explodePhrases

  Works like explode() with a " " delimiter, but phrases in (unescaped) quotation marks count as one word. 
  Returns an array of nonempty strings, or an empty array.
======================*/
function explodePhrases( $string ) {
	//Split the phrase by matching quoted strings and using them as delimiters. 
	$regex = "/[\s]*([\S]*\"[^\"]*\"[\S]*)[\s]*/"; //keep stuff outside the quotes to retain operators like + or ()
	$quotesplit = preg_split($regex, $string, NULL, PREG_SPLIT_NO_EMPTY |  PREG_SPLIT_DELIM_CAPTURE);
	
	$phraselist = array();
	foreach ($quotesplit as $value) {
		if ( preg_match($regex, $value) ) { 	//keep quoted strings intact
			$phraselist[] = $value; 
		} else {	//explode unquoted strings into words
			$phraselist = array_merge($phraselist, preg_split("/\s+/", $value, NULL, PREG_SPLIT_NO_EMPTY));
		}
	}
	return $phraselist;
}

/*====================
preprocessForSqlBoolean

  Force SQL Boolean mode to find all the words (default is OR) and to treat hyphenated words as one word.
======================*/
function preprocessForSqlBoolean( $searchstring ) { //$searchstring should be unescaped
	$searchPhrases = explodePhrases($searchstring);
	
	$operators = "+-~<>";
	for ($i=0; $i<sizeof($searchPhrases); $i++) {
		//force a match on all words by adding a + operator, unless the user added their own
		$first = substr($searchPhrases[$i],0,1);		
		if ( strpbrk($first, $operators) ) {
			$op = $first;
			$searchPhrases[$i] = substr($searchPhrases[$i],1);
		} else $op = "+";
		
		//surround hyphenated words in quotes
		if ( stripos($searchPhrases[$i],"-") && ($searchPhrases[$i][0] !== "\"") ) {
			$searchPhrases[$i] = "\"" . $searchPhrases[$i] . "\"";
		}
		$searchPhrases[$i] = $op . $searchPhrases[$i];
	}
	
	$searchPhraseString = implode(" ", $searchPhrases);
	return mysql_real_escape_string($searchPhraseString); //sanitize single quotes
}
?>
