<?php


$button[0] = array(
	"text_label"=>"This is IO 0",
	"io"=>"0",
	"op"=>"button",
	"w"=>"50"
);

$button[1] = array(
	"text_label"=>"This is IO 1",
	"io"=>"1",
	"op"=>"button",
	"w"=>"50"
);

$button[2] = array(
	"text_label"=>"Momentary 3",
	"io"=>"2",
	"op"=>"button",
//	"initval"=>"0",
//	"delay"=>"5",
	"w"=>"50"
);

$button[3] = array(
	"text_label"=>"This is IO 3",
	"io"=>"3",
	"op"=>"button",
//	"initval"=>"1",
	"w"=>"50"
);



?>
<table border="1" valign="top" cellpadding="5">
<?php

foreach( $button as $var => $vals){
	$url = "/aindex.php?";
	foreach( $vals as $valvar => $valval){
		// this adds all fields to the URL string EXCEPT those with an underscore (like text_label)
		$url .= ( preg_match("/_/",$valvar) ? "" : "&" . $valvar . "=" . $valval);
	}



?>
<tr>
	<td align="center" valign="middle">
		<?=$vals[text_label]?>
	</td>
	<td align="center" valign="middle">
		<iframe
			frameborder="0"
			scrolling="no"
			src="<?=$url?>"
			width="<?=($vals[w] + 10)?>"
			height="<?=($vals[w] + 10)?>"
		></iframe>
	</td>
	<td align="left" bgcolor="pink">
		<pre><?=$url?></pre>
	</td>
</tr>
</iframe><br>
<?php

}
