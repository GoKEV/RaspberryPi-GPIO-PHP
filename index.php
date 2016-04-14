<?php

/*

	Written by Kevin Holmes KEV@GoKEV.com
	Please feel free to use, expand, and enjoy the concept of open source.
	Please feel free to give credit where credit is due.

	This PHP is simply an interface to call the native command-line functions:

	GET STATUS of GPIO 0:		gpio read 0
	TURN VALUE TO 1			gpio write 0 1
	TURN VALUE TO 0			gpio write 0 0

	This script worked for me on Raspian with absolutely no additional packages added
	aside from apache2 and php5 -- using Raspberry Pi Model B+ (the 2012 model)
*/

$devices = array(
	'Button One' => '0',
	'Button Two' => '1',
	'Button Three' => '2',
	'Button Four' => '3',
);

/*
	This is your array of devices.  See a GPIO pinout to make sure you're 
	using the correct wires for GPIO 0,1,2,3 or change these to whatever
	values you use. 

	The text portion is simply for display.  Change it to what you want
	displayed on the web interface.  Adding additional elements to this array
	will create more buttons (provided a unique name is used).  I tested this
	with TWO GPIO relays and added in values for GPIO 10,11,12,and 13.

*/

$io = preg_replace("/[^0-9]/","",$_REQUEST['io']);	// SECURITY:  strip out anything in the io var that isn't a number


// Listen for the input string to decide what we do
switch ($_REQUEST['op']) {
	case "status":
		$content = check_the_status($io);
		break;
	case "off":
		curl_command($io,"write", "1");
		$content = check_the_status($io);
		break;
	case "on":
		curl_command($io,"write", "0");
		$content = check_the_status($io);
		break;
	case "list":
		$content = get_list($devices);
		break;
	default:
		init_gpio($devices);
		$content = get_list($devices);
		$content .= show_gpio_graphic();
// Comment out the show_gpio_graphic line above to hid the graphic from the web page
		break;
}

// Some HTML output with a (hopefully) mobile-friendly option
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
<?php echo $content ?>
</table>
</body>


<?

////////////////////////////////////////////////////////////////////////////////

function check_the_status($io){
	$onurl = $_SERVER['SCRIPT_NAME'] . "?io=" . $io . "&op=on";
	$offurl = $_SERVER['SCRIPT_NAME'] . "?io=" . $io . "&op=off";
	$status = curl_command($io);
	$led = ($status ? "off.png" : "on.png");
	$link = ($status ? $offurl : $onurl);
	return led_link($led,$link);

}

////////////////////////////////////////////////////////////////////////////////

function led_link($led,$link){
	$out =<<<ALLDONE
<a href="$link">
	<img src="$led" width="58">
</a>

ALLDONE;
	return $out;
}

////////////////////////////////////////////////////////////////////////////////

function init_gpio($devices){
	$out = null;
	foreach ($devices as $name => $io){
		curl_command($io,"mode","out");
	}
}

////////////////////////////////////////////////////////////////////////////////

function get_list($devices){
	$out = null;
	foreach ($devices as $name => $io){
		$cell = make_cell($io);
		$out .=<<<ALLDONE
	<tr>
		<td>
			$name 
			<div style="font-family: Verdana, Arial, Sans; font-size: 8px">	(IO $io)</div>
		</td>
		<td>
			$cell
		</td>
	</tr>

ALLDONE;

	}

	return $out;
}

////////////////////////////////////////////////////////////////////////////////

function curl_command($io,$action="read",$value=""){
	$command =<<<ALLDONE
         gpio $action $io $value
ALLDONE;

	$reply = shell_exec($command);
	$result = preg_replace("/\s+/","",$reply);
	return ($result ? false : true);

}

////////////////////////////////////////////////////////////////////////////////

function show_gpio_graphic(){
	$out = <<<ALLDONE

<table>
	<tr>
		<td colspan="100%" bgcolor="white" height="300">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="100%" bgcolor="black" height="5">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="100%">
			In my test setup, I'm using this pinout:<br>
			<li>Pin 2:  Red (power wire to relay block)
			<li>Pin 6:  Black (ground wire to relay block)
			<li>Pin 11: Blue (trigger for relay 1 -- GPIO 0)
			<li>Pin 12: Blue (trigger for relay 2 -- GPIO 1)
			<li>Pin 13: Blue (trigger for relay 3 -- GPIO 2)
			<li>Pin 15: Blue (trigger for relay 4 -- GPIO 3)
			<br><br>
			<img src="RaspberryPiPinout.png"><br>
			To remove this graphic and text, comment out the call on line 60:<br>
			<b>\$out .= show_gpio_graphic();</b><br><br>
			Like this!<br>

			<b>//\$out .= show_gpio_graphic();</b><br>

			<br><br><br>
		</td>
	</tr>
</table>

ALLDONE;
	return $out;
}

////////////////////////////////////////////////////////////////////////////////

function make_cell($io){
	$sturl = $_SERVER['SCRIPT_NAME'] . "?io=" . $io . "&op=status";

	$out =<<<ALLDONE
<iframe frameborder="0" width="75" height="75" align="center" valign="mid" src="$sturl"></iframe>
ALLDONE;
	return $out;

}

