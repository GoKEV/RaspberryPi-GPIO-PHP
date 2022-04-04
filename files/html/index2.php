<?php

/////////////////////////////////////////////////////////////////////
////  Edit these variables to define the color and labels of your button
////
////	text_label:	Literal text that will be displayed beside the button
////	io:		the ID of the GPIO you wish to control with this button
////	op:		button is the only function for now.
////	w:		image width in pixels (the image is natively 300x300)
////	c:		color (red, yellow, green, blue are included)
////				a Gimp template is included in the HTML directory
////				to make your own different colors if you wish.
/////////////////////////////////////////////////////////////////////

$button[0] = array(
	"text_label"=>"This is IO 0.  Click on, click off.",
	"io"=>"0",
	"op"=>"button",
	"w"=>"100",
	"c"=>"red"
);

$button[1] = array(
	"text_label"=>"This is IO 1.  Click on, click off.",
	"io"=>"1",
	"op"=>"button",
	"w"=>"100",
	"c"=>"yellow"

);

$button[2] = array(
	"text_label"=>"This is IO 2 (1 second momentary, default on)",
	"io"=>"2",
	"op"=>"button",
	"w"=>"100",
	"c"=>"green",
	"v"=>"1",
	"d"=>"1"

);

$button[3] = array(
	"text_label"=>"This is IO 3 (3 second momentary, default off)",
	"io"=>"3",
	"op"=>"button",
	"w"=>"100",
	"c"=>"blue",
	"v"=>"0",
	"d"=>"3"

);


/////////////////////////////////////////////////////////////////////
////  Editing anything below this will change the page funtion.
/////////////////////////////////////////////////////////////////////

?>
<table border="0" valign="top" cellpadding="10">
<?php

foreach( $button as $var => $vals){
	$link_button = "/api.php?op=button&io=$vals[io]&w=$vals[w]&c=$vals[c]";
	$link_button .= ( isset($vals[d]) ? "&value=$vals[v]&d=$vals[d]" : "");
?>
<tr>
	<td align="center" valign="middle">
		<?=$vals[text_label]?>
	</td>
	<td align="center" valign="middle">
		<iframe
			name="<?=$iframe_name?>"
			frameborder="0"
			scrolling="no"
			width="<?=($vals[w] + 10)?>"
			height="<?=($vals[w] + 10)?>"
			src="<?=$link_button?>"
		></iframe>
	</td>
</tr>
</iframe><br>

<?php

}
