<?php ob_start(); include 'functions.php';
/*
Plugin Name: Alfie - Feed Plugin
Plugin URI: http://www.productfeedtool.nl
Description: This plugin will help you make "Price Comparison" and Searchfilters just in seconds!
Author: The Alfie Develop Team
Version: 1.2.1
Author URI: http://www.productfeedtool.nl/
*/



// Plugin Activation
register_activation_hook( __FILE__, 'myplugin_activate' );
function myplugin_activate()
{
global $wpdb;
$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix ."alfie_colindex` (
  `col_id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  `colindex` varchar(255) NOT NULL,
  `source_url` varchar(255) NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`col_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql = "
CREATE TABLE IF NOT EXISTS `".$wpdb->prefix ."alfie_producten` (
  `pID` int(11) NOT NULL AUTO_INCREMENT,
  `col_id` int(11) NOT NULL,
  `productnaam` varchar(255) NOT NULL,
  `prijs` float(11) NOT NULL,
  `omschrijving` text NOT NULL,
  `imageurl` varchar(255) NOT NULL,
  `producturl` varchar(255) NOT NULL,
  `match_sequence` int(11) NOT NULL,
  `adv_name` varchar(255) NOT NULL,
  `adv_img` varchar(255) NOT NULL,
  `adv_url` varchar(255) NOT NULL,
  PRIMARY KEY (`pID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5347 ;";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql = "CREATE TABLE `".$wpdb->prefix ."alfie_reactions` (
  `r_id` int(11) NOT NULL AUTO_INCREMENT,
  `colid` int(11) NOT NULL,
  `matchid` int(11) NOT NULL,
  `naam` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ranking` varchar(6) NOT NULL,
  `message` text NOT NULL,
  `approved` varchar(1) NOT NULL DEFAULT '0',
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`r_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);


$sql = "CREATE TABLE `".$wpdb->prefix ."alfie_searchproduct` (
  `id` int(11) NOT NULL,
  `productnaam` varchar(255) NOT NULL,
  `prijs` varchar(255) NOT NULL,
  `omschrijving` text NOT NULL,
  `imageurl` varchar(255) NOT NULL,
  `producturl` varchar(255) NOT NULL,
  `field5` varchar(255) NOT NULL,
  `field6` varchar(255) NOT NULL,
  `field7` varchar(255) NOT NULL,
  `field8` varchar(255) NOT NULL,
  `field9` varchar(255) NOT NULL,
  `field10` varchar(255) NOT NULL,
  `field11` varchar(255) NOT NULL,
  `field12` varchar(255) NOT NULL,
  `field13` varchar(255) NOT NULL,
  `field14` varchar(255) NOT NULL,
  `field15` varchar(255) NOT NULL,
  `field16` varchar(255) NOT NULL,
  `field17` varchar(255) NOT NULL,
  `field18` varchar(255) NOT NULL,
  `field19` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

}

register_deactivation_hook( __FILE__, 'myplugin_deactivate' );
function myplugin_deactivate()
{
	global $wpdb;
	$wpdb->query("DROP TABLE ".$wpdb->prefix ."alfie_producten,".$wpdb->prefix ."alfie_colindex,".$wpdb->prefix ."alfie_reactions,".$wpdb->prefix."alfie_searchproduct");
	
}
// Include pages
include 'include/alfie-option.php';
include 'include/alfie-review.php';
include 'include/alfie-manage.php';



/*******************************************************SHORTCODE searchfilter ***********************************************************/
function alfie_searchfilt( $atts ) {
	// get the content dir
	$content_dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	?>
	<link href="<?php echo  $content_dir."/style/style.css";?>" rel="stylesheet" type="text/css" />
	<?php   
	// extract shortcode
	global $wpdb;
	extract( shortcode_atts( array(
		'id' => 'something',
	), $atts ) );
	
// run query to get the colindex
$row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."alfie_colindex WHERE col_id = '".$id."'");
// parse the colindex and make it parsable
$arrColindex = explode(",",$row->colindex);
$sqlpos = array();
for($i=0; $i<count($arrColindex); $i++)
{
	$explodeAr = explode("=>",$arrColindex[$i]);
	$sqlpos[$explodeAr[0]] = $explodeAr[1];
	
}
$file_url = $row->source_url;
# Read bin files only
$openFile = fopen($file_url, "rb");
#Read all the csv rows
$data = readallcsvrows($openFile);
// sla de keys op
$savekeys=array();
//maak een array van de zoekwaardes
$searchvalues = array();
// de zoek noedel
// ajax getter
?>
<link href="http://www.productfeedtool.nl/pb/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<link href="http://www.productfeedtool.nl/pb/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $content_dir;?>/js/ajax.js"></script>
<?php 
// get datavalues from the csv feeds
for($i=1; $i<count($data); $i++)
{
foreach($sqlpos as $keys=>$values)
{
    if(isset($data[$i][$keys])){     
    // get only the add fields
    if($values>5){
        $searchvalues["search".$keys][] =  $data[$i][$keys];
        $savekeys[]  = $keys;
    }
     }
}
}
$savekeys = array_unique($savekeys);
// maak elementen uniek
foreach($savekeys as $arrkeys)
{
$searchvalues["search".$arrkeys] = array_unique($searchvalues["search".$arrkeys]);
}
?>
<form id="form1" name="form1" method="post" >
<?php 
// geef een teller om zoekelement mee te bepalen
$zoekboxTeller = 0;
// keysavecounter
$keysavecounter = 0;
// string om keys te bewaren
$keyString = "";
// bewaar de keys
foreach($savekeys as $vals)
{
    $keysavecounter++;
    if($keysavecounter!=count($savekeys))
    {
        $keyString.=$vals.",";
        
    } else {
        $keyString .= $vals;
    }
    
}
echo '<table width="296" border="0" cellpadding="4" cellspacing="0">';
echo '<tr> ';
// maak de zoekbox
foreach($savekeys as $keys)
{
    $zoekboxTeller++;
    if($zoekboxTeller==1)
    {
     echo '<td><select id="'.$keys.'" name="'.$keys.'"  onchange="firstBox(\''.$keyString.'\',\''.$content_dir.'\',\''.$id.'\');">';
     echo '<option value=>Maak uw Keuze</option>';
    foreach($searchvalues["search".$keys] as $values)
    {
        echo '<option value='.urlencode($values).'>'.strLength($values,50).'</option>';
    }
    echo '</select></td>';
    } else {
         echo '<td><select id="'.$keys.'"   name="'.$keys.'"  onchange="secondBoxes(\''.$keyString.'\',\''.$keys.'\',\''.$zoekboxTeller.'\',\''.$content_dir.'\',\''.$id.'\');">';
        echo '<option value=>Maak uw Keuze</option>';
   
    echo '</select></td>';
        
    }
}
echo ' 
<td><input type="submit" name="search"  value="Search"/></td>
</tr>
</table>';
?>

</form>
<?php 	
if(isset($_POST['search']))
{
	// Fetch one row
	$obj_colindex =  $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."alfie_colindex WHERE col_id = '".$id."'");
	// set the counter to 0 in order to determine if there are more fields
    $teller = 0;
	// prepare the query
    $sql = "SELECT * FROM ".$wpdb->prefix."alfie_searchproduct";
	// unset the search it is not neceserry in the query
    unset($_POST['search']);
   // loop through the foreach to get the post vals
    foreach($_POST as $keys=>$values){
        $teller++;
		// set first query stack
        if($teller==1){
            $sql.=" WHERE "."field".$keys."='".mysql_escape_string(trim(urldecode($values)))."'";
        } else {
        	// set the second or more query stacks
            if($values!="") 
            $sql.="AND field".$keys."='".mysql_escape_string(trim(urldecode($values)))."'";
        }
        
    }
	// run the query
    $query = mysql_query($sql) or die (mysql_error());
	echo '<div class="col-container">';
    while($record = mysql_fetch_array($query))
    {
 	?>
           <!-- For you 2 edit In CSS  -->
			<div class="c-container">
			<div class="c-header">
			<a href="<?php echo $record["producturl"];?>" target="_blank"> <?php echo $record["productnaam"];?> </a> </div>
			<div class="c-image">  <span class="norm_prijs"> &euro;  <?php echo number_format($record["prijs"],2,',','.');?>  </span> <img src="<?php echo $record["imageurl"];?>" width="100" height="100" alt=""></a> </div>
			<div class="c-content">
			<?php echo $record["omschrijving"];?>
			<div class="c-btn"> 
			<a href="<?php echo $record["producturl"];?>" rel="nofollow" target="_blank">  Order now </a> </div>  
			</div>
			</div>
<?php   }} ?>
	
<?php }
add_shortcode( 'alfie_search_filter', 'alfie_searchfilt' );






/*******************************************************SHORTCODE Singlefeed ***********************************************************/
function alfie_single_func( $atts ) {
	$content_dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	?>
	<link href="<?php echo  $content_dir."/style/style.css";?>" rel="stylesheet" type="text/css" />
	<?php   
	global $wpdb;
	extract( shortcode_atts( array(
		'id' => 'something',
	), $atts ) );
	
$qtelReacties = mysql_query( 
	"SELECT count(*) 
	FROM ".$wpdb->prefix."alfie_producten 
	WHERE col_id='".intval($id)."'
	");
$items_totaal =  mysql_result($qtelReacties, 0);

	
$huidige_pagina = 0;
$items_per_pagina = 10; 
$aantal_paginas =  ceil($items_totaal / $items_per_pagina); // het aantal items per pagina
if(isset($_GET['pagina']) && is_numeric($_GET['pagina']) && $_GET['pagina'] > 0 && $_GET['pagina'] < $aantal_paginas) {
    $huidige_pagina = $_GET['pagina'];
}
echo " <BR><div align=center> Page </span>";
for($i = 0; $i < $aantal_paginas; $i++) {
    if($huidige_pagina == $i) {
        echo "<B>".($i+1)."</B>";
    } else {
    	if(strpos($_SERVER['QUERY_STRING'],"?"))
		{ ?>
			<a href=<?php echo the_permalink();?>?&pagina=<?php echo $i;?>><B><?php echo ($i+1);?></B>  </a>
		 <?php } else { ?>
		 		
		 		<?php 
				if(strpos($_SERVER['QUERY_STRING'],"?") ==false)
				{ ?>
					<a href=<?php echo the_permalink();?>?&pagina=<?php echo $i;?>><B><?php echo ($i+1);?></B>  </a>
				<?php } else { ?>
				<a href=<?php echo the_permalink();?>?pagina=<?php echo $i;?>><B><?php echo ($i+1);?></B>  </a>
				<?php } ?>
	
		 	
		 	
		 	<?php }
	?>
       
    <?php }
    if($i < $aantal_paginas - 1) {
        echo " - ";
    }
 
}
echo '</div>';
$offset = $huidige_pagina * $items_per_pagina;


// show content
$parse_result = $wpdb->get_results( 
	"SELECT * 
	FROM ".$wpdb->prefix."alfie_producten 
	WHERE col_id='".intval($id)."' LIMIT ".$offset."," .$items_per_pagina
	);
foreach ( $parse_result as $elems ) 
{
?>
	
<div class="c-container">
<div class="c-header">
<a href="<?php echo $elems->producturl;?>" target="_blank"><?php echo $elems->productnaam;?></a> </div>
<div class="c-image">  <span class="norm_prijs"> &euro; <?php echo number_format($elems->prijs,2,',','.');?> </span> <img src="<?php echo $elems->imageurl;?>" width="100" height="100" alt=""></a> </div>
<div class="c-content"><?php echo strLength($elems->omschrijving,150);?><br />
<br />

<div class="btn"> 
<a href="<?php echo $elems->producturl;?>" rel="nofollow" class="btn" target="_blank"><B>NU DIRECT BESTELLEN! </B></a> </div>  
</div>
</div>
	
<?php }

}
add_shortcode( 'alfie_single_product', 'alfie_single_func' );




/*******************************************************SHORTCODE MATCHFEED ***********************************************************/
function alfie_match_feed_func( $atts ) {
	global $wpdb;
		$content_dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	extract( shortcode_atts( array(
		'id' => 'something',
	), $atts ) );
	?>
<link href="<?php echo  $content_dir."/style/style.css";?>" rel="stylesheet" type="text/css" />
<script src="<?php echo  $content_dir."/js/common.js";?>"> </script>
	<?php 
// als de matchID gezet is toon een nieuwe pagina
	if(isset($_GET['matchid']))
	{
	$parse_adv =  $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."alfie_producten WHERE match_sequence='".intval($_GET['matchid'])."' AND col_id = '".intval($_GET['colid'])."' ORDER BY prijs ASC") or die (mysql_error());
	$teller = 0;
	foreach ( $parse_adv as $adv_elem ) 
		{ 
			$teller++;
			if($teller==1)
			{?>
				<!-- Custimize here your layout -->	
				<!-- Show the first product -->
					<div class="c-container">
					<div class="c-header"> 
					<a href="<?php echo $product_url;?>" target="_blank"  title="<?php echo $adv_elem->productnaam;?>"><?php echo strLength($adv_elem->productnaam,50);?></a> 
					</div> 
						
					<div class="c-image">  			
					
					<img src="<?php echo $adv_elem->imageurl;?>" width="220" height="150" alt=""> </div> 
					
					<div class="c-content">  
						<B> <?php echo $adv_elem->productnaam;?></B> <BR><BR><?php echo $adv_elem->omschrijving;?> <BR>
							
							 <img src="http://www.productfeedtool.nl/user_images/<?php echo $adv_elem->adv_img;?>" width="60" heigth="30"> <BR>
					  <?php $avgrate=  GetProductVote(intval($_GET['matchid']),intval($_GET['colid']));
					  for($i=0; $i<$avgrate;$i++)
					  {?>  <img src="<?php echo  WP_PLUGIN_URL."/alfie/images/star.png";?>">
					  <?php } ?><BR>
                     <div align=center ><h2>Goedkoopste prijs: &euro; <?php echo number_format($adv_elem->prijs,2,',','.');?></h2></span> </div>

					 <div align=center><a class="btn" href="<?php echo $adv_elem->producturl;?> rel="nofollow" target="_blank">  <B>NU DIRECT BESTELLEN!</B></a> </div>
					  		
					</div>
					</div><BR><BR>
						<div style="margin-left:29px;"><h3> Vergeleken bij de volgende winkels:</h3> <hr></div>
		<?php 	} // end if?>
		
			<!-- Show the advertisers-->
			<div style="margin-left:29px;">
				<a href="<?php echo $adv_elem->adv_url;?>" target="_blank"> <B> <?php echo $adv_elem->adv_name;?>  </B></a><BR>
				<img src="http://www.productfeedtool.nl/user_images/<?php echo $adv_elem->adv_img;?>" width="60" heigth="30"> <BR>
				  <span class="small_price">&euro;<?php echo number_format($adv_elem->prijs,2,',','.');?>  </span>
				<HR>
			</div>
		<?php } // end loop ?>
		<?php
		  if(isset($_POST['submitform']))
	   {
	   		$naam = santinizeInput($_POST['naam']);
			$email = santinizeInput($_POST['email']);
			$ranking = santinizeInput($_POST['ranking']);
			$message = santinizeInput($_POST['bericht']);
			if(!$email || !$naam || !$message)
			{
				echo "<div align=center> <h2 style=color:red> <B>All fields are required</B> </h2></div>";
			} else {
				if($_COOKIE['posted'])
					{
						echo '<div align=center><h2 style=color:red>Spamming not allowed dude!</h2></div>';
					} else {
				$query = $wpdb->query("INSERT INTO ".$wpdb->prefix."alfie_reactions (colid,matchid,naam,email,ranking,message)
							VALUES ('".intval($_GET['colid'])."',
									'".intval($_GET['matchid'])."',
									'".$naam."',
									'".$email."',
									'".$ranking."',
									'".$message."')");
					}
									
				if($query)
				{
					setcookie("posted","1",time()+50);
					
					echo "<div align=center><h2 style=color:green>Thanks your message has been submited. The administrator will approve your message first.</h2></div>";
				}
							
				
			}
	   }
	   
// get reacties if there are reactions
$parse_rec =  $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."alfie_reactions WHERE matchid='".intval($_GET['matchid'])."' AND colid = '".intval($_GET['colid'])."' AND approved='1' ORDER BY datum DESC");

	foreach ( $parse_rec as $rec_elem ) 
		{
			echo '<div style="margin-left:29px;">';
			echo '<B>Reaction from: '.$rec_elem->naam.'</B><BR>';
			echo $rec_elem->message.'<BR>Rating:';
			for($i=0; $i<$rec_elem->ranking;$i++)
			{?>
				<img src="<?php echo  $content_dir."/images/star.png";?>">
				
			<?php }
			echo '<BR>'.$rec_elem->datum.'<BR>';
			echo '<BR><HR></div>';
		} 
		?> 
		<!-- Submission form-->
		<div style="margin-left:29px;"><h1> Add your comment here.</h1></div>
		<fieldset>
	  <form method="post">
	  <div class="ctrlHolder">
	    <label for="naam">Your Name<span class="required">*</span></label>
	    <input type="text" id="naam" name="naam" class="required" />

	  </div>
	
	  <div class="ctrlHolder">
	    <label for="email">Your Email*</label>

	    <input type="text" name="email" id="email" class="email" />
	  </div>

	  
	  <div class="ctrlHolder">
	    <label for="onderwerp">Rate this product.<span class="required"></span></label>
	    <select name="ranking">
	    	<option value="1"> *</option>
	    	<option value="2"> **</option>
	    	<option value="3"> ***</option>
	    	<option value="4"> ****</option>
	    	<option value="5"> *****</option>
	    </select>
	  </div>
	  
	 
	  <div class="ctrlHolder">
	    <label for="bericht">Message<span class="required">*</span></label>

	    <textarea name="bericht" id="bericht" class="required" cols="25" rows="5" style="width:350px;" ></textarea>
	  </div>
	   <div class="buttonHolder">
	 <input type="submit" name="submitform" value="Submit message" />
	  
	  </div>
	  </fieldset>
	   </form>
	   <?php
	} else {
	// show main Matching products
 $qtelReacties = $wpdb->get_results("SELECT * 
	FROM ".$wpdb->prefix."alfie_producten 
	WHERE col_id='".intval($id)."'GROUP BY match_sequence
	");
$items_totaal = count($qtelReacties);
$huidige_pagina = 0;
$items_per_pagina = 10; 
$aantal_paginas =  ceil($items_totaal / $items_per_pagina); // het aantal items per pagina
if(isset($_GET['pagina']) && is_numeric($_GET['pagina']) && $_GET['pagina'] > 0 && $_GET['pagina'] < $aantal_paginas) {
    $huidige_pagina = $_GET['pagina'];
}
echo " <BR><div align=center> Page </span>";
for($i = 0; $i < $aantal_paginas; $i++) {
    if($huidige_pagina == $i) {
        echo "<B>".($i+1)."</B>";
    } else {
	
      if(strpos($_SERVER['QUERY_STRING'],"?"))
		{ ?>
			<a href=<?php echo the_permalink();?>?&pagina=<?php echo $i;?>><B><?php echo ($i+1);?></B>  </a>
		 <?php } else { ?>
		 	
		 	
		 		<?php 
	if(strpos($_SERVER['QUERY_STRING'],"?") ==false)
	{ ?>
		<a href=<?php echo the_permalink();?>?&pagina=<?php echo $i;?>><B><?php echo ($i+1);?></B>  </a>
	<?php } else { ?>
	<a href=<?php echo the_permalink();?>?pagina=<?php echo $i;?>><B><?php echo ($i+1);?></B>  </a>
	<?php } ?>
	
	
		 	<?php }
   }
    if($i < $aantal_paginas - 1) {
        echo " - ";
    }
 
}
echo '</div>';
$offset = $huidige_pagina * $items_per_pagina;
$parse_result = $wpdb->get_results( 
	"SELECT * 
	FROM ".$wpdb->prefix."alfie_producten 
	WHERE col_id='".intval($id)."'
	GROUP BY match_sequence LIMIT ".$offset."," .$items_per_pagina
	);

foreach ( $parse_result as $elems ) 
{
	$count_match_occ = checkMatchingProduct($elems->match_sequence,$id);
	$match_details = getCheapestDetails($elems->match_sequence);
	$match_details = sorteleminarray($match_details,'prijs');
	$price = "";
	$product_url = "";
	// check here if there are matching found 
	// if so choose the cheapest product otherwise the normal product
	if($elems->match_sequence!=0)
	{
		$price = $match_details[0]["prijs"];
		$adv_image =  $match_details[0]["adv_img"];
		$product_url = $match_details[0]["producturl"];
	} else { 
		$price = $elems->prijs;
		$product_url  = $elems->producturl;
		
	}?>
	
<!-- Custimize here your layout -->	
<div class="c-container">
<div class="c-header"> 
<a href="<?php echo $product_url;?>" target="_blank"  title="<?php echo $elems->producturl;?>"><?php echo strLength($elems->productnaam,50);?></a> 
</div> 
	
<div class="c-image">  			
<span class="norm_prijs"> &euro;  <?php echo number_format($price,2,',','.');?> </span> 
<img src="<?php echo $elems->imageurl;?>" width="100" height="100" alt="" > </div> 

<div class="cats-content">  
	<B> <?php echo $elems->productnaam;?></B> <BR><BR><?php echo strLength($elems->omschrijving,200);?> <BR>
		
		 <img src="http://www.productfeedtool.nl/user_images/<?php echo $adv_image;?>" width="60" heigth="30"> <BR>
<?php 
// check if it is a matching product
 if($elems->match_sequence!=0)
{
?>
 Vergeleken bij

 <a href="<?php echo the_permalink(); ?>?&matchid=<?php echo $elems->match_sequence."&colid=".$elems->col_id;?>">
 
 
 
 <B> <?php echo  $count_match_occ;?> </B>  winkels.</a> <HR><BR>	
<?php } ?>   
  <?php
  // get the stars
  	$content_dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
   $avgrate=  GetProductVote($elems->match_sequence,intval($elems->col_id));
  for($i=0; $i<$avgrate;$i++)
  {?>  <img src="<?php echo  $content_dir."/images/star.png";?>">
  <?php } ?> 
					  
					  <?php
					  if(GetVotedUsers($elems->match_sequence,intval($elems->col_id))!=0)
					  {?>
<?php echo $product_url;?>					  Reviewed by <?php echo GetVotedUsers($elems->match_sequence,intval($elems->col_id)); ?> users.<?php } ?><BR><BR>
<?php

if(strpos($_SERVER['QUERY_STRING'],"?"))
{?>					  
<a class="btncat" href="<?php echo the_permalink(); ?>&matchid=<?php echo $elems->match_sequence."&colid=".$elems->col_id;?>"> <B>Meer informatie</B></a> |  
<?php }  else { ?>
	
	<?php 
	if(strpos($_SERVER['QUERY_STRING'],"?") ==false)
	{ ?>
		<a class="readmore" href="<?php echo the_permalink(); ?>?&matchid=<?php echo $elems->match_sequence."&colid=".$elems->col_id;?>"> <B>Meer informatie </B></a> |
	<?php } else { ?>
	<a class="readmore" href="<?php echo the_permalink(); ?>?matchid=<?php echo $elems->match_sequence."&colid=".$elems->col_id;?>"> <B>Meer informatie </B></a> |
	<?php } ?>
	 
	 
	<?php } ?>
	
	
 <a class="btn" href="<?php echo $product_url;?>" rel="nofollow" target="_blank">  <B>Direct bestellen</B></a> 
  		
</div>
</div>
<?php }
}?>

<?php
}
add_shortcode( 'alfie_matchfeed', 'alfie_match_feed_func' ); ?>