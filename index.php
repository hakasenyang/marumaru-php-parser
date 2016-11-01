<?php
	include_once '_function.php';
	$marumaru = new Marumaru();
	$num = $_GET['num'];
	if(!is_numeric($num) || strpos($num, '.'))
	{
?>
<!doctype html><html><head><title>Yuncomics(marumaru.in) Image URL Parser API</title><meta charset="UTF-8">
<style>body img { width: auto; height: auto; max-width: 100%;
}</style>
<link rel="shortcut icon" href="favicon.ico">
</head><body>
<h1>Yuncomics(marumaru.in) <u>Image URL</u> Parser API</h1>
<p>Using: /api/{yuncomics number}/{1 or 0(null)}<br>
Use only application/web/other developers.</p>
<h2>Example</h2>
<p>no json - <a href="/api/553645" target="_blank">/api/553645 (Gochuumon wa Usagi desuka?)</a><br>
json - <a href="/api/553645/1" target="_blank">/api/553645/1 (Gochuumon wa Usagi desuka?)</a><br>
<a href="https://github.com/fmaru/fmaru" rel="noreferrer" target="_blank">fmaru</a> php porting by hakase - <a href="/fmaru" target="_blank">/fmaru</a><br>
image direct view - <a href="/img/553645" target="_blank">/img/553645 (Gochuumon wa Usagi desuka?)</a></p>
<h2>JSON Type</h2>
<p>title : Manga Subject<br>
url : Image URL<br>
explorer : Another same cartoon episode (same episode will be ignored) - id => name (if not data, show null value)<br>
prevnext : Next Episode / Prev Episode (if not data, show null value)<br>
- prev : id => name (or NULL)<br>
- next : id => name (or NULL)</p>
<h2>String Type</h2>
<p>First Line : Manga Subject<br>
Other Line : Image URL</p>
<h2>Error Message</h2>
<p>Number 0 : Connect Error (yuncomics 403 or other error)<br>
Number 1 : Cookie Send Error (Not applied sucuri cookie data)<br>
Number 2 : Cookie Get Error<br>
Number 3 : Password Error (Protected archive) - Retry 10 minutes after view or retry about 3 times)<br>
Number 4 : Not found comics data<br>
Other error : read the message</p>
<p>Output only JSON (Example) {"error":1,"message":"Error Message)"}<br>
Only error / message method use</p>
<h2>Etc...</h2>
<p><strong><i><u>No Open Source. - I just not want.<br>오픈 소스 계획 없음. - 그냥 하기 싫어.</u></i></strong></p>
<p>Developed by <a href="https://keybase.io/hakasekr" rel="noreferrer" target="_blank">Hakase</a> (contact@hakase.kr)<br>
사용은 자유고 제한 없음. 애초에 제한 있으면 API 인증키를 넣겠지만 귀찮아서 안 넣음.
</p>
</body>
</html>
<?php
		exit;
	}

cookieget:
	$dd = $marumaru->FileRead();
	if(!$dd || explode(PHP_EOL, $dd)[0] < time())
	{
		$cookie = $marumaru->GetCookie();
		if(!$cookie) $marumaru->ErrorEcho(2);
		$marumaru->FileWrite($cookie);
	}
	else
		$cookie = explode(PHP_EOL, $dd)[1];

startdata:
	$caches++;
	$data = $marumaru->WEBParsing('http://www.yuncomics.com/archives/'.$num, $cookie);
	/*if(stripos($data, 'HTTP/1.1 301 Moved Permanently') !== false)
	{
		$num = explode('/', $marumaru->splits($data, 'Location: ', PHP_EOL))[4];
		$data = $marumaru->WEBParsing('http://www.yuncomics.com/archives/'.$num, $cookie.$cookie2);
	}*/
	if(stripos($data, 'HTTP/1.1 404 Not Found') !== false || stripos($data, 'HTTP/1.1 301 Moved Permanently') !== false) $marumaru->ErrorEcho(4);
	if(stripos($data, 'HTTP/1.1 200 OK') === false) $marumaru->ErrorEcho(0);
	if(stripos($data, 'This content is password protected.') !== false)
		if($caches > 5) $marumaru->ErrorEcho(3);
		else goto startdata;
	if(stripos($data, 'You are being redirected...') !== false)
		if($caches > 2) $marumaru->ErrorEcho(1);
		else goto cookieget;

	$jsonon = ($_GET['json'] == 1) ? true : false;
	$aaa = explode('data-src="', $data);
	$title = $marumaru->splits($aaa[0], '<title>', '</title>');
	$title = trim(explode(' | ', $title)[0]);
	$data2 = explode('<option value="', str_replace(' selected>', '>', $data));

	for($i=1;$i<count($data2);$i++)
	{
		if($num == trim(explode('">', $data2[$i])[0]))
		{
			if($i != count($data2) - 1)
			{
				$nextid = trim(explode('">', $data2[$i+1])[0]);
				$nextname = trim(explode('</option>', explode('">', $data2[$i+1])[1])[0]);
			}
			if($i != 1)
			{
				$previd = trim(explode('">', $data2[$i-1])[0]);
				$prevname = trim(explode('</option>', explode('">', $data2[$i-1])[1])[0]);
			}
			continue;
		}
		$aac[] = [trim(explode('">', $data2[$i])[0]) => trim(explode('</option>', explode('">', $data2[$i])[1])[0])];
	}
	if($previd || $nextid)
	{
		if($previd) $aad[] = ['prev'=>[$previd=>$prevname]]; else $aad[] = ['prev'=>null];
		if($nextid) $aad[] = ['next'=>[$nextid=>$nextname]]; else $aad[] = ['next'=>null];
	}
	for($i=1;$i<count($aaa);$i++)
		$aab[] = trim(explode('"', $aaa[$i])[0]);

	if($jsonon)
	{
		$aaaa = array('title'=>$title, 'url'=>$aab, 'explorer'=>$aac, 'prevnext'=>$aad);
		echo json_encode($aaaa);
	}
	else
		echo $title.PHP_EOL.implode(PHP_EOL, $aab);


	exit;