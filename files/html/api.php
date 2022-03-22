<?php

error_reporting( error_reporting() & ~E_NOTICE );

$input = clean_input($_REQUEST);
//$content = "<pre>" . print_r($_REQUEST,true) . "</pre>";
//$content .= "<pre>" . print_r($input,true) . "</pre>";

$io = 9;

switch ($_REQUEST['op']) {
	case "read":
		$result[io] =  $input[io];
		$result = gpio_read($input[io]);
		$content = json_encode($result,JSON_PRETTY_PRINT);
		break;
	case "write":
		$result = gpio_write($input[io],$input[value]);
		$content = json_encode($result,JSON_PRETTY_PRINT);
		break;
	case "switch":
		$result = gpio_switch($input[io]);
		$content = json_encode($result,JSON_PRETTY_PRINT);
		break;
	case "button":
		$result = make_button($input[io],$input[value],$input[w]);
		$html = $result[button];
		$content = json_encode($result,JSON_PRETTY_PRINT);
		break;
	default:
		break;
}


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header('Content-type: text/javascript');
	print $content;
}else{
	// "included/required";
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
<?php print $html ?>

<!--
<pre><?php print_r($result); ?></pre>
-->
</table>
</body>


<?php
}







////////////////////////////////////////////////////////////////////////////////

function clean_input($in){
	foreach( $in as $var => $val){
		$var = preg_replace("/[^a-zA-Z0-9]/","",$var);
		$val = preg_replace("/[^a-zA-Z0-9]/","",$val);
		$out[$var]=$val;
	}
	return $out;
}

////////////////////////////////////////////////////////////////////////////////
function gpio_getval($io){
	$command =<<<ALLDONE
         gpio read $io
ALLDONE;
	return preg_replace("/\s+/","",shell_exec($command));
}

////////////////////////////////////////////////////////////////////////////////
function gpio_setval($io,$value){
	$command =<<<ALLDONE
         gpio write $io $value
ALLDONE;
	return preg_replace("/\s+/","",shell_exec($command));
}

////////////////////////////////////////////////////////////////////////////////
function gpio_read($io){
	$result[io] = $io;
	$result[state] = gpio_getval($io);
	$result[nextstate] = ($result[state] == 1 ? 0 : 1);
	$result[changed] = false;
	return $result;
}

////////////////////////////////////////////////////////////////////////////////
function gpio_write($io,$newstate){
	$statea = gpio_getval($io);
		gpio_setval($io,$newstate);
	$stateb = gpio_getval($io);

	$result[changed] = ($statea == $stateb ? false : true);
	$result[io] = $io;
	$result[state] = gpio_getval($io);
	return $result;
}

////////////////////////////////////////////////////////////////////////////////
function gpio_switch($io){
	$statea = gpio_getval($io);
	$newstate = ($statea == 1 ? 0 : 1);
	gpio_setval($io,$newstate);
	$stateb = gpio_getval($io);

	$result[changed] = ($statea == $stateb ? false : true);
	$result[io] = $io;
	$result[state] = gpio_getval($io);
	$result[nextstate] = ($result[state] == 1 ? 0 : 1);
	return $result;
}

////////////////////////////////////////////////////////////////////////////////

function make_button($io,$value="",$w="150"){
	if ( isset($value) ){
		gpio_write($io,$value);
	}

	$result = gpio_read($io);
        $result[io] = $io;
        $result[state] = gpio_getval($io);
		$result[led] = ($result[state] == 0 ? "red_off.png" : "red_on.png");
	$result[link] = $_SERVER['SCRIPT_NAME'] . "?io=" . $result[io] . "&op=button" . "&value=" . $result[nextstate] . "&w=" . $w;
	$result[button] =<<<ALLDONE
<a href="$result[link]">
<img src="$result[led]" width="$w">
</a>
ALLDONE;

	return $result;
}
