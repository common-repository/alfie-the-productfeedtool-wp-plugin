<?php
/*
 *  This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// General framework for Alfie to parse feed


function sorteleminarray($a,$subkey) {
	foreach($a as $k=>$v) {
		@$b[$k] = strtolower($v[$subkey]);
	}
	asort($b);
	foreach($b as $key=>$val) {
		$c[] = $a[$key];
	}
	return $c;
}



function checkMatchingProduct($match_sequence,$id)
{
	global $wpdb;
	$query = mysql_query("SELECT COUNT(*) FROM ".$wpdb->prefix."alfie_producten WHERE match_sequence = '".intval($match_sequence)."'
						AND col_id ='".intval($id)."' GROUP BY match_sequence") or die (mysql_error());
	
	return mysql_result($query,0);
}

function getCheapestDetails($match_sequence)
{
	global $wpdb;
	$arrValues= array();
	$i=0;
	$query = mysql_query("SELECT * FROM ".$wpdb->prefix."alfie_producten WHERE match_sequence = '".intval($match_sequence)."'") or die (mysql_error());
	while($record = mysql_fetch_array($query))
	{
		
		$arrValues[$i]["prijs"] = $record["prijs"];
		$arrValues[$i]["producturl"] = $record["producturl"];
		$arrValues[$i]["producturl"] = $record["producturl"];
		$arrValues[$i]["adv_name"] = $record["adv_name"];
		$arrValues[$i]["adv_url"] = $record["adv_url"];
		$arrValues[$i]["adv_img"] = $record["adv_img"];
		$i++;
		
	}
	return $arrValues;
}

function readallcsvrows($handle)
{
 $fp = $handle;
  while (!feof($fp)) {
    $line[] = fgetcsv($fp, 1024,";");
   
  }
  return $line;
  fclose($fp);
}

function santinizeInput($value)
{
	$value = strip_tags(mysql_escape_string($value));
	return $value;
}


function updateSingleFeed($colindex,$source_url)
{

$teller = 0;
$arrColindex = explode(",",$colindex);
$sqlpos = array();
for($i=0; $i<count($arrColindex); $i++)
{
	$explodeAr = explode("=>",$arrColindex[$i]);
	$sqlpos[$explodeAr[0]] = $explodeAr[1];
	
}

# @ Generated URL from Alfie to get CSV file
$file_url = $source_url;

# Read bin files only
$openFile = fopen($file_url, "rb");

#Read all the csv rows
$data = readallcsvrows($openFile);
#store values in array
$arrSaveValues = array();
for($i=1; $i<count($data); $i++)
{
foreach($sqlpos as $keys=>$values)
{
	if(isset($data[$i][$keys]))
	{
	if($values==1)
	{
	$arrSaveValues[$i]["productnaam"] = $data[$i][$keys];
	}
	if($values==2)
	{
	$arrSaveValues[$i]["omschrijving"] = $data[$i][$keys];
	}
	if($values==3)
	{
	$arrSaveValues[$i]["prijs"] = $data[$i][$keys];
	}
	if($values==4)
	{
	$arrSaveValues[$i]["imageurl"] = $data[$i][$keys];
	}
	if($values==5)
	{
	$arrSaveValues[$i]["producturl"] = $data[$i][$keys];
	}
	}
	}

}
return $arrSaveValues;	
}






function strLength($summary,$limit)
{
	if (strlen($summary) > $limit)
      $summary = substr($summary, 0, strrpos(substr($summary, 0, $limit), ' ')) . '...';
      return $summary;
}

function getMatchingFromSequence($col_id,$match_seq)
{
	global $wpdb;
	$query = mysql_query("SELECT * FROM ".$wpdb->prefix."alfie_producten WHERE match_sequence='".intval($match_seq)."' AND col_id = '".intval($col_id)."'") or die (mysql_error());
	
}


function GetProductVote($match_seq,$col_id)
{
	global $wpdb;
	$query = mysql_query("SELECT AVG(ranking) FROM ".$wpdb->prefix."alfie_reactions WHERE matchid='".intval($match_seq)."' AND colid = '".intval($col_id)."'") or die (mysql_error());
	return round(mysql_result($query, 0));
}

function GetVotedUsers($match_seq,$col_id)
{
	global $wpdb;
	$query = mysql_query("SELECT COUNT(*) FROM ".$wpdb->prefix."alfie_reactions WHERE matchid='".intval($match_seq)."' AND colid = '".intval($col_id)."'") or die (mysql_error());
	return round(mysql_result($query, 0));
}


?>