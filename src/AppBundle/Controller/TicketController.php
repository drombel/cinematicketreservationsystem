<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ticket;
use AppBundle\Entity\Cinema_hall_has_Movie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Validator\Constraints\Time;
use \Datetime;
use \DateInterval;

/**
 * Ticket controller.
 *
 * @Route("ticket")
 */
class TicketController extends Controller
{
    /**
     * Ticket reservation
     *
     * @Route("/reservation", name="reservation")
     */
    public function reservationAction(Request $request)
    {
        $id = $request->query->get('num');
        $this->changeTicketStatus($id);
        return $this->redirectToRoute('homepage');
    }

    /**
     * Lists all ticket entities.
     *
     * @Route("/", name="ticket_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $em = $this->getDoctrine()->getManager();
        $tickets = $em->getRepository('AppBundle:Ticket')->findAll();

        return $this->render('ticket/index.html.twig', array(
            'tickets' => $tickets,
        ));
    }

    /**
     * Step 1 of tickets reservation.
     *
     * @Route("/step-1", name="ticket_step_1")
     * @Method({"POST"})
     */
    public function stepOneAction(Request $request)
    {
        // o ktorej seans

        $cinemaId = $request->request->getInt("cinemaId");
        $movieId = $request->request->getInt("movieId");

        $data = [];
        if ($cinemaId>0 && $movieId>0){
            //godziny do wyboru
            $em = $this->getDoctrine()->getManager();

            $repo = $em->getRepository('AppBundle:Ticket');
            $cinemaHall_has_Movie = $repo->findAllMoviesByMovieIdAndCinemaId($movieId, $cinemaId);

            $data['Cinema_hall_has_Movie'] = $cinemaHall_has_Movie;
        }else{
            echo "Błędne dane";
        }
        return $this->render('ticket/step_one.html.twig', $data);
    }

    /**
     * Step 2 of tickets reservation.
     *
     * @Route("/step-2", name="ticket_step_2")
     * @Method({"POST"})
     */
    public function stepTwoAction(Request $request)
    {
        // kiedy seans

        $cinemaId = $request->request->getInt("cinemaId");
        $movieId = $request->request->getInt("movieId");
        $cinemaHallHasMovieId = $request->request->getInt("cinemaHallHasMovieId");

        $data = [];
        if ($cinemaId>0 && $movieId>0 && $cinemaHallHasMovieId>0){
            //dni do wyboru max tydzien do przodu, od dzisiejszej daty do +7 chyba ze data koncwa jest mniejsza
            $em = $this->getDoctrine()->getManager();

            $repoCHHM = $em->getRepository('AppBundle:Cinema_hall_has_Movie');
            $Cinema_hall_has_Movie = $repoCHHM->find($cinemaHallHasMovieId);

            $now = new DateTime();
            $dateBeginMovieToday = new DateTime($now->format('Y-m-d')." ".$Cinema_hall_has_Movie->getTimeMovieStart()->format('H:i'));
            $dateEndMovie = new DateTime($Cinema_hall_has_Movie->getTimeEnd()->format('Y-m-d')." ".$Cinema_hall_has_Movie->getTimeMovieStart()->format('H:i'));
            $dates = [];
            $week = 7;

            // sprawdzenie czy dzisiaj jeszcze bedzie grane
            if ($now < $dateBeginMovieToday){
                $dates[] = new DateTime($now->format('Y-m-d'));
            }

            // tworzenie listy dat
            for ($i = count($dates); $i < $week; $i++){
                $now->add(new DateInterval('P1D')); // dodaje 1 dzien
                // sprawdza czy aktualnie sprawdzana data jest mniejsza od makymalnej tego filmu
                if($now < $dateEndMovie){ $dates[] = new DateTime($now->format('Y-m-d')); }
                else{ break; }
            }

            $data['dates'] = $dates;
        }else{
            echo "Błędne dane";
        }
        return $this->render('ticket/step_two.html.twig', $data);
    }

