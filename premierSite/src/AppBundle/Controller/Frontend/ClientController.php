<?php

namespace AppBundle\Controller\Frontend;

use AppBundle\Entity\Client;
use AppBundle\Form\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @Route("/clients", name="client_")
 */
class ClientController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        if ( $this->isGranted("ROLE_SUPER_ADMIN") ) {
            $clients = $em->getRepository('AppBundle:Client')->findAll();
        } else {
            $clients = $em->getRepository('AppBundle:Client')->findBy(array('creator' => $this->getUser()));
        }


        return $this->render('front/client/index.html.twig', array(
            'clients' => $clients,
        ));
    }

    /**
     * @Route("/new", name="new")
     */
    public function newAction(Request $request)
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $client->setCreator($this->getUser());
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('client_show', array('id' => $client->getId()));
        }

        return $this->render('front/client/new.html.twig', array(
            'client' => $client,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function showAction(Client $client)
    {
        $this->denyAccessUnlessGranted('client_show', $client);
        return $this->render('front/client/show.html.twig', array(
            'client' => $client));
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function editAction(Request $request,Client $client)
    {
        $this->denyAccessUnlessGranted('client_edit', $client);
        $editForm = $this->createForm('AppBundle\Form\ClientType', $client);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_index', array('id' => $client->getId()));
        }

        return $this->render('front/client/edit.html.twig', array(
            'client' => $client,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Client $client, UploadHandler $uploadHandler)
    {
        $token = $request->query->get('token');

        if ( ! $this->isCsrfTokenValid('delete_client', $token) ) {
            throw new Exception('CSRF attack');
        }
        $uploadHandler->remove($client,'photoFile');
        $em = $this->getDoctrine()->getManager();
        $em->remove($client);
        $em->flush();

        return $this->redirectToRoute('client_index');
    }
}
