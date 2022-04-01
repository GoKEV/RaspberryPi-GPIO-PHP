<?php

error_reporting( error_reporting() & ~E_NOTICE );

$input = clean_input($_REQUEST);

//print "<pre>" . print_r($input,true) . "</pre>\n";
//exit;

switch ($_REQUEST['op']) {
	case "read":
		$json = 1;
		$result = gpio_read($input[io]);
		$content = json_encode($result,JSON_PRETTY_PRINT);
	break;
	case "write":
		$json = 1;
		$result = gpio_write($input[io],$input[value]);
		$content = json_encode($result,JSON_PRETTY_PRINT);
	break;
	case "switch":
		$json = 1;
		$result = gpio_switch($input[io]);
		$content = json_encode($result,JSON_PRETTY_PRINT);
	break;
	case "button":
		$result = make_button();
		$html = ($result[rewrite] == 1 ? $result[rwbutton] : $result[button]);
		$content = json_encode($result,JSON_PRETTY_PRINT);
		$json = $result[json];
	break;
	default:
		$content = "nothing to see here";
	break;
}

if ( $json == 1 ){
	header('Content-type: text/javascript');
	print $content;
}else{
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
function parse_rewrite($in,$input){
	$mid = explode("/",$in);
	foreach( $mid as $var => $val){
		if ( ($val != "") and ($val != "api") ){
			$getout[intval($count)]=$val;
			$count = ($count + 1);
		}
	}


	if ( isset($getout[0]) and isset($getout[1]) ){
		$out[op] = $getout[0];
		$out[io] = $getout[1];
		$out[value] = $getout[2];
		$out[w] = $getout[3];
		$out[c] = $getout[4];
		$out[rewrite] = 1;
	}else{
		$out[rewrite] = 0;
	}
	return $out;


}

////////////////////////////////////////////////////////////////////////////////
function clean_input($in){

	if ( (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) and ($in['op'] != "buttonrewrite") ){
		$out[phpself] = 1;
	}else{
		$out[phpself] = 1;
	}

	$rwvars = explode("/",$_SERVER['REQUEST_URI']);
	foreach( $rwvars as $rwvar => $rwval){
		if ( ($rwval != "") and ($rwval != "api") ){
			$rwout[intval($count)]=$rwval;
			$count = ($count + 1);
		}
	}

	foreach( $in as $var => $val){
		$var = preg_replace("/[^a-zA-Z0-9]/","",$var);
		$val = preg_replace("/[^a-zA-Z0-9]/","",$val);
		$out[$var]=$val;
	}

	if ( (isset($rwout[0])) and (isset($rwout[1])) ){
		$out[op] = $rwout[0];
		$out[io] = $rwout[1];
		$out[value] = $rwout[2];
		$out[w] = $rwout[3];
		$out[c] = $rwout[4];
		$out[json] = $rwout[5];
		$out[rewrite] = 1;
	}else{
		$out[rewrite] = 0;
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
function make_button(){
	global $input;

	if ( isset($input[initval]) ){
		$value = $input[initval];
	}else if ( isset($input[delay]) ){
		print "DELAY $input[delay]";
	}

	$write_result = gpio_write($input[io],$input[value]);

	$result = gpio_read($input[io]);
	$result[write] = $write_result;

	$result[phpself] = $input[phpself];
	$result[io] = $input[io];
        $result[state] = gpio_getval($input[io]);
	$result[c] = $input[c];
	$result[color] = ( isset($input[c]) ? $input[c] : "red" );
	$result[led] = ($result[state] == 0 ? "/images/" . $result[color] . "_off.png" : "/images/" . $result[color] . "_on.png");
	$result[link] = $_SERVER['SCRIPT_NAME'] . "?io=" . $result[io] . "&op=button" . "&value=" . $result[nextstate] . "&w=" . $input[w] . "&c=" . $result[color];
	$result[rwlink] = "/button/" . $result[io] . "/" . $result[nextstate] . "/" . $input[w] . "/" . $result[color] . "/";

	$result[json] = $input[json];
	$result[rewrite] = $input[rewrite];

	$result[button] .=<<<ALLDONE
<a href="$result[link]">
<img src="$result[led]" width="$input[w]">
</a>
ALLDONE;

	$result[rwbutton] =<<<ALLDONE
<a href="$result[rwlink]">
<img src="$result[led]" width="$input[w]">
</a>
ALLDONE;

	return $result;
}