    /**
     * Step 3 of tickets reservation.
     *
     * @Route("/step-3", name="ticket_step_3")
     * @Method({"POST"})
     */
    public function stepThreeAction(Request $request)
    {

        $cinemaId = $request->request->getInt("cinemaId");
        $movieId = $request->request->getInt("movieId");
        $cinemaHallHasMovieId = $request->request->getInt("cinemaHallHasMovieId");
        $date = new DateTime($request->request->get("date"));

        if ( $cinemaId > 0 && $movieId > 0 && $cinemaHallHasMovieId > 0 ){
            //siedzenia i email
            $em = $this->getDoctrine()->getManager();

            $repoCHHM = $em->getRepository('AppBundle:Cinema_hall_has_Movie');
            $Cinema_hall_has_Movie = $repoCHHM->find($cinemaHallHasMovieId);
            $cinemaHallId = $Cinema_hall_has_Movie->getCinemaHallId();

            if (!$this->isDateAllowed($Cinema_hall_has_Movie, $date)){
                echo 'niepoprawna data';
                exit();// zmienic ta wiadomosc na cos sensownego // later
            }

            $seats = $this->getSeats($cinemaHallId);
            $seatsTaken = $this->getSeatsTaken($cinemaHallHasMovieId, $date);
            $seatsSorted = $this->sortSeats($seats, $seatsTaken);

            $data = ['seatsSorted'=>$seatsSorted];
        }else{
            echo "Błędne dane";
        }

        $securityContext = $this->container->get('security.authorization_checker');
        $data['isLogged'] = false;

        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) $data['isLogged'] = true;

