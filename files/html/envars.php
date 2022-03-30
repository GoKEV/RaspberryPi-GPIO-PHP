<?php
$this_script = $_SERVER['SCRIPT_NAME'];
$this_uri = $_SERVER['REQUEST_URI'];

$header = array("one","two","three");

// We cycle through the arrays we want to capture
$cliopt = getopt("abc");
if (!empty($cliopt['a'])) $outvars = cycle_array('$cliopt',$cliopt);
if (!empty($argv)) $outvars .= cycle_array('$argv',$argv);
if (!empty($_ENV)) $outvars .= cycle_array('$_ENV',$_ENV);
if (!empty($_SERVER)) $outvars .= cycle_array('$_SERVER',$_SERVER);
if (!empty($_POST)) $outvars .= cycle_array('$_POST',$_POST);
if (!empty($_GET)) $outvars .= cycle_array('$_GET',$_GET);
if (!empty($_REQUEST)) $outvars .= cycle_array('$_REQUEST',$_REQUEST);

// We build the form to submit
$submit_form = submit_form($this_script,$this_uri);




////////////////////////////////////////////////////////////////////////////////
/*                       All functons and HTML below here.                    */
////////////////////////////////////////////////////////////////////////////////

function submit_form_html($link,$type){

	$out=<<<ALLDONE
	<tr>
		<td colspan="100%">
			<p style="font-size:14px;">Submit variables to <b>$type :</b>

			<a href="$link">$link</a><br>
		</td>
	</tr>
	<tr>
		<td align="right">
			<form method="GET" action="$link">
				get_input: <input type="text" name="get_input"><br>
				<input type="submit" value="SUBMIT GET"></p>
			</form>
		</td>
		<td align="right">
			<form method="POST" action="$link">
				post_input: <input type="text" name="post_input"><br>
				<input type="submit" value="SUBMIT POST"></p>
			</form>
		</td>
	</tr>

ALLDONE;

	return $out;
}

function submit_form($script,$uri){
	$out = "<table class=\"vars\">\n";
	$out .= submit_form_html($script,"\$_SERVER['SCRIPT_NAME']");

	if ($script != $uri){
		$out .= submit_form_html($uri,"\$_SERVER['REQUEST_URI']");
	}

	$out .= "</table>\n";

	return $out;

}




function cycle_array($name,$data){
	$outvars=<<<ALLDONE
	<tr>
		<td colspan="100%" style="background:#000000;">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="100%">Original Array:<h1>$name</h1></td>
	</tr>
	<tr>
		<td>KEY</td>
		<td>VARIABLE NAME</td>
		<td>VALUE</td>
	</tr>
ALLDONE;

	foreach( $data as $key => $value){
		$count++;

		// if an htaccess password was used, we don't want it to show in plaintext here.  Or DO we?!
		if ($key == "PHP_AUTH_PW"){
			$value = preg_replace("[^.*]", "*", $value) . " <--- (this value exists in plaintext but has been obfusticated in this script)";
		}

		// some additional formatting to easily read the elements within the cookie while keeping the string in its original format
		if ($key == "HTTP_COOKIE"){
			$cookie = preg_replace("/;/","\n\n",urldecode($value));
			$cookie = preg_replace("/(\||\&)/"," $1\n",$cookie);
			$value .= "\n\n<pre>$cookie</pre>";
		}

		$dispname = $name . '[\'' . $key . '\']';

$outvars.=<<<ALLDONE
	<tr>
		<td>$count</td>
		<td>$dispname</td>
		<td>$value</td>
	</tr>


ALLDONE;

	}
	return $outvars;
}



?>
<style type="text/css">
FONT,BODY {
	font-family: Verdana,sans,arial,Helvetica;
	font-size: 36px;
}
table.vars {
	border-width: 0px 0px 0px 0px;
	border-spacing: 2px;
	border-style: double double double double;
	border-color: gray gray gray gray;
	border-collapse: separate;
	background-color: white;
}
table.vars th {
	border-width: 1px 1px 1px 1px;
	padding: 3px 3px 3px 3px;
	border-style: groove groove groove groove;
	border-color: gray gray gray gray;
	background-color: white;
	-moz-border-radius: 9px 9px 9px 9px;
}
table.vars td {
	border-width: 1px 1px 1px 1px;
	padding: 5px 5px 5px 5px;
	border-style: groove groove groove groove;
	border-color: gray gray gray gray;
	background-color: white;
	font-size: 12px;
	-moz-border-radius: 9px 9px 9px 9px;
}
table.vars tr {
	height: 25px;
}

</style>

<?=$submit_form?>

<table class="vars">

<br>
<?=$outvars?>
</table>

