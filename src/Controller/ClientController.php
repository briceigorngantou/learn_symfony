<?php

namespace App\Controller;

use App\Entity\Clients;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client/new', name: 'client_new')]
    public function createAction(Request $request, EntityManagerInterface $em): Response
    {
        $client = new Clients();

        $form = $this->createFormBuilder($client)
            ->add('name', TextType::class, ['required' => true, 'label' => 'Nom'])
            ->add('fullname', TextType::class, ['required' => true, 'label' => 'Prenom'])
            ->add('email', EmailType::class, ['required' => true, 'label' => 'Email'])
            ->add('phone', TextType::class, ['required' => true, 'label' => 'Pmail'])
            ->add('save', SubmitType::class, ['label' => 'Valider'])
            ->getForm();

        $form->handleRequest($request);

        $already_exist = $em->getRepository(Clients::class)
            ->findOneBy([
                'email' => $client->getEmail(),
                'deleted' => false
            ]);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$already_exist) {
                $client->setDeleted(false);
                $client->setCreatedAt(new \DateTimeImmutable());
                $clients = $form->getData();
                $em->persist($clients);
                $em->flush();

                echo 'Envoyé';
                return $this->showAction($em);
            } else {
                throw $this->createNotFoundException(
                    'Client existe deja'
                );
            }
        }
        return $this->render('client/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/client/{id}', name: 'client_view', methods: ['GET'])]
    public function viewAction(EntityManagerInterface $em, $id)
    {
        $client = $em->getRepository(Clients::class);
        $client = $client->findOneBy(['id' => $id, 'deleted' => false]);

        if (!$client) {
            throw $this->createNotFoundException(
                'Aucun client pour l\'id: ' . $id
            );
        }

        return $this->render(
            'client/view.html.twig',
            array('client' => $client)
        );

    }


    #[Route('/client', name: 'clients_all')]
    public function showAction(EntityManagerInterface $em): Response
    {

        $client = $em->getRepository(Clients::class);
        $client = $client->findBy(['deleted' => false]);

        return $this->render(
            'client/list.html.twig',
            array('clients' => $client)
        );
    }


    #[Route('/client/delete/{id}', name: 'client_delete')]
    public function deleteAction(EntityManagerInterface $em, $id): Response
    {

        $client = $em->getRepository(Clients::class);
        $client = $client->findOneBy(['id' => $id, 'deleted' => false]);

        if (!$client) {
            throw $this->createNotFoundException(
                'There are no clients with the following id: ' . $id
            );
        }
        $client->setDeleted(true);
        $em->persist($client);
        // $em->remove($client);
        $em->flush();

        return $this->redirect($this->generateUrl('clients_all'));

    }


    #[Route('/client/edit/{id}', name: 'client_edit')]
    public function updateAction(Request $request, EntityManagerInterface $em, $id): Response
    {
        $client = $em->getRepository(Clients::class);
        $client = $client->findOneBy(['id' => $id, 'deleted' => false]);

        if (!$client) {
            throw $this->createNotFoundException(
                'There are no clients with the following id: ' . $id
            );
        }

        $form = $this->createFormBuilder($client)
            ->add('name', TextType::class)
            ->add('fullname', TextType::class)
            ->add('email', TextType::class)
            ->add('phone', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Editer'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $client = $form->getData();
            $em->flush();

            return $this->redirect($this->generateUrl('clients_all'));

        }

        return $this->render(
            'client/edit.html.twig',
            array('form' => $form->createView())
        );

    }
}