<?php 
$inc = "../setting/".$_GET['zip'].".inc.php";
include($inc); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <?php 
	//echo $_GA;
	
	#Google Web Optimizer Conversion Page Setting
	$_setAccount 		= 'UA-25132646-1';
    $_channel 			= 'Display';
    $_trackPageview 	= '2143806993';
	$_page				= 'conversion'; #conversion
	GWO($_setAccount,$_trackPageview,$_channel,$_page);

	?>
			<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
			<meta name="viewport" content="initial-scale=1.0; maximum-scale=1.5;">
			<link href="../styles/main30f4.css?v=3" rel="stylesheet" media="screen" type="text/css" />
			<link href="../styles/retina.html" rel="stylesheet" media="only screen and (-webkit-min-device-pixel-ratio: 2)" type="text/css" />

			<title>Thank You For Your Interest in <?php echo $_TITLE; ?></title>
    <?php 
	//echo $_GA;
	?>
		</head>
		
		<body>
			
		<div id="header">
		</div>
		
<div id="top2"></div>
<div id="thanks"></div>
<div id="links"><a href="http://www.nu.edu/"></a></div>
<div id="contact">
Have a question?<br />
Call us: <a href="tel:18006288648">1-800-628-8648</a><br />
Email us: <a href="mailto:advisor@nu.edu">advisor@nu.edu</a><br />
</div>	

<?php 
//echo $_CONVERSION;
?>	
	
<?php 
	echo $_BRIGHTTAG;
?>
</body>
</html>
		