<?php function alfie_option_page()
{	$content_dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	?>
<div class="wrap">
<h2> Add  here your feed generated from Alfie. </h2>
<BR>Here you can add the colindex and source url generated from Alfie. The final result is product display.
<BR>Use the short tag to display the product.. The <B> Short-tag</B> will be generated when you imported the feed.<BR><BR>	

<form name="alfie_insert" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<table width="693" height="167" style="border:1px solid gray;">
<tr> 
    <td align="left"> <B>Name </B></td>
    <td align="left"> 
    <input type="text" name="naam" value="" size="50">
    
   </td>
  </tr>
  
  <input type="hidden" name="oscimp_hidden" value="Y">
  <tr> 
    <td align="left"> <B>Alfie Source URL</B></td>
    <td align="left"> 
   <input type="text" name="source_url" value="" size="50" autocomplete="off">
    
   </td>
  </tr>
  
  
  <tr> 
    <td align="left"> <B>Alfie Colindex</B></td>
    <td align="left"> 
    <input type="text" name="colindex" value="" autocomplete="off">
    
   </td>
  </tr>
  
   <tr> 
    <td align="left"> <B>Type Feed</B></td>
    <td align="left"> 
    <select name="type_feed">
    <option value="single"> Single feed </option> 
    <option value="match"> Match feed </option>
    <option value="searchfilter"> SearchFilter </option>
    
    </select>
    
   </td>
  </tr>
  
   <tr> 
    <td align="left"> <input type="submit" name="Submit" value="Import feed" /></td>
    <td align="left"> 
   </td>
  </tr>
</table>
</form>

	<?php
	if($_POST['oscimp_hidden'] == 'Y') {
		$naam  = $_POST['naam'];
		$source_url = esc_url($_POST['source_url']);
		$colindex= trim($_POST['colindex']);
		$type_feed = $_POST['type_feed'];
		if(!$colindex || !$source_url)
		{
			echo '<div class="error"> Error, all fields are required! Please make sure all fields are correctly filled in! </div>';
		} else {
			$data = array();
			global $wpdb;
			$data["naam"] = $naam;
			$data["type"] = $type_feed;
			$data["colindex"] = $colindex;
			
			$data["source_url"] = $source_url;
			if($wpdb->insert($wpdb->prefix.'alfie_colindex', $data)==true)
			{
				// doe  single product import
				if($type_feed=="single")
				{
				
				$feedData  = updateSingleFeed($colindex,$source_url);
				$col_id = mysql_insert_id();
				foreach($feedData as $productdata)
				{
				$wpdb->query("INSERT INTO ".$wpdb->prefix."alfie_producten (col_id,productnaam,prijs,omschrijving,imageurl,producturl) 
				VALUES ( '".$col_id ."',
						'".santinizeInput($productdata["productnaam"])."',
						'".santinizeInput($productdata["prijs"])."',
						'".santinizeInput($productdata["omschrijving"])."',
						'".santinizeInput($productdata["imageurl"])."',
						'".santinizeInput($productdata["producturl"])."')") or die (mysql_error());
						$teller++;
			}
		echo "<div class=updated> Done!. Products added: <B>". $teller ."</B>. Use the following code in your post to display:
				<BR> <B> [alfie_single_product id=\"".$col_id."\"]  </B></div>";
			}
				
				
			 if($type_feed=="match")
				{
				// doe  match product import
				$col_id = mysql_insert_id();
				include 'importmatchfeed.php';
				echo "<div class=updated>Done!. <B>Matching</B> Products added: <B>". $teller ."</B>. Use the following code in your post to display: <BR> <B> [alfie_matchfeed id=\"".$col_id."\"]  </B></div>";		
				
				}
				
			if($type_feed =="searchfilter")
			{
				$col_id = mysql_insert_id();
				include 'savesearchfilter.php';
				echo "<div class=updated>Done! (Records Fetched: ".$teller."). Use the following code in your post to display the box  <BR> <B> [alfie_search_filter id=\"".$col_id."\"]  </B></div>";	
			}
				
				
					
			
		}
	}
}


}

function alfie_plugin_menu()
{
	
	add_menu_page('Alfie','Alfie - Main', 'manage_options', 'alfie-feed-plugin', 'alfie_option_page');
	add_submenu_page('alfie-feed-plugin', 'Manage feeds', 'Manage Feeds', 'manage_options', 'alfie-manage', 'alfie_manage');
	add_submenu_page('alfie-feed-plugin', 'Approve reviews', 'Approve reviews', 'manage_options', 'alfie-review', 'alfie_review');
	add_action('admin_menu', 'alfie-manage');
	add_action('admin_menu', 'alfie-review');	
}
add_action('admin_menu', 'alfie_plugin_menu');

