<?php

namespace core\controller;

use core\controller\ProjectTwigExtension;

abstract class AbstractController
{
    protected $loader;
    protected $twig;
    protected $template;



    public function render(String $view, array $data = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__, 2).'/template');
        $twig = new \Twig\Environment($loader, [
        ]);
        $twig->addExtension(new ProjectTwigExtension());
        $template = $twig->load($view . ".twig");
        return $template->render($data);
    }

    public function error404()
    {
        header(filter_input(INPUT_SERVER, "SERVER_PROTOCOL") . ' 404 Not Found');
        return print_r($this->render('error\404'));
    }
    public function error403($isAdmin = false)
    {
        header(filter_input(INPUT_SERVER, "SERVER_PROTOCOL") . ' 403 Forbidden');
        if ($isAdmin) {
            return print_r($this->render('error\403admin'));
        }
        return print_r($this->render('error\403'));
    }
}
