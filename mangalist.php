<?php
    include_once '_function.php';
    $marumaru = new Marumaru();
    $file = $marumaru->FileRead('mangalist.txt');
    if(!isset($file) || @explode(PHP_EOL, $file)[0] < time())
    {
        $data = $marumaru->WEBParsing('https://marumaru.in/c/53');
        $a = explode('" href="/b/manga/', $data);
        $b = explode('<div width="200"><', $data);
        for($i=1,$cnt=count($a);$i<$cnt;$i++)
        {
            $name = strip_tags('<'.explode('</div>', $b[$i])[0]);
            $id = explode('"', $a[$i])[0];
            $c[] = ['href'=>'/b/manga/'.$id, 'title'=>$name, 'id'=>$id];
        }
        $file = json_encode(array('list'=>$c));
        $marumaru->FileWrite($file, 'mangalist.txt', (60*30));
        echo $file;
        exit;
    }
    echo explode(PHP_EOL, $file)[1];
    exit;
