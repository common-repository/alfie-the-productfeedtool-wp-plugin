<?php function alfie_manage()
{
	
	global $wpdb;
	if(isset($_GET['update']))
	{
		$col_id = intval($_GET['update']);
		$type = mysql_escape_string($_GET['type']);
		$row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."alfie_colindex WHERE col_id = '".$col_id."'");
		if($type=="single")
		{
			$wpdb->query("DELETE FROM ".$wpdb->prefix."alfie_producten WHERE col_id='".$col_id."'");
			
				$teller = 0;
				$arrSaveValues = updateSingleFeed($row->colindex,$row->source_url);
				foreach($arrSaveValues as $productdata)
				{
				mysql_query("INSERT INTO ".$wpdb->prefix."alfie_producten (col_id,productnaam,prijs,omschrijving,imageurl,producturl) 
				VALUES ( '".$col_id ."',
				'".santinizeInput($productdata["productnaam"])."',
				'".santinizeInput($productdata["prijs"])."',
				'".santinizeInput($productdata["omschrijving"])."',
				'".santinizeInput($productdata["imageurl"])."',
				'".santinizeInput($productdata["producturl"])."')") or die (mysql_error());
					$teller++;
				} 
				if($teller!=0)
				{
				echo '<div class=updated highlight> The feed <B>'.$row->naam.'</B> is UPDATED with <B>'.$teller.'</B> new rows!</div>';
				$wpdb->query("UPDATE wp_alfie_colindex SET datum = NOW() WHERE col_id='".$col_id."'");
				}
		
		}
		
		if($type=="match")
		{
			$colindex = $row->colindex;
			$col_url = $row->source_url;
			
			include 'updatematchfeed.php';
		}
		
		if($type=="searchfilt")
		{
			// verwijder oude data
			$wpdb->query("DELETE FROM ".$wpdb->prefix."alfie_searchproduct WHERE id = '".$col_id."'");
			$colindex = $row->colindex;
			$source_url = $row->source_url;
			include 'savesearchfilter.php';
			$wpdb->query("UPDATE wp_alfie_colindex SET datum = NOW() WHERE col_id='".$col_id."'");
			echo '<div class=updated highlight> The feed <B>'.$row->naam.'</B> is UPDATED with <B>'.$teller.'</B> new rows!</div>';
		}
		
		
		
	}
	
	if(isset($_GET['delete']))
	{
		if($wpdb->query("DELETE FROM ".$wpdb->prefix."alfie_colindex WHERE col_id = '".intval($_GET['delete'])."'"))
		{
			$wpdb->query("DELETE FROM ".$wpdb->prefix."alfie_producten WHERE col_id = '".intval($_GET['delete'])."'");
			$wpdb->query("DELETE FROM ".$wpdb->prefix."alfie_reactions WHERE colid='".intval($_GET['delete'])."'");
			$wpdb->query("DELETE FROM ".$wpdb->prefix."alfie_searchproduct WHERE id='".intval($_GET['delete'])."'");
			wp_redirect("admin.php?page=alfie-manage");	
			ob_end_flush();
			exit;
		} else {
			echo "<div class=error>Unknown error, something went wrong with the query! </div>";
		}
	} else {
		
	
	$manage_query = $wpdb->get_results( 
	"
	SELECT * 
	FROM ".$wpdb->prefix."alfie_colindex
	");
	

  if(count($manage_query)==0)
  {
  	echo '<div class=updated> No feeds added yet! Please add some feeds. </div>';
  } else  { 
	?><BR>
		
	<h2> Manage your imported feeds here.</h2>
	Over here you can manage your feeds. Press Update to refresh the products with the latest products. <BR>You also see "create short tag". You can copy this tag in your post, and voila! You have the fetched products.<BR>
		<BR>	
	<table width="850" height="64" border="0" cellpadding="2" cellspacing="2" style="border:1px solid black;">
   <tr bgcolor="#999999">
    <td width="176" align="left" valign="top" style="background:#280028; color:white;"><strong>Feed Name</strong></td>
    <td width="176" align="left" valign="top" style="background:#280028; color:white;"><strong>Type Feed</strong></td>
    <td width="176" align="center" valign="top" style="background:#280028; color:white;"><strong>Short Tag</strong></td>
     <td width="176" align="center" valign="top" style="background:#280028; color:white;"><strong> Last update </strong></td>
    <td width="176" align="center" valign="top" style="background:#280028; color:white;"><strong>Update </strong></td>
  	<td width="176" align="center" valign="top" style="background:#280028; color:white;"><strong>Delete </strong></td>
  </tr>
  <?php 
	foreach ( $manage_query as $elems ) 
{?>

 <tr>
    <td align="left" valign="top"><?php echo $elems->naam;?></td>
    <td align="left" valign="top" width="50"><?php echo $elems->type;?></td>
    <td align="center" valign="top" width="250"><?php  if($elems->type=="single") { echo "[alfie_single_product id=\"".$elems->col_id."\"]"; }
	 if($elems->type=="match") {  echo "[alfie_matchfeed id=\"".$elems->col_id."\"]";}
		if($elems->type=="searchfilt") {  echo "[alfie_search_filter id=\"".$elems->col_id."\"]";}
	?>
    
    
    </td>
    <td align="left" valign="top"><?php echo $elems->datum;?></td>
    <td align="center" valign="top"><a href="admin.php?page=alfie-manage&update=<?php echo $elems->col_id;?>&type=<?php echo $elems->type;?>"><B> Update now </b></a></td>
    <td align="center" valign="top"><a href="admin.php?page=alfie-manage&delete=<?php echo $elems->col_id;?>">Delete</a></td>
  </tr>
<?php }}?>

</table>

<?php 
	}
}