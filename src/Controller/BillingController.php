<?php

namespace App\Controller;

use App\Entity\Billing;
use App\Form\RentCarType;
use App\Repository\BillingRepository;
use App\Repository\CarRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BillingController extends AbstractController
{
    /**
     * @Route("/panier", name="panier_client")
     * @param SessionInterface $session
     * @param BillingRepository $billingRepository
     * @return Response
     */
    public function index(Request $request, SessionInterface $session, BillingRepository $billingRepository, CarRepository $carRepository, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {
        $panier = $session->get('panier', []);
        //vérification cookie
        $user = $this->get('security.token_storage')->getToken()->getUser();

        //unset($panier[2]);
        //$session->set('panier', $panier);
        $cookies = $request->cookies;
        //dd($panier);
        $json = $cookies->get($user->getId());
        if (!empty($json)) {
            $arrayCookie = json_decode($json, true);
            foreach ($arrayCookie as $key => $value) {
                if (!array_key_exists($key, $panier)) {
                    $car = $carRepository->find($key);
                    $userEntity = $userRepository->find($user->getId());
                    $start =new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $value['startDate']))));
                    $end = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $value['endDate']))));
                    $interval = ($start->diff($end))->d;
                    $price = $car->getAmount() * $interval;

                    $addPanier = new Billing();
                    $addPanier->setIdUser($userEntity)
                        ->setIdCar($car)
                        ->setStartDate($start)
                        ->setEndDate($end)
                        ->setPrice($price)
                        ->setPaid(0)
                        ->setReturned(0);
                    $manager->persist($addPanier);
                    $manager->flush();
                    $panier[$car->getId()] = 1;
                }
            }
            $session->set('panier', $panier);
        }

        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $billing =  $billingRepository->findOneBy([
                'idCar' => $id
            ]);

            $panierWithData[] = [
                'billing' => $billing,
                'quantity' => $quantity
            ];
        }

        $total = 0;
        foreach ($panierWithData as $item) {
            $billing = $item['billing'];
            $totalItem = $billing->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        return $this->render('billing/index.html.twig', [
            'panierClient' => $panierWithData,
            'total' => $total
        ]);

    }

    /**
     * @Route("/facture/client/{id}", name="billing_rent")
     * @param int $id
     * @param CarRepository $carRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function rentCar(int $id, CarRepository $carRepository, UserRepository $userRepository,
                            EntityManagerInterface $manager, Request $request) {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user->getRole() != 'client') {
            return $this->redirectToRoute('dashboard');
        }

        $car = $carRepository->find($id);
        $renter = $userRepository->find($car->getIdOwner());
        $billing = new Billing();

        $form = $this->createForm(RentCarType::class, $billing);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $end = $form->get('endDate')->getData();
            $start = $form->get('startDate')->getData();

            $cookieToEncode = [
                $car->getId() => [
                'startDate' => $start->format('d/m/Y')
                ]
            ];
            //dd($cookieToEncode);

            $interval = ($start->diff($end))->d;
            $price = $car->getAmount() * ($interval-2);
            $billing->setPaid(0)
                ->setReturned(0)
                ->setIdUser($user)
                ->setIdCar($car)
                ->setPrice($price)
                ->setStartDate($start);

            $manager->persist($billing);
            $manager->flush();

            if ($end->format('Y') == '9999') {
                $end = $form->get('startDate')->getData();
                $end->modify('last day of this month');
            }

            $billing->setEndDate($end);
            $manager->persist($billing);
            $manager->flush();

            //Ajout dans le panier
            $session = $request->getSession();

            $panier = $session->get('panier', []);
            $id = $car->getId();
            if(!empty($panier[$id]))
                $panier[$id]++;
            else
                $panier[$id] = 1;

            $session->set('panier', $panier);

            //Ajout dans les cookies
            $cookieToEncode[$car->getId()]['endDate'] = $end->format('d/m/Y');
            $existingCookie = $request->cookies->get($user->getId());
            $response = new Response();
            if (!empty($existingCookie)) {
                $array = json_decode($existingCookie, true);
                $array += $cookieToEncode;
                //dd($array);
                $cookieEncoded = json_encode($array);
            } else {
                $cookieEncoded = json_encode($cookieToEncode);
            }
            $cookie = Cookie::create($user->getId(), $cookieEncoded, time() + 36000);
            $response->headers->setCookie($cookie);
            $response->send();
            //$cookie = $request->cookies->get($user->getId());
            //$response->headers->clearCookie($user->getId());
            //$response->send();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('billing/rentCar.html.twig', [
            'car' => $car,
            'renter' => $renter,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("panier/ajouter/{id}", name="panier_payer")
     */
    public function carPaied($id, SessionInterface $session, BillingRepository $billingRepository,
                             EntityManagerInterface $entityManager)
    {
        $panier = $session->get('panier', []);
        $idCar = $billingRepository->find($id)->getIdCar()->getId();

        if(!empty($panier[$idCar])) {
            $billing = $billingRepository->findOneBy([
                'idCar' => $id,
                'paid' => 0
            ]);
            $billing->setPaid(1);

            $currentQuantity = $billing->getIdCar()->getQuantity();
            if($currentQuantity - $panier[$idCar] == 0)
            {
                $billing->getIdCar()->setQuantity(0);
                $billing->getIdCar()->setRent('indisponible');
            }
            else
                $billing->getIdCar()->setQuantity($currentQuantity - $panier[$idCar]);

            $entityManager->persist($billing);
            $entityManager->flush();

            unset($panier[$idCar]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('panier_client');
    }

    /**
     * @Route("/panier/supprimer/{id}", name="panier_remove")
     */
    public function remove($id, Request $request, SessionInterface $session, BillingRepository $billingRepository,
                           CarRepository  $carRepository, EntityManagerInterface $entityManager): RedirectResponse
    {
        $panier = $session->get('panier', []);
        if(!empty($panier[$id])) {
            $car = $carRepository->find($id);
            $entityManager->remove($billingRepository->findOneBy([
                'idCar' => $car->getId(),
                'paid' => 0
            ]));
            $entityManager->flush();
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        //enlever le cookie
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $cookies = $request->cookies;
        $json = $cookies->get($user->getId());
        //dd($json);
        if (!empty($json)) {
            $arrayCookie = json_decode($json, true);
            //dd($arrayCookie);
            $response = new Response();
            if (sizeof($arrayCookie) == 1) {
                $response->headers->clearCookie($user->getId());
                $response->send();
            } elseif (sizeof($arrayCookie) > 1) {
                unset($arrayCookie[$id]);
                $cookies->remove($user->getId());
                $arrayCookie = json_encode($arrayCookie);
                $cookie = Cookie::create($user->getId(), $arrayCookie, time() + 36000);
                $response->headers->setCookie($cookie);
                $response->send();
            }
        }
        //$response = new Response();
        //$cookie = $request->cookies->get($user->getId());
        //$response->headers->clearCookie($user->getId());
        //$response->send();
        //dd($panier);


        return $this->redirectToRoute('panier_client');
    }

    /**
     * @Route("/dashboard/retournerVoiture/{id}", name="dashboard_return_car")
     */
    public function returnCar(int $id, BillingRepository $billingRepository, CarRepository $carRepository,EntityManagerInterface $manager) {
        $bill = $billingRepository->find($id);
        //dd($bill);
        if ($bill->getPaid()) {
            $bill->setReturned(1);
            $carToReturned = $carRepository->find($bill->getIdCar()->getId());
            if ($carToReturned->getRent() == 'indisponible') {
                $carToReturned->setRent('disponible');
            }
            $carToReturned->setQuantity($carToReturned->getQuantity()+1);
            //dd($carToReturned);
            $manager->persist($bill);
            $manager->flush();
            $manager->persist($carToReturned);
            $manager->flush();
            return $this->render('dashboard/client/myRent.html.twig', [
                'msgSuccess' => 'Voiture retournée avec succès !'
            ]);
        } else {
            return $this->render('dashboard/client/myRent.html.twig', [
                'msgError' => 'Vous ne pouvez pas rendre une voiture non payée.'
            ]);
        }
    }

    /**
     * @Route("/facture/mesClients", name="show_all_client")
     */
    public function showAllClient(BillingRepository $repository) {
        $user = $this->getUser();
        $clients = $repository->findAllClientBy($user->getId()); // à changer
        //dd($clients);
        return $this->render('dashboard/renter/showBill.html.twig', [
            'clients' =>$clients
        ]);
    }

    /**
     * @Route("/facture/parClient/{id}", name="show_bill")
     */
    public function showBill(int $id, BillingRepository $billingRepository, UserRepository $userRepository) {
        $owner = $this->getUser();
        $clientFacture = $userRepository->find($id);
        $clients = $billingRepository->findAllClientBy($owner->getId()); // à changer
        $bills = $billingRepository->findAllBillsOfAClientById($owner->getId(), $id);
        //dd($bills);
        //dd($clients);
        return $this->render('dashboard/renter/showBill.html.twig', [
            'owner' => $owner,
            'clientFacture' => $clientFacture,
            'clients' => $clients,
            'bills' => $bills
        ]);
    }

    /**
     * @Route("/facture/parMois", name="show_month_bill")
     */
    public function showMonthlyBill(BillingRepository $billingRepository, UserRepository $userRepository) {
        $owner = $this->getUser();
        $clients = $billingRepository->findAllClientBy($owner->getId()); // à changer
        $bills = $billingRepository->findAllClientAndTheCarRentedOfTheMonthOwnedBy($owner->getId());
        //dd($bills);
        //dd($clients);
        return $this->render('dashboard/renter/showBill.html.twig', [
            'owner' => $owner,
            'clients' => $clients,
            'bills' => $bills
        ]);
    }
}
