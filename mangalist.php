<?php
    include_once '_function.php';
    $marumaru = new Marumaru();
    $file = $marumaru->FileRead('mangalist.txt');
    if(!$file || explode(PHP_EOL, $file)[0] < time())
    {
        $data = $marumaru->WEBParsing('http://marumaru.in/c/1');
        $a = explode('" href="/b/manga/', $data);
        $b = explode('<div width="200"><', $data);
        for($i=1;$i<count($a);$i++)
        {
            $name = strip_tags('<'.explode('</div>', $b[$i])[0]);
            $id = explode('"', $a[$i])[0];
            $c[] = ['href'=>'/b/manga/'.$id, 'title'=>$name, 'id'=>$id];
        }
        $marumaru->FileWrite(json_encode(array('list'=>$c)), 'mangalist.txt');
        $file = $marumaru->FileRead('mangalist.txt');
    }
    echo explode(PHP_EOL, $file)[1];
    exit;