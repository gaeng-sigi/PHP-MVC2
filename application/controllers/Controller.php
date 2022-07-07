<?php

    namespace application\controllers;

    class Controller {
        protected $ctx;
        protected $model;

        // model에 static을 붙일 수 없는 이유 - feedmodel, usermodel이 있는데,
        // 값을 하나만 받을 수 있기 때문에 static을 붙일 수 없다.
        // static이 있거나, parameter가 있으면 static을 붙여도 된다.

        private static $needLoginUrlArr = [ // 로그인 해야지 들어갈 수 있음.
            "feed",
            "user/feedwin"
        ];

        public function __construct($action, $model) {
            if (!isset($_SESSION)) { session_start(); }

            $urlPaths = getUrl();

            foreach (static::$needLoginUrlArr as $url) {
                if (strpos($urlPaths, $url) === 0 && !isset($_SESSION[_LOGINUSER])) {
                    // header("Location:/user/signin");
                    // exit();

                    $this->getView("redirect:/user/signin");
                }
            }

            $this->model = $model;
            $view = $this->$action();

            if (empty($view) && gettype($view) === "string") {
                echo "Controller 에러 발생";
                exit();
            }

            // 문자열 -> 화면 응답 / 객체, 배열 -> 제이슨 응답

            if (gettype($view) === "string") {
                require_once $this->getView($view);
            } else if (gettype($view) === "object" || gettype($view) === "array") {
                header("Content-Type:application/json");
                echo json_encode($view);
            }
        }
        
        protected function getModel() {
            
        }

        protected function addAttribute($key, $val) {
            $this->$key = $val;
        }

        protected function getView($view) {
            if (strpos($view, "redirect:") === 0) {
                header("Location: " . substr($view, 9));
                exit();
            }
            
            return _VIEW . $view;
        }

        protected function flash($name = '', $val = '') {
            if (!empty($name)) { // 공백이 아니면
                if (!empty($val)) {
                    $_SESSION[$name] = $val;
                } else if (empty($val) && !empty($_SESSION[$name])) {
                    unset($_SESSION[$name]);
                }
            }
        }
    }
