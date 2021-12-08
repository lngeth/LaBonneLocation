<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $contactData = $form->getData();

            $message = (new Email())
                ->from($contactData['email'])
                ->to('ibrahime.ahbib@etu.u-paris.fr')
                ->subject("Vous avez reçu un mail de la part de " . $contactData['full_name'])
                ->text('Envoyeur : ' . $contactData['email'] . \PHP_EOL .
                    $contactData['message'],
                    'text/plain');

            $mailer->send($message);


            $this->addFlash('success', 'Votre message a bien été envoyée !');

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'contact_form' => $form->createView()
        ]);
    }
}
