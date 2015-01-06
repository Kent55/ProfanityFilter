<?php
/**
* Profanity Filter Version 1.0.0
* Copyright (C) 2015 Matt Kent
*/

define('IN_PROFANITY', TRUE);
define('DEBUG_MODE', FALSE);

if (DEBUG_MODE) error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

require_once 'class_core.php';

?>
<!doctype html>
<html>
<head>
	<title>Profanity Filter Version <?php echo SCRIPT_V; ?></title>
</head>

<body>
<h2>Profanity Filter Version <?php echo SCRIPT_V; ?></h2>
<br />
<form action="" method="get">
<input value="<?php echo @$_GET['string']; ?>" type="text" name="string" />
<br /><br />
<input type="submit" name="do" value="Filter" />
</form>
<br />

<?php
if (isset($_GET['do']) AND trim($_GET['string']) != '')
{
	$string = urlencode(trim($_GET['string']));
	echo new Profanity($string);
	echo '<hr />';
}
 ?>

</body>
</html>