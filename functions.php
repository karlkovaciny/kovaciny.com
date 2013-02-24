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
	$OR_locations = array_keys($searchPhrases, "OR");
	echo "OR_Locations is" . var_dump($OR_locations) . "<br>";
	
	for ($i=0; $i<sizeof($searchPhrases); $i++) {
		//force a match on all words by adding a + operator, unless the user added their own operator or OR keyword
		$first = substr($searchPhrases[$i],0,1);
		if ( strpbrk($first, $operators) ) {
			$op = $first;
			$searchPhrases[$i] = substr($searchPhrases[$i],1);
		} else {
			//scheck if OR was used. If not, add a +.
			$sfsfs = array($i-1,$i,$i+1);
			foreach ($OR_locations as $value) {
				if ( in_array($value, $sfsfs) ) {
					$or_present=TRUE;					
				}
			}
			if ($or_present) $op = ""; else $op = "+";
		}
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
