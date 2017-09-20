<?php
print <<< EOD
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">

<title>isPrimeDrill</title>
<link rel="stylesheet" href="http://tatt.ch/style.css">
<style>
	hr {border-top: 2px solid #B0D4E5;}
</style>
<link rel="stylesheet" href="http://tatt.ch/kuin/prettify/prettify.css" type="text/css">
<script type="text/javascript" src="http://tatt.ch/kuin/prettify/prettify.js"></script>
<script type="text/javascript">
	// <![CDATA[
	(function(){
		function init(event){
			prettyPrint();
		}
		if(window.addEventListener)window.addEventListener("load",init,false);
		else if(window.attachEvent)window.attachEvent("onload",init);
	})();
	// ]]>
</script>
</head>
<body>
<div id="container">
<h1><a href="./isPrimeDrill.js">isPrimeDriill.js</a></h1>
<pre class="prettyprint lang-javascript linenums" style="border-radius:0; margin: 0px -10px 0px -10px;">
EOD;

print htmlspecialchars(file_get_contents("./isPrimeDrill.js"));

print <<<EOD
</pre>
</div>
</body>
</html>
EOD;
?>
