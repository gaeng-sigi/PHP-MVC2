<?php

    // autoload.php 모든 클래스를 자동으로 불러 올 수 있도록 사용 한다!!!(객체 지향 프로그래밍)

    spl_autoload_register(function ($path) {      
        $path = str_replace('\\','/',$path);
        $paths = explode('/', $path);        
        if (preg_match('/model/', strtolower($paths[1]))) {
            $className = 'models';
        } else if (preg_match('/controller/',strtolower($paths[1]))) {
            $className = 'controllers';
        } else {
            $className = 'libs';
        }

        $loadpath = $paths[0].'/'.$className.'/'.$paths[2].'.php';
        
       // echo 'autoload $path : ' . $loadpath . '<br>';
        
        if (!file_exists($loadpath)) {
            echo " --- autoload : file not found. ($loadpath) ";
            exit();
        }
        
        require_once $loadpath;
    });