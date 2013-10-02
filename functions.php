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

/*
Handles URLs after href= and src=.
*/
function turnRelativeLinksAbsolute($htmlcomment) {
	$matches = array();
	$pattern = '/(href|src)="([^"]*)"/i';
	if ($num_matches = preg_match_all($pattern, $htmlcomment, $matches)) {
		for ($i = 0; $i < $num_matches; $i++) {
			if (stripos($matches[2][$i], "http") === FALSE) { //won't mess with https
				$replacefrom = $matches[2][$i];
				if (stripos($replacefrom, "//") !== FALSE) { 
					//I don't know why people make links like "//www.youtube.com" but they do
					$replaceto = "http:" . $matches[2][$i];
				} else {
					$replaceto = "http://" . $matches[2][$i];
				}
				$htmlcomment = str_replace($replacefrom, $replaceto, $htmlcomment);
			}	
		}
	}
	return $htmlcomment;
}

/*====================
explodePhrases

  Works like explode() with a " " delimiter, but phrases in (unescaped) quotation marks count as one word. 
  Doesn't return empty strings.
  Up to one character from the $operators string is allowed to precede each word. (must be regex-friendly)
======================*/
function explodePhrases( $string, $operators=NULL ) {
	$regex = "/[\s]*([$operators]?\"[^\"]*\")[\s]*/";
	$quotesplit = preg_split($regex, $string, NULL, PREG_SPLIT_NO_EMPTY |  PREG_SPLIT_DELIM_CAPTURE);
	$phraselist = array();
	foreach ($quotesplit as $value) {
		if ( preg_match($regex, $value) ) {
			$phraselist[] = $value;
		} else {
			$phraselist = array_merge($phraselist, preg_split("/\s+/",$value, NULL, PREG_SPLIT_NO_EMPTY));
		}
	}
	return $phraselist;
}

/*====================
preprocessForSqlBoolean

  Force SQL Boolean mode to find all the words (default is OR) and to treat hyphenated words as one word.
======================*/
function preprocessForSqlBoolean( $searchstring ) { //$searchstring should be unescaped
	$operators = "+-~<>";
	$searchPhrases = explodePhrases($searchstring, $operators);
	
	for ($i=0; $i<sizeof($searchPhrases); $i++) {
		//check for operators, add a + if none
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
