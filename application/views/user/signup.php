<?php
function get_client_ip()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
//echo "SERVER : ".$_SERVER['REMOTE_ADDR'];
//echo "<br>";
//echo "getenv : ".get_client_ip();
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once "application/views/template/head.php"; ?>

<body>
    <div>
        <h1>회원가입</h1>

        <form action="signup" method="post">
            <div><input type="email" name="email" placeholder="email" autofocus required></div>
            <div><input type="password" name="pw" placeholder="password" required></div>
            <div><input type="text" name="nm" placeholder="name" required></div>
            <div><input type="hidden" name="uip" value="<?= $_SERVER['REMOTE_ADDR'] ?>" required></div>
            <div>
                <input type="submit" value="회원가입">
            </div>
        </form>
    </div>
</body>

</html>