        return $this->render('ticket/step_three.html.twig', $data);
    }

    /**
     * Step 4 of tickets reservation.
     *
     * @Route("/step-4", name="ticket_step_4")
     * @Method({"POST"})
     */
    public function stepFourAction(Request $request, \Swift_Mailer $mailer)
    {

        $cinemaId = $request->request->getInt("cinemaId");
        $movieId = $request->request->getInt("movieId");
        $cinemaHallHasMovieId = $request->request->getInt("cinemaHallHasMovieId");
        $date = new DateTime($request->request->get("date"));
        $seatIds = $request->request->get("seats");
        $email = $request->request->get("email");
        $name = $request->request->get("name");
        $surname = $request->request->get("surname");

        $amountOfSeats = count($seatIds);
        $seatIds = is_array($seatIds)?$seatIds:[$seatIds];

        foreach ($seatIds as $key=>$seat){
            $value = intval($seat);
            if($value > 0 && is_numeric($value))
            {$seatIds[$key] = $value;}
            else{unset($seatIds[$key]);}
        }

        $data = [];

        if (
            $cinemaId > 0 &&
            $movieId > 0 &&
            $cinemaHallHasMovieId > 0 &&
            $amountOfSeats == count($seatIds) &&
            filter_var($email, FILTER_VALIDATE_EMAIL)
        ){
            //caly ticket i wysylanie maila
            $em = $this->getDoctrine()->getManager();
            $flag = true;

            $repoCHHM = $em->getRepository('AppBundle:Cinema_hall_has_Movie');
            $Cinema_hall_has_Movie = $repoCHHM->find($cinemaHallHasMovieId);

            if (!$this->isDateAllowed($Cinema_hall_has_Movie, $date)){
                $flag = false;
            }

            $seatsTaken = $this->getSeatsTaken($cinemaHallHasMovieId, $date);
            foreach ($seatIds as $seat){
                if(in_array($seat, $seatsTaken)) $flag = false;
            }

            $ticket = new Ticket();
            $ticket->setCinemaHallHasMovieId($cinemaHallHasMovieId);
            $ticket->setEmail($email);
            $ticket->setSeatId($seatIds);
            if($name == '' && $surname == '') {
                $userId = $this->get('security.token_storage')->getToken()->getUser();
                $ticket->setUserId($userId);
            }
            $ticket->setStatus('Pending');
            $ticket->setDate($date);

            if($flag == true){
                $em->persist($ticket);
                $em->flush();
                $this->sendEmail($email, $cinemaId, $movieId, $seatIds, $cinemaHallHasMovieId, $name, $surname, $ticket->getId(), $mailer);
            }

            $data['flag'] = $flag;

        }else{
            echo "Błędne dane";
            exit();
        }

        return $this->render('ticket/step_four.html.twig', $data);
        // potem wszystko sie pozmienia zeby wygladalo fancy,

        // jeszcze gdzies maila wstawic i bedzie bilet, wtedy to zabezpieczenia i rzeczy panelowe
    }

    /**
     * Creates a new ticket entity.
     *
     * @Route("/new", name="ticket_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $ticket = new Ticket();
        $form = $this->createForm('AppBundle\Form\TicketType', $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();

            return $this->redirectToRoute('ticket_show', array('id' => $ticket->getId()));
        }

        return $this->render('ticket/new.html.twig', array(
            'ticket' => $ticket,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ticket entity.
     *
     * @Route("/{id}", name="ticket_show")
     * @Method("GET")
     */
    public function showAction(Ticket $ticket)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $deleteForm = $this->createDeleteForm($ticket);

        return $this->render('ticket/show.html.twig', array(
            'ticket' => $ticket,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ticket entity.
     *
     * @Route("/{id}/edit", name="ticket_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Ticket $ticket)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $deleteForm = $this->createDeleteForm($ticket);
        $editForm = $this->createForm('AppBundle\Form\TicketType', $ticket);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_edit', array('id' => $ticket->getId()));
        }

        return $this->render('ticket/edit.html.twig', array(
            'ticket' => $ticket,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ticket entity.
     *
     * @Route("/{id}", name="ticket_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Ticket $ticket)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $form = $this->createDeleteForm($ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ticket);
            $em->flush();
        }

        return $this->redirectToRoute('ticket_index');
    }

    /**
     * Creates a form to delete a ticket entity.
     *
     * @param Ticket $ticket The ticket entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Ticket $ticket)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ticket_delete', array('id' => $ticket->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function sortSeats($seats, $seatsTaken = []){
        $seatsSorted = [];
        foreach($seats as $seat){
            $row = $seat->getRow();
            $col = $seat->getCol();
            if(in_array( $seat->getId(), $seatsTaken)) $seat->taken = true;
            $seatsSorted[$row] = ( isset($seatsSorted[$row]) && is_array($seatsSorted[$row]) )?$seatsSorted[$row]:[];
            $seatsSorted[$row][$col] = $seat;
        }
        return $seatsSorted;
    }

    private function getSeatsTaken($cinemaHallHasMovieId, $date){
        $em = $this->getDoctrine()->getManager();
        $repoTicket = $em->getRepository('AppBundle:Ticket');
        $tickets = $repoTicket->findBy([
            'cinemaHallHasMovieId' => $cinemaHallHasMovieId,
            'status' => ['Pending','Ok'],
            'date' => $date]
        );
        $seatsTaken = [];
        foreach ($tickets as $ticket){
            foreach($ticket->getSeatId() as $seatId)
                $seatsTaken[] = $seatId;
        }
        return $seatsTaken;
    }

    private function getSeats($cinemaHallId){
        $em = $this->getDoctrine()->getManager();
        $repoSeat = $em->getRepository('AppBundle:Seat');
        $seats = $repoSeat->findBy(['cinema_hallId'=>$cinemaHallId]);
        return $seats;
    }

    private function sendEmail($email, $cinemaId, $movieId, $seatIds, $cinemaHallHasMovieId, $name, $surname, $ticket, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $cinema = $em->getRepository('AppBundle:Cinema')->find($cinemaId);
        $movie = $em->getRepository('AppBundle:Movie')->find($movieId);
        $chm = $em->getRepository('AppBundle:Cinema_hall_has_Movie')->find($cinemaHallHasMovieId);
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));
        $cinemaHall = $chm->getCinemaHallId();
        $cinemaHallNumber = $em->getRepository('AppBundle:Cinema_hall')->find($cinemaHall);

        $seats = [];

        foreach ($seatIds as $seatId) {
            $seatId += 10;
            $seats[] = (string)$seatId;
        }
        $numberOfSeats = count($seats);

        $time = date('H:i:s');
        $time = strtotime($time) + 1800;
        $time = date('H:i:s', $time);

        $data = [
            'name' => $name!=''?$name:$user->getName(),
            'surname' => $surname!=''?$surname:$user->getName(),
            'cinema' => $cinema,
            'movie' => $movie,
            'chm' => $chm->getTimeMovieStart()->format('H:i:s'),
            'cinemaHall' => $cinemaHallNumber,
            'seats' => $seats,
            'numberOfSeats' => $numberOfSeats,
            'time' => $time,
            'ticket' => $ticket
        ];

        $message = (new \Swift_Message('Rezerwacja biletu - TicketManiac'))
            ->setFrom('ticketmaniac2018@gmail.com')
            ->setTo($email)
            ->setBody(
                $this->renderView('Emails/ticket.html.twig', $data),
                'text/html');

        $mailer->send($message);
    }

    public function changeTicketStatus($id)
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->findOneBy(array('id' => $id));
        $ticket->setStatus('Ok');
        $this->getDoctrine()->getManager()->flush();
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

    private function isDateAllowed($Cinema_hall_has_Movie, $date){
        $dateBeginMovie = $Cinema_hall_has_Movie->getTimeStart();
        $dateEndMovie = new DateTime($Cinema_hall_has_Movie->getTimeEnd()->format('Y-m-d')." ".$Cinema_hall_has_Movie->getTimeMovieStart()->format('H:i'));
        return ($dateBeginMovie < $date && $date < $dateEndMovie); // albo z negacja
    }
}
