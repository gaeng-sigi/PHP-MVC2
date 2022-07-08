<?php
    namespace application\controllers;

    use application\libs\Application;

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
                        "nm" => $_POST["nm"],
                        "uip" => $_POST["uip"]
                    ];

                    // 비밀번호 암호화
                    $param["pw"] = password_hash($param["pw"], PASSWORD_BCRYPT);
                    $this->model->insUser($param);

                    return "redirect:signin";
            }
        }

        // 로그아웃
        public function logout() {
            $this->flash(_LOGINUSER);

            return  "redirect:/user/signin";
        }


        // 프로필
        public function feedwin() {
            $iuser = isset($_GET["iuser"]) ? intval($_GET["iuser"]) : 0;
            $param = ["feediuser" => $iuser, "loginiuser" => getIuser() ];

            $this->addAttribute(_DATA, $this->model->selUserProfile($param));
            $this->addAttribute(_JS, ["user/feedwin", "https://unpkg.com/swiper@8/swiper-bundle.min.js"]);
            $this->addAttribute(_CSS, ["user/feedwin", "https://unpkg.com/swiper@8/swiper-bundle.min.css", "feed/index"]);
            $this->addAttribute(_MAIN, $this->getView("user/feedwin.php"));

            return "template/t1.php";
        }

        // 피드
        public function feed() {
            if(getMethod() === _GET) {
                $page = 1;

                if (isset($_GET["page"])) {
                    $page = intval($_GET["page"]);
                }

                $startIdx = ($page - 1) * _FEED_ITEM_CNT;
                $param = [
                    "startIdx" => $startIdx,
                    "toiuser" => $_GET["iuser"],
                    "loginiuser" => getIuser()
                ];

                $list = $this->model->selFeedList($param);
                
                foreach ($list as $item) {
                    $param2 = ["ifeed"=>$item->ifeed];
                    $item->imgList = Application::getModel("feed")->selFeedImgList($param2);
                    $item->cmt = Application::getModel("feedcmt")->selFeedCmt($param2);
                }

                return $list;
            }
        }

        // 팔로우
        public function follow() {
            $param = [
                'fromiuser' => getIuser()
            ];

            switch (getMethod()) {
                case _POST: // 팔로우 처리
                    $json = getJson();
                    $param["toiuser"] = $json["toiuser"];

                    return [_RESULT => $this->model->insUserFollow($param)];

                case _DELETE: // 팔로우 취소
                    $param["toiuser"] = $_GET["toiuser"];

                    return [_RESULT => $this->model->delUserFollow($param)];
            }
        }
        
        
        public function profile() {
            switch(getMethod()) {
                case _DELETE: // 현재 사진 삭제(프로필 사진)
                    $loginUser = getLoginUser();
                    if($loginUser && $loginUser->mainimg !== null) {
                        $path = "static/img/profile/{$loginUser->iuser}/{$loginUser->mainimg}";
                        if(file_exists($path) && unlink($path)) {
                            $param = ["iuser" => $loginUser->iuser, "delMainImg" => 1];
                            if($this->model->updUser($param)) {
                                rmdir("static/img/profile/{$loginUser->iuser}"); // 폴더도 같이 삭제.
                                $loginUser -> mainimg = null;
                                return [_RESULT => 1];
                            }
                        }
                    }
                return [_RESULT => 0];
                
                case _POST: // 사진 업로드(프로필 사진)
                    $filenm = $_FILES["img"]["name"];
                    if (!is_array($_FILES)) { return [_RESULT => 0]; }

                    $loginUser = getLoginUser();
                    $param = ["iuser" => $loginUser->iuser];

                    if ($loginUser && $loginUser->mainimg !== null) {
                        $path = "static/img/profile/{$loginUser->iuser}/{$loginUser->mainimg}";
                        if (file_exists($path)) {
                            unlink($path);
                        }
                    }
                    $saveDirectory = _IMG_PATH . "/profile/{$loginUser->iuser}";

                    if (!is_dir($saveDirectory)) {
                        mkdir($saveDirectory, 0777, true);
                    }

                    $tempName = $_FILES['img']["tmp_name"];
                    $randomFileNm = getRandomFileNm($filenm);

                    if (move_uploaded_file($tempName, $saveDirectory . "/" . $randomFileNm)) {
                        $param["mainimg"] = $randomFileNm;
                        $this->model->updUser($param);
                        $loginUser->mainimg = $randomFileNm;
                        return [_RESULT => 1];
                    }

                    return [_RESULT => 0];
            }
        }
    }