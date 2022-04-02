<?php

error_reporting( error_reporting() & ~E_NOTICE );

$input = clean_input($_REQUEST);

//print "<pre>" . print_r($input,true) . "</pre>\n";
//exit;

switch ($_REQUEST['op']) {
	case "api":
		$json = 0;
		$html = "Here's an API guide<br>";
		$html .= "<pre>" . file_get_contents("api_doc.txt") . "</pre>\n";
		$html .= "That was an API guide<br>";
	break;
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
		$json = $input[json];
	break;
	default:
		$content = "nothing to see here";
	break;
}

if ( $json > 0 ){
	header('Content-type: text/javascript');
	print $content;
}else{
?>
<?php print $html ?>

<!--
<pre><?php print_r($result); ?></pre>
-->

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
		$out[value] = ( preg_match("/[0-1]/",$rwout[2] ) ? $rwout[2] : "" );
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
	$result[changed] = false;
	$result[io] = $io;
	$result[state] = gpio_getval($io);
	$result[nextstate] = ($result[state] == 1 ? 0 : 1);
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
	$result[nextstate] = ($result[state] == 1 ? 0 : 1);
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
	$messages[function_name] = "make_button";

	$write_result  = gpio_write($input[io],$input[value]);
	$result = gpio_read($input[io]);

	$result[changed] = $write_result[changed];
	$result[nextstate] = $write_result[nextstate];

	$result[io] = $input[io];
        $result[state] = gpio_getval($input[io]);
	$result[c] = $input[c];

	$led_on = "/images/" . $result[c] . "_on.png";
	$led_off = "/images/" . $result[c] . "_off.png";

	$led_on_file = $_SERVER['DOCUMENT_ROOT'] . $led_on;
	$led_off_file = $_SERVER['DOCUMENT_ROOT'] . $led_off;


	if ( (! file_exists($led_on_file)) or (! file_exists($led_off_file))){
		$messages[defaultcolor] = "green";
		$messages[color_error] = "$messages[defaultcolor] was chosen because files were not found for $led_on_file or $led_off_file";
		$result[c] = $messages[defaultcolor];
		$led_on = "/images/" . $result[c] . "_on.png";
		$led_off = "/images/" . $result[c] . "_off.png";
	}



	$result[led] = ($result[state] == 0 ? $led_off : $led_on);
	$result[link] = $_SERVER['SCRIPT_NAME'] . "?io=" . $result[io] . "&op=button" . "&value=" . $result[nextstate] . "&w=" . $input[w] . "&c=" . $result[c];
	$result[rwlink] = "/button/" . $result[io] . "/" . $result[nextstate] . "/" . $input[w] . "/" . $result[c] . "/";

	$result[rewrite] = $input[rewrite];
	if ($input[value] != ""){ $result[value] = $input[value];}

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

	if ($input[json] > 2){
		$result[request_uri] = $_SERVER['REQUEST_URI'];
		$result[path_info] = $_SERVER['PATH_INFO'];
		$result[query_string] = $_SERVER['QUERY_STRING'];
		$result[input] = $input;
		$result[write_result] = $write_result;
		$result[base_url] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
		$result[full_link] = $result[base_url] . $result[link];
		$result[full_rwlink] = $result[base_url] . $result[rwlink];
		$result[full_button] .=<<<ALLDONE
<a href="$result[full_link]">
<img src="$result[led]" width="$input[w]">
</a>
ALLDONE;

		$result[full_rwbutton] =<<<ALLDONE
<a href="$result[full_rwlink]">
<img src="$result[led]" width="$input[w]">
</a>
ALLDONE;
	}
	if ($input[json] > 1){
		$result[messages] = $messages;
	}

	return $result;
}
