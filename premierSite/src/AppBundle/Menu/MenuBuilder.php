<?php

/**
 * Created by PhpStorm.
 * User: sebastien.nexon
 * Date: 16/01/2018
 * Time: 16:52
 */
// src/AppBundle/Menu/Builder.php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Index', array('route' => 'client_index'));

        $menu->addChild('Profile', array('route' => 'fos_user_profile_show'));

        $menu->addChild('Logout', array('route' => 'fos_user_security_logout'));

        return $menu;
    }
}