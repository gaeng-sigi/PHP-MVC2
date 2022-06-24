<?php
namespace application\controllers;

class UserController extends Controller {
    public function signin() {        
        switch(getMethod()) {
            case _GET:
                return "user/signin.php";
            case _POST:

                $param = [
                    "email" => $_POST["email"],
                    "pw" => $_POST["pw"]
                ];

                $dbUser = $this->model->selUser($param);
                
                // 아이디, 비번이 하나라도 없거나 틀리면 > /user/signin 리다이렉트
                if(!$dbUser || !password_verify($param["pw"], $dbUser->pw)) {
                    return "redirect:signin?email={$param["email"]}&err";
                }

                // 아이디, 비번이 맞다면 > /feed/index 리다이렉트 해주세요.
                return "redirect:/feed/index";
            }
    }

    public function signup() {
        switch(getMethod()) {
            case _GET:
                return "user/signup.php";
            case _POST:
                $param = [
                    "email" => $_POST["email"],
                    "pw" => $_POST["pw"],
                    "nm" => $_POST["nm"]
                ];

                // 비밀번호 암호화
                $param["pw"] = password_hash($param["pw"], PASSWORD_BCRYPT);
                $this->model->insUser($param);

                return "redirect:signin";
        }
    } 
}