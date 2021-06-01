<?php

namespace core\controller;

class ProjectTwigExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions()
    {
        return
        [new \Twig\TwigFunction('guest', function () {
            return \core\auth\RoleChecker::guest();
        }) ,
        new \Twig\TwigFunction('auth', function ($role) {
            return \core\auth\RoleChecker::role($role);
        }) ,
        new \Twig\TwigFunction('string', function ($string, $editor = false) {
            $string = html_entity_decode($string, ENT_QUOTES);
            if ($editor)
            {
                $string = strip_tags($string);
            }
            return $string;
        }),
        new \Twig\TwigFunction('getSession', function ($session) {
            return (new \core\request\RequestManager())->session($session);
        })
        ];
    }
}
