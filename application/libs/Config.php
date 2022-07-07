<?php

    // config.php 여러 가지 상수들을 정의 한다!!!

    // 상수 - 변하지 않는 값
    // 도메인, 파일위치, 디렉터리 경로, PATH 등 고유값

    // 상수를 쓰는 이유 - 페이지가 많은 경우, 코드 파악하기 힘든 경우
    // 공동 작업 하는 경우(실수 또는 협의가 안 되어 고정값이 변경되는 것을 방지)

    // 상수는 common, header 등 모든 페이지에 삽입되는 공통문서에 적용되며 ---- 개별 페이지에는 적용 안함 XXX!!!
    // define(상수, 값)

    define("_SERVICE_NM", "PHPgram");
    define('_ROOT', $_SERVER['DOCUMENT_ROOT']);
    define('_DBTYPE', 'mysql'); //mysql, mariadb 등
    define('_DBHOST', 'localhost'); //DB접속 주소
    define('_DBNAME', 'phpgram'); //DB명
    define('_DBUSER', 'root'); //아이디
    define('_DBPASSWORD', '506greendg@'); //비번
    define('_CHARSET', 'utf8');
    define("_VIEW", "application/views/");

    define("_HEADER", "header");
    define("_MAIN", "main");
    define("_FOOTER", "footer");

    define("_CSS", "css");
    define("_JS", "js");

    define("_LOGINUSER", "loginUser"); // 로그인 세션

    define("_LIST", "list");
    define("_DATA", "data");
    define("_ITEM", "item");
    define("_RESULT", "result");

    define("_POST", "POST");
    define("_GET", "GET");
    define("_PUT", "PUT");
    define("_DELETE", "DELETE");

    define("_IMG_PATH", "static/img"); // 이미지 저장 공간
    define("_FEED_ITEM_CNT", 20);