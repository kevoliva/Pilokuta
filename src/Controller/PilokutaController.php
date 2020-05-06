<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PilokutaController extends AbstractController
{
    /**
     * @Route("/pilokuta", name="pilokuta")
     */
    public function index()
    {
        return $this->render('pilokuta/index.html.twig', [
            'controller_name' => 'PilokutaController',
        ]);
    }
}
