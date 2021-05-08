<?php

namespace app;

use AltoRouter;
use app\controller\homeController;

class Router {

        private $router;

        public function __construct ()
        {
            $this->router = new AltoRouter();
        }

        public function get(string $url, string $controller, string $function, string $userRole = null, ?string $name = null)
        {
            $this->router->map('GET', $url, [ 'c' => $controller, 'a' => $function, 'ur' => $userRole], $name);
            return $this;
        }
        public function post(string $url, string $controller, string $function, string $userRole = null, ?string $name = null)
        {
            $this->router->map('POST', $url, [ 'c' => $controller, 'a' => $function, 'ur' => $userRole], $name);
            return $this;
        }

        public function run() {
            $match = $this->router->match();
            if ($match != false){
            switch ($match['target']['c']) {
                case ('homeController') :
                    $controller = new homeController();
                    break;
            };
            call_user_func_array(array($controller,$match['target']['a']), array());
            return $this;
            }
            header( filter_input(INPUT_SERVER, "SERVER_PROTOCOL") . ' 404 Not Found');
        }
    }
?>