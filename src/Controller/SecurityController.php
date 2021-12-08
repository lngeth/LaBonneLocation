<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\BillingRepository;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class   SecurityController extends AbstractController
{
    /**
     * @Route("/connexion", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     */
    public function login(AuthenticationUtils $authenticationUtils) {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/inscription", name="security_registration")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasher $encoder
     * @return Response
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $encoder) {
        $user = new User();
        //on relie les champs du formulaire avec ceux de l'utilisateur
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->hashPassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Vous êtes bien inscrit, merci de rentrez à nouveau vos identifiants');
            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/suppressionPanier", name="destroy_bag")
     */
    public function destroyShoppingBag(SessionInterface $session, BillingRepository $billingRepository, EntityManagerInterface $entityManager) {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $panier = $session->get('panier', []);
        if(!empty($panier)) {
            foreach ($panier as $key => $value) {
                $bill = $billingRepository->findOneBy([
                    'idCar' => $key,
                    'idUser' => $user->getId(),
                    'paid' => 0
                ]);
                $entityManager->remove($bill);
                $entityManager->flush();
                $bill = $billingRepository->findOneBy([
                    'idCar' => $key,
                    'idUser' => $user->getId(),
                    'paid' => 0
                ]);
                unset($panier[$key]);
            }
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute('security_logout');
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){}

}
