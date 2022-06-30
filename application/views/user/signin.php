<!DOCTYPE html>
<html lang="en">
<?php include_once "application/views/template/head.php"; ?>

<body class="h-full container-center">
    <div>
        <div class="d-inline-flex flex-grow-1 flex-shrink-0">
            <a href="/feed/index">
                <img src="/static/svg/logo.svg">
            </a>
        </div>
        <div class="err">
            <?php
                if (isset($_GET["err"])) {
                    print "로그인을 할 수 없습니다.";
                }
            ?>
        </div>
        <form action="signin" method="post">
            <!-- post 방식은 form 태그 있어야 한다. -->
            <div><input type="email" name="email" placeholder="email" value="<?= getParam('email') ?>" autofocus required></div>
            <div><input type="password" name="pw" placeholder="password" required></div>
            <div>
                <input type="submit" value="로그인">
            </div>
        </form>
        <div>
            <a href="signup">회원가입</a>
        </div>
    </div>
</body>

</html>

<!--

    GET(삭제) - 값이 쿼리스트링으로 전달.

    POST(등록, 수정) - 값이 Body에 담겨져서 전달.

    javascript 
    const obj = { "name": "홍길동" } 왼쪽 key 값, 오른쪽 value 값

    php
    $arr = [ "name" => "홍길동" ]

-->