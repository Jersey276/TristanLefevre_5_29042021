<?php

namespace app;

use AltoRouter;
use app\controller\HomeController;
use app\controller\AuthController;
use app\controller\PostController;
use core\auth\roleChecker;

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
            if ($match != false)
            {
                if ($match['target']['ur'] != null)
                {
                    if ($match['target']['ur'] != "Guest")
                    {
                        if (!roleChecker::role($match['target']['ur']))
                        {
                        return header( filter_input(INPUT_SERVER, "SERVER_PROTOCOL") . ' 404 Not Found');
                        }
                    } elseif ($match['target']['ur'] == "Guest" && !roleChecker::guest())
                    {
                        return header( filter_input(INPUT_SERVER, "SERVER_PROTOCOL") . ' 404 Not Found');
                    }
                }
                switch ($match['target']['c'])
                {
                    case ('HomeController') :
                        $controller = new HomeController();
                        break;
                    case ('AuthController') :
                        $controller = new AuthController();
                        break;
                    case ('PostController') :
                        $controller = new PostController();
                        break;
                };
                call_user_func_array(array($controller,$match['target']['a']), $match['params']);
                return $this;
            }
            return header( filter_input(INPUT_SERVER, "SERVER_PROTOCOL") . ' 404 Not Found');
        }
    }
?>