<?php
    namespace application\controllers;

    class UserController extends Controller {

        // 로그인
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

                    $dbUser->pw = null;
                    $dbUser->regdt = null;
                    $this->flash(_LOGINUSER, $dbUser);
                    
                    // 아이디, 비번이 맞다면 > /feed/index 리다이렉트
                    return "redirect:/feed/index";
                }
        }

        // 회원가입
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

        public function logout() {
            $this->flash(_LOGINUSER);
            return  "redirect:/user/signin";
        }

        public function feedwin() {
            $iuser = isset($_GET["iuser"]) ? intval($_GET["iuser"]) : 0;
            $param = ["feediuser" => $iuser, "loginiuser" => getIuser() ];
            $this->addAttribute(_DATA, $this->model->selUserProfile($param));
            $this->addAttribute(_JS, ["user/feedwin", "https://unpkg.com/swiper@8/swiper-bundle.min.js"]);
            $this->addAttribute(_CSS, ["user/feedwin", "https://unpkg.com/swiper@8/swiper-bundle.min.css"]);
            $this->addAttribute(_MAIN, $this->getView("user/feedwin.php"));
            return "template/t1.php";
        }
    }