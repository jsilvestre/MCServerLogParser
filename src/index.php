<?php

define('APP_PATH','app');
define('EXT','.php');

require_once('bootstrap.php');

/**
 * Application wrapper
 */

// we get the file requested
if(!empty($_GET['page']))
{
	$page = $_GET['page'];
}
else
{
	$page = 'main';
}

// we look for the requested file in the file system
if(file_exists(APP_PATH.'/'.$page.EXT))
{
	$path = APP_PATH.'/'.$page.EXT;
}
else
{
	header('HTTP/1.0 404 Not Found');
	$path = APP_PATH.'/error/404'.EXT;
}

// buffering the result
ob_start();
	require $path;
    $display = ob_get_contents();
ob_end_clean();

// routing is over, starting displaying the wrapper

?>
<!DOCTYPE html>
<html>
<head>
	<title>Minecraft Server Log Parser</title>
	<meta charset="UTF-8" />
	<meta http-equiv="content-type" content="text/html" />
	<link rel="stylesheet" href="resources/styles/app.css" type="text/css" media="screen" />
</head>
	
<body>

	<div id="header">
		<h1>MC Server Log Parser</h1>
		<a href="index.php?page=overall">Overall stats</a> - 
		<a href="index.php?page=admin">Admin</a>"
	</div>
	
	<div id="contenu">
		<?php echo $display; ?>
	</div>
</body>
</html>