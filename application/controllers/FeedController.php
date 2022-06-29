<?php

namespace application\controllers;

class FeedController extends Controller
{
    public function index() {
        $this->addAttribute(_JS, ["feed/index"]);
        $this->addAttribute(_MAIN, $this->getView("feed/index.php"));
        return "template/t1.php";
    }

    public function rest() {
        switch (getMethod()) {
            case _POST:

                if(!is_array($_FILES) || !isset($_FILES["imgs"])) {
                    return ["result" => 0];
                }
                
                $iuser = getIuser();
                $param = [
                    "location" => $_POST["location"],
                    "ctnt" => $_POST["ctnt"],
                    "iuser" => $iuser
                ];

                $ifeed = $this->model->insFeed($param);

                $paramImg = [ "ifeed" => $ifeed ];
                foreach($_FILES["imgs"]["name"] as $key => $originFileNm) {

                    $saveDirectory = _IMG_PATH . "/feed/" . $ifeed;
                    if(!is_dir($saveDirectory)) {
                        mkdir($saveDirectory, 0777, true);
                    }
                    $tempName = $_FILES["imgs"]["tmp_name"][$key];
                    $randomFileNm = getRandomFileNm($originFileNm);
                    IF(move_uploaded_file($tempName, $saveDirectory . "/" . $randomFileNm)) {
                        $paramImg["img"] = $randomFileNm;

                        $this->model->insFeedImg($paramImg);
                        // chmod("C:/Apache24/PHPgram/static/img/profile/1/test." . $ext, 0755);
                    }
                }
                // return ["result" => $r];

                // debug 확인.(파일이 등록 됬는지)
                // print getIuser();
                // if (is_array($_FILES)) {
                //     foreach ($_FILES['imgs']['name'] as $key => $value) {
                //         print "key : {$key}, value: {$value} <br>";
                //     }
                // }
                // print "ctnt : " . $_POST["ctnt"] . "<br>";
                // print "location : " . $_POST["location"] . "<br>";
                return ["result" => 1];

            case _GET:
                $page = 1;
                if(isset($_GET["page"])) {
                    $page = intval($_GET["page"]);
                }
                
                $startIdx = ($page - 1) * _FEED_ITEM_CNT;
                $param = [
                    "startIdx" => $startIdx,
                    "iuser" => getIuser()
                ];

                return $this->model->selFeedList($param);
        }
    }
}
