<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cinema;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Cinema controller.
 *
 * @Route("cinema")
 */
class CinemaController extends Controller
{
    /**
     * Lists all cinema entities.
     *
     * @Route("/", name="cinema_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cinemas = $em->getRepository('AppBundle:Cinema')->findAll();

        return $this->render('cinema/index.html.twig', array(
            'cinemas' => $cinemas,
        ));
    }

    /**
     * Lists all cinema entities.
     *
     * @Route("/all", name="cinema_all_index")
     * @Method("GET")
     */
    public function allAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cinemas = $em->getRepository('AppBundle:Cinema')->findAllWithCity();

        return $this->render('cinema/index_all.html.twig', array(
            'cinemas' => $cinemas,
        ));
    }

    /**
     * Creates a new cinema entity.
     *
     * @Route("/new", name="cinema_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cinema = new Cinema();
        $form = $this->createForm('AppBundle\Form\CinemaType', $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cinema);
            $em->flush();

            return $this->redirectToRoute('cinema_show', array('id' => $cinema->getId()));
        }

        return $this->render('cinema/new.html.twig', array(
            'cinema' => $cinema,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cinema entity.
     *
     * @Route("/{id}", name="cinema_show")
     * @Method("GET")
     */
    public function showAction(Cinema $cinema)
    {
        $deleteForm = $this->createDeleteForm($cinema);

        return $this->render('cinema/show.html.twig', array(
            'cinema' => $cinema,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cinema entity.
     *
     * @Route("/{id}/edit", name="cinema_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cinema $cinema)
    {
        $deleteForm = $this->createDeleteForm($cinema);
        $editForm = $this->createForm('AppBundle\Form\CinemaType', $cinema);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cinema_edit', array('id' => $cinema->getId()));
        }

        return $this->render('cinema/edit.html.twig', array(
            'cinema' => $cinema,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }



    /**
     * Deletes a cinema entity.
     *
     * @Route("/{id}", name="cinema_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Cinema $cinema)
    {
        $form = $this->createDeleteForm($cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cinema);
            $em->flush();
        }

        return $this->redirectToRoute('cinema_index');
    }



    /**
     * Creates a form to delete a cinema entity.
     *
     * @param Cinema $cinema The cinema entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cinema $cinema)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cinema_delete', array('id' => $cinema->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
