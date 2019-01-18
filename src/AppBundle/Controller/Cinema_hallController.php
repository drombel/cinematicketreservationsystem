<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cinema_hall;
use AppBundle\Entity\Seat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Cinema_hall controller.
 *
 * @Route("cinema_hall")
 */
class Cinema_hallController extends Controller
{
    /**
     * Lists all cinema_hall entities.
     *
     * @Route("/", name="cinema_hall_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $em = $this->getDoctrine()->getManager();

        $cinema_halls = $em->getRepository('AppBundle:Cinema_hall')->findAll();
        $repoSeat = $em->getRepository(Seat::class);
        foreach ($cinema_halls as $cinema_hall){
            $cinema_hall->seats = $repoSeat->getAmountOfSeatsByCinemaHall($cinema_hall->getId());
        }

        return $this->render('cinema_hall/index.html.twig', array(
            'cinema_halls' => $cinema_halls,
        ));
    }

    /**
     * Setting amount of seats in cinema_hall entity.
     *
     * @Route("/set_seats", name="cinema_hall_set_seats")
     * @Method({"GET", "POST"})
     */
    public function setSeatsAction(Request $request)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $form = $this->createForm('AppBundle\Form\SetSeatsType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->get('set_seats');
            $rows = intval($data['rows']);
            $cols = intval($data['cols']);
            $cinemaHallId = $request->query->getInt('id');
            if($rows > 0 && $cols > 0  && $cinemaHallId > 0){

                $em = $this->getDoctrine()->getManager();
                $cinemaHall = $em->find( Cinema_hall::class, $cinemaHallId);
                $repoSeat = $em->getRepository(Seat::class);
                $emSeat = $this->getDoctrine()->getManagerForClass('AppBundle:Seat');
                $repoSeat->deleteSeatsByCinemaHall($cinemaHallId);
                for ($rowIndex = 1; $rowIndex <= $rows; $rowIndex++) {
                    for ($colIndex = 1; $colIndex <= $cols; $colIndex++) {
                        $seat = new Seat();
                        $seat->setcinema_hallId($cinemaHall);
                        $seat->setRow($rowIndex);
                        $seat->setCol($colIndex);
                        $emSeat->persist($seat);
                    }
                }
                $emSeat->flush();
            }
            return $this->redirectToRoute('cinema_hall_index');
        }

        return $this->render('cinema_hall/setSeats.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new cinema_hall entity.
     *
     * @Route("/new", name="cinema_hall_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $cinema_hall = new Cinema_hall();
        $form = $this->createForm('AppBundle\Form\Cinema_hallType', $cinema_hall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cinema_hall);
            $em->flush();

            return $this->redirectToRoute('cinema_hall_show', array('id' => $cinema_hall->getId()));
        }

        return $this->render('cinema_hall/new.html.twig', array(
            'cinema_hall' => $cinema_hall,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cinema_hall entity.
     *
     * @Route("/{id}", name="cinema_hall_show")
     * @Method("GET")
     */
    public function showAction(Cinema_hall $cinema_hall)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $deleteForm = $this->createDeleteForm($cinema_hall);

        return $this->render('cinema_hall/show.html.twig', array(
            'cinema_hall' => $cinema_hall,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cinema_hall entity.
     *
     * @Route("/{id}/edit", name="cinema_hall_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cinema_hall $cinema_hall)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $deleteForm = $this->createDeleteForm($cinema_hall);
        $editForm = $this->createForm('AppBundle\Form\Cinema_hallType', $cinema_hall);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cinema_hall_edit', array('id' => $cinema_hall->getId()));
        }

        return $this->render('cinema_hall/edit.html.twig', array(
            'cinema_hall' => $cinema_hall,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a cinema_hall entity.
     *
     * @Route("/{id}", name="cinema_hall_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Cinema_hall $cinema_hall)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');
        $form = $this->createDeleteForm($cinema_hall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cinema_hall);
            $em->flush();
        }

        return $this->redirectToRoute('cinema_hall_index');
    }

    /**
     * Creates a form to delete a cinema_hall entity.
     *
     * @param Cinema_hall $cinema_hall The cinema_hall entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cinema_hall $cinema_hall)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cinema_hall_delete', array('id' => $cinema_hall->getId())))
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
