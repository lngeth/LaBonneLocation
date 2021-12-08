<?php

namespace App\Controller;

use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param CarRepository $repository
     * @param Request $request
     * @return Response
     */
    public function index(CarRepository $repository, Request $request): Response
    {
        $cars = $repository->findBy(['rent' => 'disponible'], ['amount'=>'DESC'], 3);

        return $this->render('home/index.html.twig', [
            'cars' => $cars,
            'error' => $request->query->get('error') ?? null
        ]);
    }

    /**
     * @Route("/index/catalogue", name="show_all_cars")
     * @param CarRepository $repository
     * @return Response
     */
    public function showAllCars(CarRepository $repository)
    {
        $cars = $repository->findBy(['rent' => 'disponible']);
        //dd($cars);
        return $this->render('home/allCars.html.twig', ['cars'=> $cars]);
    }
}