<?php

namespace core\controller;

use core\controller\ProjectTwigExtension;

abstract class AbstractController {

    protected $loader, $twig, $template;



    function render(String $view, array $data = []) {
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__, 2).'/template');
        $twig = new \Twig\Environment($loader, [
        ]);
        $twig->addExtension(new ProjectTwigExtension());
        $template = $twig->load($view . ".twig");
        return $template->render($data);
    }
}