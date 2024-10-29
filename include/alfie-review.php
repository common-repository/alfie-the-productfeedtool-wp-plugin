<?php
function alfie_review()
{
	global $wpdb;
		$content_dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	if(isset($_GET['approve']))
	{
		$r_id =intval($_GET['approve']);
		$wpdb->query("UPDATE ".$wpdb->prefix."alfie_reactions SET approved='1' WHERE r_id ='".$r_id."'");
		wp_redirect("admin.php?page=alfie-review");
		
	}
	
	
	if(isset($_GET['delete']))
	{
		$r_id =intval($_GET['delete']);
		$wpdb->query("DELETE FROM ".$wpdb->prefix."alfie_reactions WHERE r_id ='".$r_id."'");
		wp_redirect("admin.php?page=alfie-review");
		
	}
	
	if(isset($_GET['readrev']))
	{
	$r_id =intval($_GET['readrev']);
	$row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."alfie_reactions WHERE r_id = '".$r_id."'");
	echo "<a href=admin.php?page=alfie-review&approve=".$r_id."><B>APPROVE REVIEW</B><hr></a><BR>Name:".$row->naam."<BR>Email:".$row->email."<BR>
	
	<B>Review:</B><BR><HR>";
	echo $row->message;
	} else {?>
	
	
	<BR>
		<img src="<?php echo  $content_dir."/images/logo.jpg";?>" width="100" heigth="100">
	<h2> Manage reviews here.</h2>
	Some people migth have submitted a review. Please approve the review here and look if it is no crap.<BR>
		<BR>	
	<table width="850" height="64" border="0" cellpadding="2" cellspacing="2" style="border:1px solid black;">
   <tr bgcolor="#999999">
    <td width="176" align="left" valign="top" style="background:#280028; color:white;"><strong>Email</strong></td>
    <td width="176" align="center" valign="top" style="background:#280028; color:white;"><strong>Date submitted</strong></td>	
	<td width="176" align="left" valign="top" style="background:#280028; color:white;"><strong>Read review</strong></td>
	<td width="176" align="center" valign="top" style="background:#280028; color:white;"><strong>Approve</strong></td>
	<td width="176" align="center" valign="top" style="background:#280028; color:white;"><strong>Delete</strong></td>
	</tr>
	<?php
	$parse_result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."alfie_reactions WHERE approved='0' ORDER BY datum DESC");
	foreach ( $parse_result as $elems ) 
	{?>
	<tr>
	    <td align="left" valign="top"><?php echo $elems->email;?></td>
    <td align="center" valign="top"><?php echo $elems->datum;?></td>
    <td align="center" valign="top"><a href="admin.php?page=alfie-review&readrev=<?php echo $elems->r_id;?>">Read</a></td>
    <td align="center" valign="top"><a href="admin.php?page=alfie-review&approve=<?php echo $elems->r_id;?>">Y</td>
    	  <td align="center" valign="top"><a href="admin.php?page=alfie-review&delete=<?php echo $elems->r_id;?>">Delete</td>
  </tr>
  <?php } ?>
	</table>
<?php }
}