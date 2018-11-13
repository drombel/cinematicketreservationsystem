<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ticket_sold;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Ticket_sold controller.
 *
 * @Route("ticket_sold")
 */
class Ticket_soldController extends Controller
{
    /**
     * Lists all ticket_sold entities.
     *
     * @Route("/", name="ticket_sold_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ticket_solds = $em->getRepository('AppBundle:Ticket_sold')->findAll();

        return $this->render('ticket_sold/index.html.twig', array(
            'ticket_solds' => $ticket_solds,
        ));
    }

    /**
     * Creates a new ticket_sold entity.
     *
     * @Route("/new", name="ticket_sold_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ticket_sold = new Ticket_sold();
        $form = $this->createForm('AppBundle\Form\Ticket_soldType', $ticket_sold);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket_sold);
            $em->flush();

            return $this->redirectToRoute('ticket_sold_show', array('id' => $ticket_sold->getId()));
        }

        return $this->render('ticket_sold/new.html.twig', array(
            'ticket_sold' => $ticket_sold,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ticket_sold entity.
     *
     * @Route("/{id}", name="ticket_sold_show")
     * @Method("GET")
     */
    public function showAction(Ticket_sold $ticket_sold)
    {
        $deleteForm = $this->createDeleteForm($ticket_sold);

        return $this->render('ticket_sold/show.html.twig', array(
            'ticket_sold' => $ticket_sold,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ticket_sold entity.
     *
     * @Route("/{id}/edit", name="ticket_sold_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Ticket_sold $ticket_sold)
    {
        $deleteForm = $this->createDeleteForm($ticket_sold);
        $editForm = $this->createForm('AppBundle\Form\Ticket_soldType', $ticket_sold);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_sold_edit', array('id' => $ticket_sold->getId()));
        }

        return $this->render('ticket_sold/edit.html.twig', array(
            'ticket_sold' => $ticket_sold,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ticket_sold entity.
     *
     * @Route("/{id}", name="ticket_sold_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Ticket_sold $ticket_sold)
    {
        $form = $this->createDeleteForm($ticket_sold);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ticket_sold);
            $em->flush();
        }

        return $this->redirectToRoute('ticket_sold_index');
    }

    /**
     * Creates a form to delete a ticket_sold entity.
     *
     * @param Ticket_sold $ticket_sold The ticket_sold entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Ticket_sold $ticket_sold)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ticket_sold_delete', array('id' => $ticket_sold->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
