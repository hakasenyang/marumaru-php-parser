# marumaru-php-parser
마루마루 사이트의 PHP 파싱 파일입니다. fmaru 관련 포팅 자료도 있습니다.

## 제공 파일
* Fmaru 포팅
* marumaru API (이미지 주소를 json 으로 출력)
* 기타...

## 예시 사이트
[Hakase's Marumaru API](https://marumaru.hakase.kr/)

## 사용 방법
1. 해당 파일을 다운로드 받아서 권한을 777로 주면 끝납니다.
2. 권한은 cookie.txt, mangalist.txt (쿠키 캐싱) 파일을 생성하기 위해 필요합니다.
3. api 관련 rewrite 는 아래를 참고하십시오. (nginx 기준)
4. rewrite 없이 사용하려면 소스를 수정하셔야 합니다.

```
rewrite ^/api/(.*)/(.*)$ /index.php?num=$1&json=1;
rewrite ^/api/(.*)$ /index.php?num=$1;
rewrite ^/img/(.*)$ /index.php?num=$1&image=1;
rewrite ^/fmaru$ /fmaru.php;
```

## Fmaru 포팅 원본 소스
Fmaru 관련 포팅의 원본 소스는 [fmaru's github](https://github.com/fmaru/fmaru) 에서 확인하실 수 있습니다.

## Sucuri proxy 소스
```
Sucuri proxy 관련 소스는 아래와 같은 사이트에서 가져왔습니다.
 * [organization's cloudflare-bypass](https://github.com/organization/cloudflare-bypass)
 * [코드팟's PHP Sucuri Proxy](http://cafe.naver.com/gogoomas/337647)
```
현재 yuncomics 에서 wasabisyrup 으로 교체되면서 Sucuri Proxy 가 적용되고 있지 않습니다.
따라서 현재는 임시로 소스를 제거합니다.

## 저작권
MIT License 를 따릅니다. fmaru 의 작품(?)처럼 마음껏 수정해서 광고를 달던 어떻게 사용하던 상관 없습니다.
