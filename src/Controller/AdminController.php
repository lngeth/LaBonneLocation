<?php

namespace App\Controller;

use App\Entity\Billing;
use App\Entity\Car;
use App\Entity\User;
use App\Form\AddCarType;
use App\Repository\BillingRepository;
use App\Repository\CarRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class AdminController
 * @package App\User
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("", name="index")
     * @return Response
     */
    public function index(): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if($securityContext->isGranted('IS_AUTHENTICATED_FULLY') &&
            $this->get('security.token_storage')->getToken()->getUser()->getRole() == "Admin") {
            return $this->render('admin/index.html.twig', []);
        }


        $error = "Vous n'êtes pas administrateur !";
        return $this->redirectToRoute('index', [
            'error' => $error
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/ajouterVoiture", name="addCar")
     */
    public function createCar(Request $request) : Response
    {
        $adminCall = true;
        return $this->redirectToRoute('addCar', ['adminCall' => $adminCall]);
    }

    /**
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $entityManager
     * @param Car $car
     * @Route("/car/modify/{id}", name="editCar")
     */
    public function editCar(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager, Car $car)
    {
        $form = $this->createForm(AddCarType::class, $car);
        $form->handleRequest($request);

        $datasheet = [
            "motor" => $form->get('motor')->getData(),
            "vitesse" => $form->get('vitesse')->getData(),
            "nbSeat" => $form->get('nbSeat')->getData()
        ];

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $car->getIdOwner();

            $file = $form->get('image')->getData();
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('kernel.project_dir').'/public/assets/images',
                    $newFilename
                );
            } catch (FileException $e) {
                dump($e);
            }
            $car->setImage($newFilename)
                ->setIdOwner($user)
                ->setDatasheet($datasheet)
                ->setRent("disponible");

            $entityManager->persist($car);
            $entityManager->flush();

            $this->addFlash('message', "Votre véhicule a été modifié !");
            return $this->redirectToRoute('admin_index');

        }

        return $this->render('dashboard/renter/addCar.html.twig',[
            'form' => $form->createView(),
            'datasheet' => $datasheet
        ]);
    }

    /**
     * @Route("/cars", name="cars")
     * @param CarRepository $carRepository
     * @return Response
     */
    public function displayCars(CarRepository $carRepository) : Response
    {
        $cars = $carRepository->findAll();
        return $this->render('admin/cars.html.twig', [
            'cars' => $cars
        ]);
    }

    /**
     * @Route("/billings", name="billings")
     * @param BillingRepository $billings
     * @return Response
     */
    public function displayBillings(BillingRepository $billingRepository) : Response
    {
        $billings = $billingRepository->findAll();
        return $this->render('admin/billings.html.twig', [
           'billings' => $billings
        ]);
    }

    /**
     * @Route("/car/supprimer/{id}", name="remove_car")
     * @param Car $car
     * @param CarRepository $carRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function removeCar(Car $car,CarRepository $carRepository, EntityManagerInterface $entityManager) : Response
    {
        $entityManager->remove($car);
        $entityManager->flush();
        $this->addFlash('message', "La voiture a bien été supprimée !");

        return $this->redirectToRoute('admin_index');
    }

    /**
     * @Route("/users", name="users")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function displayUsers(UserRepository $userRepository) : Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/supprimer/{id}", name="remove_user")
     * @param User $user
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function removeUser(User $user, UserRepository $userRepository, EntityManagerInterface $entityManager) : Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('message', "La voiture a bien été supprimée !");

        return $this->redirectToRoute('admin_index');
    }
}
