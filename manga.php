<?php
    include_once '_function.php';
    $marumaru = new Marumaru();
    $data = $marumaru->WEBParsing('http://marumaru.in/'.$_GET['href']);
    $data = str_replace('class="con_link" ', 'target="_blank" ', $data);
    $data = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $data);
    $thumb = $marumaru->splits($data, '<meta property="og:image" content="', '"');
    $a = explode('<a target="_blank" href="', $data);
    for($i=1;$i<count($a);$i++)
    {
        $href = explode('/', explode('"', $a[$i])[0])[4];
        $title = str_replace('&nbsp;', ' ', strip_tags('<a target="_blank" href="'.explode('</a>', $a[$i])[0]));
        if(!$title) continue;
        $episodes[] = ['href'=>$href, 'title'=>$title];
    }
    echo json_encode(array('cover'=>$thumb, 'episodes'=>$episodes));