<?php
/**
 * Created by PhpStorm.
 * User: stefano
 * Date: 14/07/18
 * Time: 11:16
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction(Session $session)
    {
        $session->clear();
        return $this->render(':louvre:home.html.twig');
    }
}