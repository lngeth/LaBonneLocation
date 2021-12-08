<?php

namespace App\Controller;

use App\Repository\CarRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    /**
     * @Route("/voiture/caracteristiques/{id}", name="car_show")
     */
    public function showCar(int $id, CarRepository $carRepository, UserRepository $userRepository) {
        $car = $carRepository->find($id);
        $renter = $userRepository->find($car->getIdOwner());
        return $this->render('car/showCar.html.twig', [
            'car' => $car,
            'renter' => $renter
        ]);
    }
}
