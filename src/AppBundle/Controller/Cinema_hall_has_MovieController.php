<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cinema_hall_has_Movie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Cinema_hall_has_movie controller.
 *
 * @Route("cinema_hall_has_movie")
 */
class Cinema_hall_has_MovieController extends Controller
{
    /**
     * Lists all cinema_hall_has_Movie entities.
     *
     * @Route("/", name="cinema_hall_has_movie_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cinema_hall_has_Movies = $em->getRepository('AppBundle:Cinema_hall_has_Movie')->findAll();

        return $this->render('cinema_hall_has_movie/index.html.twig', array(
            'cinema_hall_has_Movies' => $cinema_hall_has_Movies,
        ));
    }

    /**
     * Creates a new cinema_hall_has_Movie entity.
     *
     * @Route("/new", name="cinema_hall_has_movie_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cinema_hall_has_Movie = new Cinema_hall_has_movie();
        $form = $this->createForm('AppBundle\Form\Cinema_hall_has_MovieType', $cinema_hall_has_Movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cinema_hall_has_Movie);
            $em->flush();

            return $this->redirectToRoute('cinema_hall_has_movie_show', array('id' => $cinema_hall_has_Movie->getId()));
        }

        return $this->render('cinema_hall_has_movie/new.html.twig', array(
            'cinema_hall_has_Movie' => $cinema_hall_has_Movie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cinema_hall_has_Movie entity.
     *
     * @Route("/{id}", name="cinema_hall_has_movie_show")
     * @Method("GET")
     */
    public function showAction(Cinema_hall_has_Movie $cinema_hall_has_Movie)
    {
        $deleteForm = $this->createDeleteForm($cinema_hall_has_Movie);

        return $this->render('cinema_hall_has_movie/show.html.twig', array(
            'cinema_hall_has_Movie' => $cinema_hall_has_Movie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cinema_hall_has_Movie entity.
     *
     * @Route("/{id}/edit", name="cinema_hall_has_movie_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cinema_hall_has_Movie $cinema_hall_has_Movie)
    {
        $deleteForm = $this->createDeleteForm($cinema_hall_has_Movie);
        $editForm = $this->createForm('AppBundle\Form\Cinema_hall_has_MovieType', $cinema_hall_has_Movie);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cinema_hall_has_movie_edit', array('id' => $cinema_hall_has_Movie->getId()));
        }

        return $this->render('cinema_hall_has_movie/edit.html.twig', array(
            'cinema_hall_has_Movie' => $cinema_hall_has_Movie,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a cinema_hall_has_Movie entity.
     *
     * @Route("/{id}", name="cinema_hall_has_movie_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Cinema_hall_has_Movie $cinema_hall_has_Movie)
    {
        $form = $this->createDeleteForm($cinema_hall_has_Movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cinema_hall_has_Movie);
            $em->flush();
        }

        return $this->redirectToRoute('cinema_hall_has_movie_index');
    }

    /**
     * Creates a form to delete a cinema_hall_has_Movie entity.
     *
     * @param Cinema_hall_has_Movie $cinema_hall_has_Movie The cinema_hall_has_Movie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cinema_hall_has_Movie $cinema_hall_has_Movie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cinema_hall_has_movie_delete', array('id' => $cinema_hall_has_Movie->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
