<?php

namespace app;

use AltoRouter;
use app\controller\HomeController;
use app\controller\AuthController;
use app\controller\PostController;
use app\controller\CommentController;
use app\controller\ErrorController;
use core\auth\roleChecker;

class Router
{
    private $router;

    public function __construct()
    {
        $this->router = new AltoRouter();
    }

    /**
     * map a Get road on route list
     * @return Router
     */
    public function get(string $url, string $controller, string $function, string $userRole = null, ?string $name = null)
    {
        $this->router->map('GET', $url, [ 'c' => $controller, 'a' => $function, 'ur' => $userRole], $name);
        return $this;
    }

    /**
     * map a Post road on route list
     * @return Router
     */
    public function post(string $url, string $controller, string $function, string $userRole = null, ?string $name = null)
    {
        $this->router->map('POST', $url, [ 'c' => $controller, 'a' => $function, 'ur' => $userRole], $name);
        return $this;
    }

    /**
     * run this road
     * @return mixed function asked
     */
    public function run()
    {
        $match = $this->router->match();
        if ($match != false) {
            if ($match['target']['ur'] != null) {
                $check = $this->checkRuleRole($match['target']['ur']);
            }
            if ($match['target']['ur'] == null ||
                $match['target']['ur'] != null &&
                $check['result']
            ) {
                $controller = $this->newController($match['target']['c']);
                call_user_func_array(array($controller,$match['target']['a']), $match['params']);
                return $this;
            }
            return $this->throwError($check['code']);
        }
        return $this->throwError();
    }
    
    /**
     * Check if user can use this route
     * @param string
     * @return array bool for result and int for error code
     */
    public function checkRuleRole($rule)
    {
        switch ($rule) {
            case 'Guest':
                if (!roleChecker::guest()) {
                    return ["result" => false, "code" => 404];
                }
                return ["result" => true];
                break;
            case 'User':
                if (roleChecker::guest()) {
                    return ['result' => false, "code" => 401];
                }
                return ['result' => true];
                break;
            default:
                if (roleChecker::role($rule)) {
                    return ['result' => true];
                }
                return ['result' => false, "code" => 403];
        }
    }

    /**
     * Ask for error page
     * @return mixed error function
     */
    public function throwError($code = 404)
    {
        switch ($code) {
            case (401):
                return header('Location: /login');
                break;
            case (403):
                return (new ErrorController())->error403();
                break;
            default:
                return (new ErrorController())->error404();
        }
    }

    public function newController($controller = "ErrorController")
    {
        switch ($controller) {
            case ('HomeController'):
                return new HomeController();
                break;
            case ('AuthController'):
                return new AuthController();
                break;
            case ('PostController'):
                return new PostController();
                break;
            case ('CommentController'):
                return new CommentController();
                break;
            default:
                return $this->throwError();
                break;
        };
    }
}
