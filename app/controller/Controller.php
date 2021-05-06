<?php

namespace app\controller;

abstract class controller {

    protected $loader, $twig, $template;



    function render(String $view, array $data = null) {
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__).'/template');
        $twig = new \Twig\Environment($loader, [
        ]);
        $template = $twig->load($view . ".twig");
        return $template->render($data);
    }
}