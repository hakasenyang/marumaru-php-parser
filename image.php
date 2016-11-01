<?php
    include_once '_function.php';
    $marumaru = new Marumaru();
    $num = $_GET['num'];
    if(!is_numeric($num) || strpos($num, '.'))
    {
?>
<!doctype html><html><head><title>Yuncomics(marumaru.in) Image URL Parser</title><meta charset="UTF-8">
<style>body img { width: auto; height: auto; max-width: 100%;
}</style>
<link rel="shortcut icon" href="favicon.ico">
</head><body>
<p><strong>Yuncomics(marumaru.in) <u>Image URL</u> Parser</strong><br>Using: /api/{yuncomics number}/{1 or 0(null)}<br>
Use only application/web/other developers.</p>
<p><strong>Example</strong><br>
no json - <a href="/api/553645" target="_blank">/api/553645 (Gochuumon wa Usagi desuka?)</a><br>
json - <a href="/api/553645/1" target="_blank">/api/553645/1 (Gochuumon wa Usagi desuka?)</a></p>
<p><strong>JSON Type</strong><br>
Title : Manga Subject<br>
URL : Image URL<br>
Explorer : Another same cartoon episode (same episode will be ignored) - ID => Name (if not data, replaced with a null value)<br>
PrevNext : Next Episode / Prev Episode - ID => Name (if not data, replaced with a null value)<br>
- Prev : ID => Name (or NULL)<br>
- Next : ID => Name (or NULL)</p>
<p><strong>String Type</strong><br>
First Line : Manga Subject<br>
Other Line : Image URL</p>
<p><strong>Error Message View</strong><br>
Number 0 : Connect Error (yuncomics 403 or other error)<br>
Number 1 : Cookie Send Error (Not applied sucuri cookie data)<br>
Number 2 : Cookie Get Error<br>
Number 3 : Password Error (Protected archive) - Retry 10 minutes after view or retry about 3 times)<br>
Number 4 : Not found comics data<br>
Other error : read the message</p>
<p>Output only JSON (Example) {"Error":1,"Message":"Error Message)"}<br>
Only Error / Message Method use</p>
<p>Domain : <strong>marumaru.hakase.kr</strong></p>
<p><strong><i><u>No Open Source. - I just not want.<br>오픈 소스 계획 없음. - 그냥 하기 싫어.</u></i></strong></p>
<p>Developed by Hakase (contact@hakase.kr)<br>
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

    for($i=1;$i<count($aaa);$i++)
        echo '<img src="'.trim(explode('"', $aaa[$i])[0]).'"><br>';
