<?php

error_reporting( error_reporting() & ~E_NOTICE );

$input = clean_input($_REQUEST);
//$content = "<pre>" . print_r($_REQUEST,true) . "</pre>";
//$content .= "<pre>" . print_r($input,true) . "</pre>";
//print "<pre>" . print_r($input,true) . "</pre>\n";



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
	case "buttonrewrite":
		$rewrite = parse_rewrite($_SERVER['REQUEST_URI']);
		$result = make_button($rewrite[1],$rewrite[2],$rewrite[3]);
		$html = $result[button] . "$rewrite[1],$rewrite[2],$rewrite[3]";
		$content = json_encode($result,JSON_PRETTY_PRINT);
		break;
	case "rewrite":
		$rewrite = parse_rewrite($_SERVER['REQUEST_URI']);
		switch ($rewrite[0]) {
			case "button":
				$result = make_button($rewrite[1],$rewrite[2],$rewrite[3]);
				$html = $result[button];
				$content = json_encode($result,JSON_PRETTY_PRINT);
				break;
			case "read":
				$result = gpio_read($rewrite[1]);
				$content = json_encode($result,JSON_PRETTY_PRINT);
				break;
			case "write":
				$result = gpio_write($rewrite[1],$rewrite[2]);
				$content = json_encode($result,JSON_PRETTY_PRINT);
				break;
		}
//		$content = "<pre>" . print_r($rewrite,true) . "</pre>\n";
		break;
	default:
		$content = "nothing to see here";
		break;
}

if ( (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) and ($_REQUEST['op'] != "buttonrewrite") ){
	// we are requesting this exact page
	header('Content-type: text/javascript');
	print $content;
}else{
	// we are requesting a different page and this is included / required
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
function parse_rewrite($in){
	$mid = explode("/",$in);
	foreach( $mid as $var => $val){
		if ( ($val != "") and ($val != "api") ){
			$out[intval($count)]=$val;
			$count = ($count + 1);
		}
	}
	return $out;
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
function gpio_init($io){
	$command =<<<ALLDONE
         gpio mode $io out
ALLDONE;
	return preg_replace("/\s+/","",shell_exec($command));
}

////////////////////////////////////////////////////////////////////////////////
function gpio_getval($io){
	gpio_init($io);
	$command =<<<ALLDONE
         gpio read $io
ALLDONE;
	return preg_replace("/\s+/","",shell_exec($command));
}

////////////////////////////////////////////////////////////////////////////////
function gpio_setval($io,$value){
	gpio_init($io);
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
	global $input;

	if ( isset($input[initval]) ){
		$value = $input[initval];
	}else if ( isset($input[delay]) ){
		print "DELAY $input[delay]";
	}

	$write_result = gpio_write($io,$value);

	$result = gpio_read($io);
	$result[write] = $write_result;

        $result[io] = $io;
        $result[state] = gpio_getval($io);
	$result[led] = ($result[state] == 0 ? "/red_off.png" : "/red_on.png");
	$result[link] = $_SERVER['SCRIPT_NAME'] . "?io=" . $result[io] . "&op=button" . "&value=" . $result[nextstate] . "&w=" . $w;
	$result[link] .= ( isset($input[delay]) ? "&delay=" . $input[delay] : "");

	if ( isset($input[delay]) ){
		$delayms = ($input[delay] * 1000);

	$result[button] =<<<ALLDONE
<script>
function delayBack() {
    setTimeout( function() { history.back(1); }, $delayms );
}
</script>
<a href="$result[link]">
<img src="$result[led]" width="$w" onload="javascript:delayBack()">
</a>
ALLDONE;
	}else{
	$result[button] .=<<<ALLDONE
<a href="$result[link]">
<img src="$result[led]" width="$w">
</a>
ALLDONE;
	}
	return $result;
}
