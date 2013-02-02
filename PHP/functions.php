<!DOCTYPE HTML>
<html>

<head>
	<meta charset="UTF-8"/>
	<title>DialogBot v1.0</title>
</head>

<body>

<?php
set_time_limit(0);

$verbs = file('Data/verbs.txt', FILE_IGNORE_NEW_LINES);
function getVerbTemplate($verb){
	 if(is_string($verb)){
		  $dom = new DomDocument();
		  $dom->load('Data/verbs_templates.xml');
		  $infinitives = $dom->getElementsByTagName('i');
		  $templates = $dom->getElementsByTagName('t');
		  foreach($infinitives as $key => $infinitive){
			   $infinitive = $infinitive->nodeValue;
			   if($infinitive == $verb) return $templates->item($key)->nodeValue;
		  }
	 }
	 return "TEMPLATE NOT FOUND";
}

foreach($verbs as $key => $verb){
	 if($key > 200){
		  $template = getVerbTemplate($verb);
		  echo "<v><i>" . $verb . "</i><t>" . $template . "</t></v>" . "\n";
	 }
	 //echo mb_strpos($template, ":", 0, "utf-8") . "<br />";
}

//echo getVerbTemplate("apparaître");

?>

</body>

</html>

