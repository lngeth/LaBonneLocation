<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
use App\Form\AddCarType;
use App\Repository\BillingRepository;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(CarRepository $carRepository, BillingRepository $billingRepository): Response
    {
        $user = $this->getUser();
        if ($user->getRole() == 'client') {
            $idUser = $user->getId();
            $billsReturned = 0;
            $priceOfAllRent = 0;
            $unpaidBills = 0;
            $priceOfThisMonthRent = 0;
            $bills = $billingRepository->findBy([
                'idUser' => $idUser
            ]);
            foreach ($bills as $bill) {
                $priceOfAllRent += $bill->getPrice();
                if ($bill->getReturned()) {
                    $billsReturned++;
                }
                if (!$bill->getPaid()) {
                    $unpaidBills++;
                }
                if (date_format($bill->getStartDate(), 'm') == date('m')) {
                    $priceOfThisMonthRent += $bill->getPrice();
                }
            }
            return $this->render('dashboard/client/index.html.twig', [
                'nbCarsRented' => sizeof($bills),
                'nbCarsRentedReturned' => $billsReturned,
                'priceOfAllRent' => $priceOfAllRent,
                'unpaidBills' => $unpaidBills,
                'priceOfThisMonthRent' => $priceOfThisMonthRent
            ]);
        }
        $idUser = $user->getId();
        $bills = $billingRepository->findAllClientAndTheCarRentedOwnedBy($idUser);
        //dd($bills);
        $cars = $carRepository->findBy([
            'idOwner' => $idUser
        ]);
        $carsAvailable = 0;
        $priceOfAllRent = 0;
        $unpaidBills = 0;
        $priceOfThisMonthRent = 0;
        foreach ($cars as $car){
            if ($car->getRent() == 'disponible') {
                $carsAvailable++;
            }
        }
        foreach ($bills as $bill) {
            $priceOfAllRent += $bill->getPrice();
            if (!$bill->getPaid()) {
                $unpaidBills++;
            }
            if (date_format($bill->getStartDate(), 'm') == date('m')) {
                $priceOfThisMonthRent += $bill->getPrice();
            }
        }
        return $this->render('dashboard/renter/index.html.twig', [
            'nbCarsRented' => sizeof($bills),
            'nbCars' => sizeof($cars),
            'carsAvailable' => $carsAvailable,
            'priceOfAllRent' => $priceOfAllRent,
            'unpaidBills' => $unpaidBills,
            'priceOfThisMonthRent' => $priceOfThisMonthRent
        ]);
    }

    /**
     * @Route("/dashboard/ajouterVoiture", name="addCar")
     */
    public function addCar(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger) {
        $car = new Car();

        $form = $this->createForm(AddCarType::class, $car);
        $form->handleRequest($request);

        $datasheet = [
            "motor" => $form->get('motor')->getData(),
            "vitesse" => $form->get('vitesse')->getData(),
            "nbSeat" => $form->get('nbSeat')->getData()
        ];

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->get('security.token_storage')->getToken()->getUser();

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

            $manager->persist($car);
            $manager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('dashboard/renter/addCar.html.twig', [
            'form' => $form->createView(),
            'datasheet' => $datasheet]);
    }

    /**
     * @Route("/dashboard/mesVoitures", name="show_owned_cars")
     */
    public function showAllOwnedCars(CarRepository $carRepository){
        $user = $this->getUser();
        $ownedCars = $carRepository->findBy(['idOwner' => $user->getId()]);
        //dd($ownedCars);
        return $this->render('dashboard/renter/showOwnedCars.html.twig', [
            'ownedCars' => $ownedCars
        ]);
    }

    /**
     * @Route("/dashboard/voituresLouees", name="showRentedCars")
     */
    public function showAllRentedCars(BillingRepository $billingRepository){
        $user = $this->getUser();
        $rentedCars = $billingRepository->findAllClientAndTheCarRentedOwnedBy($user->getId());
        //dd($rentedCars);
        return $this->render('dashboard/renter/showOwnedCars.html.twig', [
            'rentedCars' => $rentedCars
        ]);
    }

    /**
     * @Route("/dashboard/supprimer/{idCar}", name="delete_car")
     */
    public function deleteCar(int $idCar, BillingRepository $billingRepository, CarRepository $carRepository, EntityManagerInterface $manager) {
        $carInBilling = $billingRepository->findBy([
            'idCar' => $idCar
        ]);
        $msg = null;
        if (!empty($carInBilling)){
            $msg='Il y a une location sur ce véhicule. Vous ne pouvez pas le supprimer.';
            //dd($msg);
            return $this->render('dashboard/renter/showOwnedCars.html.twig', [
                'errorMsg' => $msg
            ]);
        }
        $carToDelete = $carRepository->find($idCar);
        //dd($carToDelete);
        $manager->remove($carToDelete);
        $manager->flush();
        $fileName = $carToDelete->getImage(); //effacer l'image
        $projectDir = $this->getParameter('kernel.project_dir');
        $fileSystem = new Filesystem();
        $fileSystem->remove($projectDir.'/public/assets/images/'.$fileName);
        $successMsg = 'Suppression réussie';
        return $this->render('dashboard/renter/showOwnedCars.html.twig', [
            'successMsg' => $successMsg
        ]);
    }

    /**
     * @Route("/dashboard/mesLocations", name="show_my_rented_cars")
     */
    public function showMyRentedCars(BillingRepository $billingRepository, CarRepository $carRepository, EntityManagerInterface $entityManager) {
        $user = $this->getUser();
        $carsRented = $billingRepository->findAllRentedCarsByIdUser($user->getId());
        //dd($carsRented);
        $today = date('d/m/Y');
        foreach ($carsRented as $carRented) {
            if ($carRented->getEndDate()->format('d/m/Y') < $today) {
                $coucou = "coucou";
                $car = $carRepository->findOneBy($carRented->getIdCar()->getId());
                if ($car->getRent() == 'indisponible') {
                    $car->setRent('disponible');
                }
                $car->setQuantity($car->getQuantity()+1);
                $entityManager->persist($car);
                $entityManager->flush();
                $carRented->setReturned(1);
                $entityManager->persist($carRented);
                $entityManager->flush();
            }
        }
        return $this->render('dashboard/client/myRent.html.twig', [
            'carsRented' => $carsRented
        ]);
    }
}