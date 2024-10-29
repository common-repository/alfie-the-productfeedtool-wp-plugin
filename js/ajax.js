function urldecode(str) {
   return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}

function firstBox(vals,uri,id)
	{
		
	var split_string = vals.split(",");
	
	for(var x=1; x<split_string.length;x++)
	{
		$('#'+split_string[x]).empty();
		$("<option value=>Maak uw Keuze</option>").appendTo("#"+split_string[x]);
		
		
	}
	
	var teller = 0;
	$.ajax({
  	url: uri+'ajax.php?mode='+escape($('#'+split_string[0]).val())+'&ref='+vals+'&colid='+id,
  	success: function(data) {
	var split_text = data.split(";");
	$("#"+split_string[0]).change(function() {
 	$("#"+split_string[1]).empty();
 	});
 	$("#"+split_string[1]).empty();
 	$("<option value=>Maak uw Keuze</option>").appendTo("#"+split_string[1]);
	for(var i= 0 ; i<split_text.length; i++)
	{
		if(split_text[i]!="")
	  $("<option value=\""+split_text[i]+"\">"+urldecode(split_text[i])+"</option>").appendTo("#"+split_string[1]);
		
	} 

  	}
	});
}



	
function secondBoxes(optvals,curr,numberbox,uri,id)
{
	var split_string = optvals.split(",");
	var buildrefstring ="";
	var huidige_pos = numberbox;
	
	
	for(var x=huidige_pos; x<split_string.length;x++)
	{
		
		$('#'+split_string[x]).empty();
		$("<option value=>Maak uw Keuze</option>").appendTo("#"+split_string[x]);
		
	}
	for(var i=0; i<split_string.length; i++)
	{
		buildrefstring+="&" +split_string[i]+ "="+escape($.trim($('#'+split_string[i]).val()));
		
	}
	
	$.ajax({
		
  	url: uri+'ajax.php?mode2='+escape($('#'+curr).val())+'&ref='+optvals+'&curr='+numberbox+buildrefstring+'&colid='+id,
  	success: function(data) {
  		var split_text = data.split(";");
  		
  		
  		for(var i= 0 ; i<split_text.length; i++)
		{
		if($('#'+curr).val()!="")
		if(split_text[i]!="")
	
  		$("<option value=\""+split_text[i]+"\">"+urldecode(split_text[i])+"</option>").appendTo(('#'+split_string[numberbox]));
  		}
  		
  	}
  	});
	
}