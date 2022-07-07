<?php

    namespace application\libs;

    require_once "application/utils/UrlUtils.php";
    require_once "application/utils/SessionUtils.php";
    require_once "application/utils/FileUtils.php";

    class Application {
        public $controller;
        public $action;
        private static $modelList = []; // static 사용 하는 순간 메모리가 올라감. new 와 달리 한 번만 사용.

        public function __construct() {        
            $urlPaths = getUrlPaths();
            $controller = isset($urlPaths[0]) && $urlPaths[0] != '' ? $urlPaths[0] : 'board';
            $action = isset($urlPaths[1]) && $urlPaths[1] != '' ? $urlPaths[1] : 'index';

            if (!file_exists('application/controllers/'. $controller .'Controller.php')) {
                echo "해당 컨트롤러가 존재하지 않습니다.";
                exit();
            }

            $controllerName = 'application\controllers\\' .$controller . 'controller';

            $model = $this->getModel($controller);
            new $controllerName($action, $model);
        }

        public static function getModel($key) {
            if (!in_array($key, static::$modelList)) {
                $modelName = 'application\models\\' . $key . 'model';
                static::$modelList[$key] = new $modelName();
            }
            
            return static::$modelList[$key];
        }
    }