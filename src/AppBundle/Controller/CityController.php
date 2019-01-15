<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * City controller.
 *
 * @Route("city")
 */
class CityController extends Controller
{
    /**
     * Lists all city entities.
     *
     * @Route("/", name="city_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if($hasAccess) {
            $em = $this->getDoctrine()->getManager();

            $cities = $em->getRepository('AppBundle:City')->findAll();

            return $this->render('city/index.html.twig', array(
                'cities' => $cities,
            ));
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Creates a new city entity.
     *
     * @Route("/new", name="city_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if($hasAccess) {
            $city = new City();
            $form = $this->createForm('AppBundle\Form\CityType', $city);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($city);
                $em->flush();

                return $this->redirectToRoute('city_show', array('id' => $city->getId()));
            }

            return $this->render('city/new.html.twig', array(
                'city' => $city,
                'form' => $form->createView(),
            ));
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Finds and displays a city entity.
     *
     * @Route("/{id}", name="city_show")
     * @Method("GET")
     */
    public function showAction(City $city)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if($hasAccess) {
            $deleteForm = $this->createDeleteForm($city);

            return $this->render('city/show.html.twig', array(
                'city' => $city,
                'delete_form' => $deleteForm->createView(),
            ));
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Displays a form to edit an existing city entity.
     *
     * @Route("/{id}/edit", name="city_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, City $city)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if($hasAccess) {
            $deleteForm = $this->createDeleteForm($city);
            $editForm = $this->createForm('AppBundle\Form\CityType', $city);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('city_edit', array('id' => $city->getId()));
            }

            return $this->render('city/edit.html.twig', array(
                'city' => $city,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Deletes a city entity.
     *
     * @Route("/{id}", name="city_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, City $city)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if($hasAccess) {
            $form = $this->createDeleteForm($city);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($city);
                $em->flush();
            }

            return $this->redirectToRoute('city_index');
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Creates a form to delete a city entity.
     *
     * @param City $city The city entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(City $city)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('city_delete', array('id' => $city->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function hasAccess($loggedUserRole)
    {
        if($loggedUserRole !== 'anon.') {
            if($loggedUserRole->getRole() === 'admin') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
