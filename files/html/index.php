<?php
error_reporting( error_reporting() & ~E_NOTICE );

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

//  The new TYPE field indicates behavior
//	L0 / L1 = latching switch (the 0 or 1 makes no difference)
//	M05 / M15 = momentary switch with a default value of 0 or 1, 5 second delay
//		A switch with M05 will be OFF... then turn on for 5seconds, then off
//		A switch with M13 will be ON... then turn of for 3seconds, then on

$devices[0][name]="Button One";
$devices[0][type]="L0";
$devices[0][pin]=11;

$devices[1][name]="Button Two";
$devices[1][type]="L1";
$devices[1][pin]=12;

$devices[2][name]="Button Three (Momentary 3 seconds, normally off)";
$devices[2][type]="M03";
$devices[2][pin]=13;

$devices[3][name]="Button Four (Momentary 5 seconds, normally on)";
$devices[3][type]="M15";
$devices[3][pin]=15;

/*
	This is your array of devices.  See a GPIO pinout to make sure you're 
	using the correct wires for GPIO 0,1,2,3 or change these to whatever
	values you use.

	The NAME portion is simply for display.  Change it to what 
	you want displayed on the web interface.  Adding additional
	elements to this array will create more buttons. I tested
	this with TWO GPIO relays and had eight GPIO addresses going.

EXAMPLES FOR ADDING GPIO DEVICES:

	This will add a device for the non-existent GPIO 40
	which would be wired into non-existent pin 45
	and a momentary contact, waiting 999 seconds before
	returning
$devices[40][name]="Some fake button that does nothing";
$devices[40][type]="M1999";
$devices[40][pin]=45;

LETs BREAK THIS DOWN:
$devices[40][type]="M1999";

M 1 999
| |  |___________The delay of a momentary push (from 0 to 999 seconds)
| |__________The second character is the default state. 0off 1on
|________The first letter M says "momentary".  L for "latching"

This value is parsed using substr, so delay is always 3rd (and 4th and 5th) character)

*/









// We get the input string from the URL request.
$io = preg_replace("/[^0-9]/","",$_REQUEST['io']);	// SECURITY:  strip out anything in the io var that isn't a number


// Listen for the input string to decide what we do
switch ($_REQUEST['op']) {
	case "status":
		$content = check_the_status($io);
		break;
	case "moff":
		$type = preg_replace("/[^LM0-9]/","",$_REQUEST['type']);
		$delay = substr($type, 2, 3);
		curl_command($io,"write", "1", $delay);
		$content = check_the_status($io);
		break;
	case "mon":
		$type = preg_replace("/[^LM0-9]/","",$_REQUEST['type']);
		$delay = substr($type, 2, 3);
		curl_command($io,"write", "0", $delay);
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
		$content .= get_list($devices);
		$content .= show_gpio_graphic();
// Comment out the show_gpio_graphic line above to hide the graphic from the web page
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


<?php

////////////////////////////////////////////////////////////////////////////////

function check_the_status($io){
        global $devices;
	$type = $devices[$io][type];
	$status = curl_command($io);
	if ( preg_match("/^M/",$type)){
		$onurl = $_SERVER['SCRIPT_NAME'] . "?io=" . $io . "&type=" . $type . "&op=mon";
		$offurl = $_SERVER['SCRIPT_NAME'] . "?io=" . $io . "&type=" . $type . "&op=moff";
		$led = ($status ? "blue_off.png" : "blue_on.png");
	}else{
		$onurl = $_SERVER['SCRIPT_NAME'] . "?io=" . $io . "&type=" . $type . "&op=on";
		$offurl = $_SERVER['SCRIPT_NAME'] . "?io=" . $io . "&type=" . $type . "&op=off";
		$led = ($status ? "red_off.png" : "red_on.png");
	}

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
	foreach ($devices as $io => $info){
		$name = $info[name];
		$type = $info[type];

		curl_command($io,"mode","out");

		if ( preg_match("/^M/",$type)){
			$newval = substr($type, 1, 1); 
			curl_command($io,"write", $newval);
		}
	}
}

////////////////////////////////////////////////////////////////////////////////

function get_list($devices){
	$out = null;
	foreach ($devices as $io => $info){
		$name = $info[name];
		$pin = $info[pin];
		$type = $info[type];
		$cell = make_cell($io);
		$out .=<<<ALLDONE
	<tr>
		<td>
			$name
			<div style="font-family: Verdana, Arial, Sans; font-size: 8px">	(IO $io / PIN $pin)</div>
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

function curl_command($io,$action="read",$value="",$delay=""){

	if ($delay > 0){
	        $newval = ($value == 1 ? 0 : 1);
		$command =<<<ALLDONE
         gpio $action $io $value ; sleep $delay ; gpio $action $io $newval
ALLDONE;
	}else{

		$command =<<<ALLDONE
         gpio $action $io $value
ALLDONE;
}
	$reply = shell_exec($command);
	$result = preg_replace("/\s+/","",$reply);
	return ($result ? false : true);

}

////////////////////////////////////////////////////////////////////////////////

function make_cell($io){
	global $devices;
	$type = $devices[$io][type];
	$sturl = $_SERVER['SCRIPT_NAME'] . "?io=" . $io . "&type=" . $type . "&op=status";

	$out =<<<ALLDONE
<iframe frameborder="0" width="75" height="75" align="center" valign="mid" src="$sturl"></iframe>
ALLDONE;
	return $out;

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
			<li>Pin 16: Blue (trigger for relay 5 -- GPIO 4)
			<li>Pin 18: Blue (trigger for relay 6 -- GPIO 5)
			<li>Pin 22: Blue (trigger for relay 7 -- GPIO 6)
			<li>Pin 7: Blue (trigger for relay 8 -- GPIO 7)
			<br><br>
			<img src="RaspberryPiPinout.png"><br>
			To remove this graphic and text, comment out the call on line 65:<br>
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

