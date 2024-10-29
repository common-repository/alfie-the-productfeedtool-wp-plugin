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

# Read bin files only
$openFile = fopen($source_url, "rb");
#dec global
global $sqlpos;
global $data;
global $matchID;
global $file_elemens;
#Read all the csv rows
$data = readallcsvrows($openFile);
// compute the positon of the advertiser with the var 
$file_elemens = count($data[0]);
# @ Generated colindex from Alfie
$arrColindex = explode(",",$colindex);
$sqlpos = array();
for($i=0; $i<count($arrColindex); $i++)
{
	$explodeAr = explode("=>",$arrColindex[$i]);
	$sqlpos[$explodeAr[0]] = $explodeAr[1];
	
}

$sqlpos[$file_elemens-3] =  $file_elemens-4;
$sqlpos[$file_elemens-2] = $file_elemens-3;
$sqlpos[$file_elemens-1] = $file_elemens-2;


// compute matchkey 
// this is always n-1 
// matchkey is at position 6 of CSV file
$matchID = $file_elemens-4;

// get the cheapest price from the array
function getCheapestPrice($data,$matchID,$matchKey,$sqlpos)
{
	// loop through the list of data
	for($i=1; $i<count($data); $i++)
	{
	// loop through the positions as given in Alfie
	foreach($sqlpos as $keys=>$values)
	{
		// get the price
		if($values==3)
		{
			// look if the matchkey has been given
			if(isset($data[$i][$matchID]))
			if($data[$i][$matchID]==$matchKey)
			{
				// save the price in an array
				$savePrices[] =  $data[$i][$keys];
// decrease whitespace with brackets				
}}}}	
// use the php function to sort the prices
sort($savePrices);
// return as array
return $savePrices;
}
// Function to get fields 
function getfields ($data,$sqlpos,$field,$matchID,$matchKey)
{
	// loop through the list of data
	for($i=1; $i<count($data); $i++)
	{
		// loop through the positions as given in Alfie
		foreach($sqlpos as $keys=>$values)
		{
			if($values==$field)
			{
				if(isset($data[$i][$matchID]))
				if($data[$i][$matchID]==$matchKey)
				{
				return $data[$i][$keys];
				}
				
			}
		}
	}
}
function getProductByMatchKey($matchKey)
{
	// fetch global		
	global $sqlpos;
	global $data;
	global $matchID;
	global $file_elemens;
	$relStores = array();
	$counter=0;
// loop through the list of data
	for($i=1; $i<count($data); $i++)
	{
		$counter++;
		// loop through the positions as given in Alfie
		foreach($sqlpos as $keys=>$values)
		{
			if(isset($data[$i][$matchID]))
		if($matchKey == $data[$i][$matchID])
		{
			if($values==1)
			{
				$relStores[$counter]["productname"] = $data[$i][$keys];
			}
			if($values==2)
			{
				$relStores[$counter]["description"] = $data[$i][$keys];
			}
			if($values==3)
			{
				$relStores[$counter]["price"] = $data[$i][$keys];
			}
			if($values==4)
			{
				$relStores[$counter]["imageurl"] = $data[$i][$keys];
			}
			if($values==5)
			{
				$relStores[$counter]["producturl"] = $data[$i][$keys];
			}
			
			if($values==$file_elemens-4)
			{
				$relStores[$counter]["adv_name"] = $data[$i][$keys];
			}
			
			if($values==$file_elemens-3)
			{
				$relStores[$counter]["adv_url"] = $data[$i][$keys];
			}
			
			if($values==$file_elemens-2)
			{
				$relStores[$counter]["adv_image"] = $data[$i][$keys];
			}
			
			
		}
		
	}
	
}
return $relStores;
}


// create a copy of the matchID
$copy_matchID = null;
// sla alles netjes op in een array
$saveElements = array();
// make an counter
$counter = 0;
// make matchkey's unique
for($i=1; $i<count($data); $i++)
{
// loop through the positions as given in Alfie
	foreach($sqlpos as $keys=>$values)
	{
		// get the productname
		if($values==1)
		{
			// we look in the next element of the array this must excist
			if(isset($data[$i+1][$matchID]))
			// compare matchnames if they are the same on show one
			if(isset($data[$i+1][$matchID]) == isset($data[$i][$matchID]))
			{
				
				if($copy_matchID == $data[$i][$matchID])
				{
				// compare the matchID's are there more then 1 matchings?
				$copy_matchID = $data[$i][$matchID];
				} else {
				$counter++;
				// put the in an array for display
				$saveElements[$counter]["productname"] =  $data[$i][$keys];
				$saveElements[$counter]["price"] = getCheapestPrice($data,$matchID,$data[$i][$matchID],$sqlpos);
				$saveElements[$counter]["description"] = getfields($data,$sqlpos,2,$matchID,$data[$i][$matchID]);
				$saveElements[$counter]["imageurl"] = getfields($data,$sqlpos,4,$matchID,$data[$i][$matchID]);
				$saveElements[$counter]["producturl"] = getfields($data,$sqlpos,5,$matchID,$data[$i][$matchID]);
				$saveElements[$counter]["adv_name"] = getfields($data,$sqlpos,$file_elemens-4,$matchID,$data[$i][$matchID]);
				$saveElements[$counter]["adv_url"] = getfields($data,$sqlpos,$file_elemens-3,$matchID,$data[$i][$matchID]);
				$saveElements[$counter]["adv_image"] = getfields($data,$sqlpos,$file_elemens-2,$matchID,$data[$i][$matchID]);
				$saveElements[$counter]["matchID"] =  $data[$i][$matchID];
				// set net matchID
				$copy_matchID = $data[$i][$matchID];
				}	
			} 
		}
	} 
}	




for($i=1; $i<count($saveElements); $i++){
	global $wpdb;

	$arrRelevant = getProductByMatchKey($saveElements[$i]["matchID"]);
	$arrRelevant = sorteleminarray($arrRelevant,'price'); 
	foreach($arrRelevant as $rel)
	{
		  $wpdb->query("INSERT INTO ".$wpdb->prefix."alfie_producten (col_id,productnaam,prijs,omschrijving,imageurl,producturl,match_sequence,adv_name,adv_img,adv_url) 
	VALUES ( '".$col_id ."',
			'".santinizeInput($rel["productname"])."',
			'".santinizeInput($rel["price"])."',
			'".santinizeInput($rel["description"])."',
			'".santinizeInput($rel["imageurl"])."',
			'".santinizeInput($rel["producturl"])."',
			'".$saveElements[$i]["matchID"]."',
			'".santinizeInput($rel["adv_name"])."',
			'".santinizeInput($rel["adv_image"])."',
			'".santinizeInput($rel["adv_url"])."'
			)") or die (mysql_error());
			$teller++;
	}
}

// return the array of elements

?>
