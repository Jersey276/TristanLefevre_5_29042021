<?php

namespace core\controller;

abstract class AbstractController {

    protected $loader, $twig, $template;



    function render(String $view, array $data = null) {
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__, 2).'/template');
        $twig = new \Twig\Environment($loader, [
        ]);
        $template = $twig->load($view . ".twig");
        return $template->render($data);
    }
}