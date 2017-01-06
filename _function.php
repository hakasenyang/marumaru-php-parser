<?php
    class Marumaru {
        private $httph = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36';
        private $fname = 'cookie.txt';
        public function splits($data, $first, $end, $num = 1)
        {
            $temp = @explode($first, $data);
            $temp = @explode($end, $temp[$num]);
            $temp = $temp[0];
            return $temp;
        }
        public function WEBParsing($url, $cookie=NULL, $headershow=TRUE, $postparam=NULL, $otherheader=NULL)
        {
            $ch = curl_init();
            $opts = array(CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_HEADER => $headershow,
                CURLOPT_USERAGENT => $this->httph
                );
            curl_setopt_array($ch, $opts);
            if ($otherheader) curl_setopt($ch, CURLOPT_HTTPHEADER, $otherheader);
            if ($cookie) curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            if ($postparam)
            {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postparam);
            }

            $data = curl_exec($ch);
            curl_close($ch);
            if ($curl_errno > 0)
                $this->ErrorEcho(14, 'Connect Error!!!');
            return ($data) ? $data : false;
        }
        public function FileRead($filename=NULL)
        {
            $filename = ($filename) ? $filename : $this->fname;
            if(!is_file($filename)) return false;
            if(!filesize($filename)) return false;
            $fp = fopen($filename, 'r');
            $data = fread($fp, filesize($filename));
            fclose($fp);
            return $data;
        }
        public function FileWrite($str,$filename=NULL,$time=(60*30))
        {
            $filename = ($filename) ? $filename : $this->fname;
            $fp = fopen($filename, 'w');
            // 30분
            fwrite($fp, time()+$time.PHP_EOL.$str);
            fclose($fp);
        }
        public function GetCookie()
        {
            $data = $this->WEBParsing('http://wasabisyrup.com/archives/455742', $cookie, true, 'pass=qndxkr',
                array(
                    'Referer: http://wasabisyrup.com/'
                )
            );
            $cookie = $this->splits($data, 'Set-Cookie: PHPSESSID=', ';');
            $cookie = 'PHPSESSID=' . $cookie . ';';
            if(!$cookie) return false; else     return $cookie;
        }
        public function ErrorEcho($num,$err=NULL)
        {
            if($err)
                die(json_encode(array('error'=>$num, 'message'=>$err)));
            else
            {
                switch($num)
                {
                    case 0:
                        $err='Connect Error (wasabisyrup 403 or other error)';
                        break;
                    case 1:
                        $err='Cookie Send Error (Not applied sucuri cookie data)';
                        break;
                    case 2:
                        $err='Cookie Get Error';
                        break;
                    case 3:
                        $err='Password Error (Protected archive) - Retry 10 minutes after view or retry about 3 times';
                        break;
                    case 4:
                        $err='Not found comics data';
                        break;
                    default:
                        $err='Unknown Error. Please send me an e-mail (contact@hakase.kr)';
                }
                die(json_encode(array('error'=>$num, 'message'=>$err)));
            }
        }
    }