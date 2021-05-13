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
            return \core\auth\roleChecker::guest();
        }) ,
		new \Twig\TwigFunction('auth', function ($role) {
            return \core\auth\roleChecker::role($role);
        })
        ];
	}

}