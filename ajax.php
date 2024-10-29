<?php
@require_once("../../../wp-config.php");
if(isset($_GET['mode']))
{

    $keyCounter = 0;
	global $wpdb;
    foreach($_GET as $key=>$values)
    {
        $keyCounter++;
		
        // pak de tweede waarde
        if($keyCounter>2)
        {
         // pak de eerste waarde van de query string
            $keySplit = explode(",",$_GET["ref"]);
		
          $query = mysql_query("SELECT * FROM ".$wpdb->prefix."alfie_searchproduct WHERE field".$keySplit[0]." = '".mysql_escape_string(urldecode($_GET['mode']))."'
          						 AND id = '".$_GET['colid']."'
         						 GROUP BY field".$keySplit[1]) 
          or die (mysql_error());
		
          while($record = mysql_fetch_array($query))
          {
              echo urlencode($record["field".$keySplit[1]]).";";
          }
        }
    }
    exit;
}

// tweede ajax mode
if(isset($_GET['mode2']))
{
    // verkrijg de keyword
    $searchdata = mysql_escape_string($_GET['mode2']);
    // verkrijg de ref
    $ref = $_GET['ref'];
    $arrref = explode(",",$ref);
    // verkrijg de huidige box
    // hier kunnen we een positie mee bepalen in de array
    $arrPos = $_GET['curr'];
    $sql = "SELECT *  FROM ".$wpdb->prefix."alfie_searchproduct ";
    $counter = 0;
	$old="";
    // als de array op is doe dan niets
    if(isset($arrref[$arrPos]))
    {
        // bereid query voor
        for($i=0; $i<count($arrref); $i++)
        {
            $counter++;
            // elementen mogen niet leeg zijn
            if($_GET[$arrref[$i]]!="")
            if($counter==1)
            {
            	$sql.= "WHERE id = '".intval($_GET['colid'])."'";
                $sql.=" AND field".$arrref[$i] ." = '".mysql_escape_string(urldecode($_GET[$arrref[$i]]))."'";
            
            } else {
                $sql.=" AND field".$arrref[$i] ." = '".mysql_escape_string(urldecode($_GET[$arrref[$i]]))."'";
				$old = "field".$arrref[$i];
            }
			 if($counter==count($arrref))
			{
				$sql.=" GROUP BY ". $old;
				$old =""; 
			}
			
        }
    } 
	
    $query = mysql_query($sql) or die ("QUERY FAILED: ".$sql);
    while($record = mysql_fetch_array($query))
     {            if(isset($arrref[$arrPos]))
              echo urlencode($record["field".$arrref[$arrPos]]).";";
     }
    
    exit;
}
?>