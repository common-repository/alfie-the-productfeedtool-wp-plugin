<?php
// Prepare Insert
$Insert = "INSERT INTO ".$wpdb->prefix."alfie_searchproduct(id,";
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
	
	// pak addionele velden
	if($values>5)
	{
		$arrSaveValues[$i][$keys]["extra"] = $data[$i][$keys];
		$extra_velden++;
	}
	
	}
	}

}

// tel hoeveel extra velden er zijn
$extra_velden =  count($sqlpos) -5;

// maak de extra velden aan
$sqlfield ="";
$ex_query = "";
$final_statement = "";
$extra_values = "";
for($i=5; $i<count($sqlpos);$i++)
{
	$sqlfield.="field".$i.",";

	
}
$sqlfield = substr($sqlfield,0,-1);

foreach($arrSaveValues as $productdata)
				{
					 
				// parse alle extra velden!
				if($extra_velden>0)
				{
					for($i=5; $i<count($sqlpos);$i++)
					{
						$ex_query =  "INSERT INTO ".$wpdb->prefix."alfie_searchproduct (id,productnaam,prijs,omschrijving,imageurl,producturl,".$sqlfield.") 
						VALUES ( '".$col_id ."',
						'".santinizeInput($productdata["productnaam"])."',
						'".santinizeInput($productdata["prijs"])."',
						'".santinizeInput($productdata["omschrijving"])."',
						'".santinizeInput($productdata["imageurl"])."',
						'".santinizeInput($productdata["producturl"])."',
						"; 
						$extra_values.= "'".$productdata[$i]["extra"]."',";
						
					}
				}
				// maak de query schoon
				$extra_values = substr($extra_values,0,-1);
				$extra_values .= ")";
				
				// finaliseer de query
				$final_statement = $ex_query.$extra_values;
				$wpdb->query($final_statement);
				
				// leeg de pointers
				$extra_values = null;
				$final_statement = null;
				$teller++;
			}


?>





