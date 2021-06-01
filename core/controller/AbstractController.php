<?php

namespace core\controller;

use core\controller\ProjectTwigExtension;

/**
 * Mother class for all Controller, used for custom error page and twig render system
 * @author Tristan
 * @version 2
 */
abstract class AbstractController
{
    /**
     * @var FilesystemLoader loader
     */
    protected $loader;

    /**
     * @var Environment twig
     */
    protected $twig;

    /**
     * @var TwigTemplate twig
     */
    protected $template;



    /**
     * The main function for display Twig template
     * @param String name of Twig template
     * @param data all data for template
     * @return TwigTemplate template
     */
    public function render(String $view, array $data = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__, 2).'/template');
        $twig = new \Twig\Environment($loader, [
        ]);
        $twig->addExtension(new ProjectTwigExtension());
        $template = $twig->load($view . ".twig");
        return $template->render($data);
    }

    /**
     * throw 404 error and display his page
     * @return TwigTemplate 404  error page
     */
    public function error404()
    {
        header(filter_input(INPUT_SERVER, "SERVER_PROTOCOL") . ' 404 Not Found');
        return print_r($this->render('error\404'));
    }

    /**
     * throw 403 error and display his page
     * @return TwigTemplate 404  error page
     */
    public function error403($isAdmin = false)
    {
        header(filter_input(INPUT_SERVER, "SERVER_PROTOCOL") . ' 403 Forbidden');
        if ($isAdmin) {
            return print_r($this->render('error\403admin'));
        }
        return print_r($this->render('error\403'));
    }
}
