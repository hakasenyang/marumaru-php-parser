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
        /**
         * sucuri - sucuri 프록시 관련 쿠키 우회
         * 소스 : https://github.com/organization/cloudflare-bypass
         * 소스 : http://cafe.naver.com/gogoomas/337647
         * @param  string $result 쿠키 string 입력
         * @return strring         쿠키 데이터 출력
         * 사용하지 않음.
         */
        /*public function sucuri($result)
        {
            if(strpos($result, 'sucuri_cloudproxy_js') !== false)
            {
                $cp_temp1 = explode('S=\'', $result);
                $cp_temp2 = explode(';', $cp_temp1[1]);
                $cp_script = $cp_temp2[0];
                $cp_temp1 = explode('A=\'', $result);
                $cp_temp2 = explode('\';', $cp_temp1[1]);
                $cp_chlist = $cp_temp2[0];
                $cp_charr = array();
                for($i = 0; $i < 64; $i++)
                    $cp_charr[$cp_chlist[$i]] = $i;
                $cp_len = strlen($cp_script);
                $cp_c = 0;
                $cp_u = 0;
                $cp_i = 0;
                $cp_l = 0;
                $cp_a = NULL;
                $cp_r = NULL;
                for($i = 0; $i < $cp_len; $i++)
                {
                    $cp_c = $cp_charr[$cp_script[$i]];
                    $cp_u = ($cp_u << 6) + $cp_c;
                    $cp_l += 6;
                    while($cp_l >= 8)
                        (($cp_a = ($cp_u >> ($cp_l -= 8)) & 0xff) || ($cp_i < ($cp_len - 2))) && ($cp_r .= chr($cp_a));
                }
                $cp_temp1 = explode('document.cookie=', $cp_r);
                $cp_temp2 = explode('=', $cp_temp1[1]);
                $cp_cnam = str_replace('"', '\'', $cp_temp2[0]);
                $cp_cnam_split = explode('+', $cp_cnam);
                $cp_cnam_split_cnt = count($cp_cnam_split);
                $cp_cnam_string = NULL;
                for($i = 0 ; $i < $cp_cnam_split_cnt; $i++)
                {
                    $cp_cnam = trim($cp_cnam_split[$i]);
                    if(strpos($cp_cnam, 'slice') !== false)
                    {
                        $cp_temp1 = explode('\'', $cp_cnam);
                        $cp_temp2 = explode('(', $cp_cnam);
                        $cp_temp3 = explode(')', $cp_temp2[1]);
                        $cp_temp4 = explode(',', $cp_temp3[0]);
                        $cp_cnam_string .= substr($cp_temp1[1], trim($cp_temp4[0]), (intval(trim($cp_temp4[1])-trim($cp_temp4[0]))));
                    }
                    elseif(strpos($cp_cnam, 'charAt') !== false)
                    {
                        $cp_temp1 = explode('\'', $cp_cnam);
                        $cp_temp2 = explode('(', $cp_cnam);
                        $cp_temp3 = explode(')', $cp_temp2[1]);
                        $cp_cnam_string .= substr($cp_temp1[1], trim($cp_temp3[0]), 1);
                    }
                    elseif(strpos($cp_cnam, 'String.fromCharCode') !== false)
                    {
                        $cp_temp1 = explode('(', $cp_cnam);
                        $cp_temp2 = explode(')', $cp_temp1[1]);
                        if(strpos($cp_temp2[0], '0x') !== false)
                            $cp_cnam_string .= chr(hexdec($cp_temp2[0]));
                        else
                            $cp_cnam_string .= chr($cp_temp2[0]);
                    }
                    elseif(strpos($cp_cnam, 'substr') !== false)
                    {
                        $cp_temp1 = explode('\'', $cp_cnam);
                        $cp_temp2 = explode('(', $cp_cnam);
                        $cp_temp3 = explode(')', $cp_temp2[1]);
                        $cp_temp4 = explode(',', $cp_temp3[0]);
                        $cp_cnam_string .= substr($cp_temp1[1], trim($cp_temp4[0]), trim($cp_temp4[1]));
                    }
                    else
                        $cp_cnam_string .= trim(trim($cp_cnam, '\''));
                    $cp_cnam_string .= NULL;
                }
                //$cp_temp1 = explode('=', $cp_r);
                $cp_temp1 = strpos($cp_r, '=');
                $cp_temp2 = explode(';document.cookie', substr($cp_r, $cp_temp1+1)); //$cp_temp[1]
                $cp_cval = str_replace('"', '\'', $cp_temp2[0]);
                $cp_cval_split = explode('+', $cp_cval);
                $cp_cval_split_cnt = count($cp_cval_split);
                $cp_cval_string = null;
                for($i = 0 ; $i < $cp_cval_split_cnt; $i++)
                {
                    $cp_nval = trim($cp_cval_split[$i]);
                    if(strpos($cp_nval, 'slice') !== false)
                    {
                        $cp_temp1 = explode('\'', $cp_nval);
                        $cp_temp2 = explode('(', $cp_nval);
                        $cp_temp3 = explode(')', $cp_temp2[1]);
                        $cp_temp4 = explode(',', $cp_temp3[0]);
                        $cp_cval_string .= substr($cp_temp1[1], trim($cp_temp4[0]), (intval(trim($cp_temp4[1])-trim($cp_temp4[0]))));
                    }
                    elseif(strpos($cp_nval, 'charAt') !== false)
                    {
                        $cp_temp1 = explode("'", $cp_nval);
                        $cp_temp2 = explode("(", $cp_nval);
                        $cp_temp3 = explode(")", $cp_temp2[1]);
                        $cp_cval_string .= substr($cp_temp1[1], trim($cp_temp3[0]), 1);
                    }
                    elseif(strpos($cp_nval, 'String.fromCharCode') !== false)
                    {
                        $cp_temp1 = explode('(', $cp_nval);
                        $cp_temp2 = explode(')', $cp_temp1[1]);
                        if(strpos($cp_temp2[0], '0x') !== false)
                        {
                            $cp_cval_string .= chr(hexdec($cp_temp2[0]));
                        }
                        else
                        {
                            $cp_cval_string .= chr($cp_temp2[0]);
                        }
                    }
                    elseif(strpos($cp_nval, 'substr') !== false)
                    {
                        $cp_temp1 = explode('\'', $cp_nval);
                        $cp_temp2 = explode('(', $cp_nval);
                        $cp_temp3 = explode(')', $cp_temp2[1]);
                        $cp_temp4 = explode(',', $cp_temp3[0]);
                        $cp_cval_string .= substr($cp_temp1[1], trim($cp_temp4[0]), trim($cp_temp4[1]));
                    }
                    else
                    {
                        $cp_cval_string .= trim(trim($cp_nval, '\''));
                    }
                    $cp_cval_string .= NULL;
                }

                // String Output
                return $cp_cnam_string."=".$cp_cval_string;
            }
        }*/
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
            /*$data = $this->WEBParsing('http://wasabisyrup.com/archives/');
            $cookie = $this->splits($data, '<script>', '</script>');
            $cookie = $this->sucuri($cookie);*/
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