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
	"text_label"=>"This is IO 0",
	"io"=>"0",
	"op"=>"button",
	"w"=>"100",
	"c"=>"red"
);

$button[1] = array(
	"text_label"=>"This is IO 1",
	"io"=>"1",
	"op"=>"button",
	"w"=>"100",
	"c"=>"yellow"

);

$button[2] = array(
	"text_label"=>"This is IO 2",
	"io"=>"2",
	"op"=>"button",
	"w"=>"100",
	"c"=>"green"

);

$button[3] = array(
	"text_label"=>"This is IO 3",
	"io"=>"3",
	"op"=>"button",
	"w"=>"100",
	"c"=>"blue"

);


/////////////////////////////////////////////////////////////////////
////  Editing anything below this will change the page funtion.
/////////////////////////////////////////////////////////////////////

?>
<head>
    <title>Raspberry Pi GPIO Remote :: GoKEV Pinterface</title>
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
    <link rel="apple-touch-icon" href="images/template/engage.png"/>
</head>
<meta name="viewport" content="width=device-width">
<meta name=“viewport” content="initial-scale=1.0">
<meta name="viewport" content="initial-scale=2.3, user-scalable=no">

<body>
<table border="0" style="font-family: Verdana, Arial, Sans; font-size: 14px">

<?php

foreach( $button as $var => $vals){
	$link_button = "/api.php?";
	foreach( $vals as $valvar => $valval){
		// the following adds all fields to the URL string EXCEPT
		// those with an underscore (like text_label)
		$link_button .= ( preg_match("/_/",$valvar) ? "" : "&" . $valvar . "=" . $valval);
	}

	$iframe_name = "iframe_" . $vals[io];
	$link_read = "/api.php?op=read&io=$vals[io]";
	$link_writeon = "/api.php?op=write&io=$vals[io]&value=1";
	$link_writeoff = "/api.php?op=write&io=$vals[io]&value=0";
	$link_button = "/api.php?op=button&io=$vals[io]&w=$vals[w]&c=$vals[c]";
	$slink_button = "/button/$vals[io]/_/$vals[w]/$vals[c]/";
	$slink_writeon = "/button/$vals[io]/1/$vals[w]/$vals[c]/";
	$slink_writeoff = "/button/$vals[io]/0/$vals[w]/$vals[c]/";

?>
<tr>
	<td align="center" valign="middle">
		<?=$vals[text_label]?>
	</td>
	<td align="left" bgcolor="silver">
		The iframe to the right is calling the BUTTON API CALL on load:
		<pre><b><?=$link_button?></b> or short link: <b><?=$slink_button?></b></pre><br>
		Direct links to API functions, results shown here in the right iframe:<br>
		<a href="<?=$link_read?>" target="<?=$iframe_name?>">READ API</a></br>
		<a href="<?=$link_writeon?>" target="<?=$iframe_name?>">ON API</a></br>
		<a href="<?=$link_writeoff?>" target="<?=$iframe_name?>">OFF API</a></br>
		<a href="<?=$link_button?>" target="<?=$iframe_name?>">BUTTON</a></br>
	</td>
	<td align="center" valign="middle">
		<iframe
			name="<?=$iframe_name?>"
			frameborder="0"
			scrolling="auto"
			src="<?=$link_button?>"
			height="120"
		></iframe>
	</td>
</tr>
</iframe><br>

<?php

}

?>
</table>
</body>
<br>
Take a look at this array below.  You can customize this inside index.php at the top and create different buttons.<br>Try changing the values for <b>text_label</b> or <b>w</b> (width from 1 to 300)<br><br>

And if you just want a basic page, check out the stripped down version of this one, <a href="/index2.php">index2.php</a><br>

<pre><?php print_r($button);?></pre>

