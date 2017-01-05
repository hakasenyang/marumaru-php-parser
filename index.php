<?php
    include_once '_function.php';
    $marumaru = new Marumaru();
    $num = $_GET['num'];
    $image = $_GET['image'];
    if(!isset($num))
    {
?>
<!doctype html><html><head><title>wasabisyrup(marumaru.in) Image URL Parser API</title><meta charset="UTF-8">
<style>body img { width: auto; height: auto; max-width: 100%;
}</style>
<link rel="shortcut icon" href="favicon.ico">
</head><body>
<h1>wasabisyrup(marumaru.in) <u>Image URL</u> Parser API</h1>
<p>Using: /api/{wasabisyrup number}/{1 or 0(null)}<br>
Use only application/web/other developers.</p>
<h2>Example</h2>
<p>no json - <a href="/api/93" target="_blank">/api/93 (Himouto Umaru-Chan)</a><br>
json - <a href="/api/93/1" target="_blank">/api/93/1 (Himouto Umaru-Chan?)</a><br>
<a href="https://github.com/fmaru/fmaru" rel="noreferrer" target="_blank">fmaru</a> php porting by hakase - <a href="/fmaru" target="_blank">/fmaru</a><br>
image direct view - <a href="/img/93" target="_blank">/img/93 (Himouto Umaru-Chan)</a></p>
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
<p>Number 0 : Connect Error (wasabisyrup 403 or other error)<br>
Number 1 : Cookie Send Error (Not applied sucuri cookie data)<br>
Number 2 : Cookie Get Error<br>
Number 3 : Password Error (Protected archive) - Retry 10 minutes after view or retry about 3 times)<br>
Number 4 : Not found comics data<br>
Other error : read the message</p>
<p>Output only JSON (Example) {"error":1,"message":"Error Message)"}<br>
Only error / message method use</p>
<h2>Etc...</h2>
<p><strong><i><u><a href="https://github.com/hakasenyang/marumaru-php-parser" target="_blank">Open Source</a></u></i></strong></p>
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
    if(!$dd || explode(PHP_EOL, $dd)[0] < time() || $tmp > 1 || $tmp2 > 3)
    {
        $cookie = $marumaru->GetCookie();
        if(!$cookie) $marumaru->ErrorEcho(2);
        $marumaru->FileWrite($cookie);
    }
    else
        $cookie = explode(PHP_EOL, $dd)[1];

startdata:
    $caches++;
    $data = $marumaru->WEBParsing('http://wasabisyrup.com/archives/'.$num, $cookie);
    /*if(stripos($data, 'HTTP/1.1 301 Moved Permanently') !== false)
    {
        $num = explode('/', $marumaru->splits($data, 'Location: ', PHP_EOL))[4];
        $data = $marumaru->WEBParsing('http://www.yuncomics.com/archives/'.$num, $cookie.$cookie2);
    }*/
    if(stripos($data, 'HTTP/1.1 404 Not Found') !== false ||
       stripos($data, 'HTTP/1.1 301 Moved Permanently') !== false)
        $marumaru->ErrorEcho(4);
    if(stripos($data, 'HTTP/1.1 200 OK') === false)
        $marumaru->ErrorEcho(0);
    if(stripos($data, '<h2>Protected</h2>') !== false)
        if($caches > 5)
            $marumaru->ErrorEcho(3);
        else
        {
            $tmp2++;
            goto startdata;
        }
    /*if(stripos($data, 'You are being redirected...') !== false)
        if($caches > 2)
            $marumaru->ErrorEcho(1);
        else
        {
            $tmp++;
            goto cookieget;
        }*/

    $jsonon = ($_GET['json'] == 1) ? true : false;
    $aaa = explode('data-src="', $data);
    $title = $marumaru->splits($aaa[0], '<title>', '</title>');
    $title = trim(explode(' | ', $title)[0]);
    $data2 = $marumaru->splits($data, '<select class="list-articles select-js-inline select-js-nofocus select-js-inline-right">', '</select>');
    $data2 = explode('<option value="', str_replace('selected>', '>', $data2));
    $data2 = str_replace(array("\t", PHP_EOL), NULL, $data2);

    if ($image)
    {
        $jsonon = ($_GET['json'] == 1) ? true : false;
        $aaa = explode('data-src="', str_replace('data-src="/storage/', 'data-src="http://wasabisyrup.com/storage/', $data));

        for($i=1;$i<count($aaa);$i++)
            echo '<img src="'.trim(explode('"', $aaa[$i])[0]).'"><br>';
    }
    else
    {
        for($i=1;$i<count($data2);$i++)
        {
            if($num == trim(explode('" >', $data2[$i])[0]))
            {
                if($i != count($data2) - 1)
                {
                    $nextid = trim(explode('" >', $data2[$i+1])[0]);
                    $nextname = trim(explode('</option>', explode('" >', $data2[$i+1])[1])[0]);
                }
                if($i != 1)
                {
                    $previd = trim(explode('" >', $data2[$i-1])[0]);
                    $prevname = trim(explode('</option>', explode('" >', $data2[$i-1])[1])[0]);
                }
                continue;
            }
            $aac[] = [trim(explode('" >', $data2[$i])[0]) => trim(explode('</option>', explode('" >', $data2[$i])[1])[0])];
        }
        if($previd || $nextid)
        {
            if($previd)
                $aad[] = ['prev'=>[$previd=>$prevname]];
            else
                $aad[] = ['prev'=>null];
            if($nextid)
                $aad[] = ['next'=>[$nextid=>$nextname]];
            else
                $aad[] = ['next'=>null];
        }
        for($i=1;$i<count($aaa);$i++)
            $aab[] = trim(explode('"', $aaa[$i])[0]);

        if($jsonon)
        {
            $aaaa = array('title'=>$title,
                          'url'=>$aab,
                          'explorer'=>$aac,
                          'prevnext'=>$aad);
            echo json_encode($aaaa);
        }
        else
            echo $title.PHP_EOL.implode(PHP_EOL, $aab);
    }