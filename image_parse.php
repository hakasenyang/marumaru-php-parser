<?php
	include '_function.php';
	$url = $_GET['url'];
	$marumaru = new Marumaru();

	$a = parse_url($url);
	switch($a['host'])
	{
		case 'www.yuncomics.com':
		case 'blog.yuncomics.com':
		case 'marumaru.in':
			break;
		default:
			exit;
	}

	header('Cache-Control: max-age=86400, public');
	header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
	header('Content-Type: image/jpeg');
	echo $marumaru->WEBParsing($url, NULL, NULL);
?>