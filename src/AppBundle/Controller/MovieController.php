<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Movie;
use AppBundle\Entity\Cinema;
use AppBundle\Service\FileUploader;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * Movie controller.
 *
 * @Route("movie")
 */
class MovieController extends Controller
{
    /**
     * Lists all movie entities.
     *
     * @Route("/", name="movie_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();

        if($loggedUserRole !== 'client') {

            $em = $this->getDoctrine()->getManager();

            $movies = $em->getRepository('AppBundle:Movie')->findAll();

            $movies = $this->setImages($movies);

            return $this->render('movie/index.html.twig', array(
                'movies' => $movies,
            ));
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Lists all movie entities by cinemaId.
     *
     * @Route("/movies_by_cinema/{id}", name="movies_by_cinema_list")
     * @Method("GET")
     */
    public function moviesByCinemaAction(Cinema $cinema)
    {
        $em = $this->getDoctrine()->getManager();

        $movies = $em->getRepository('AppBundle:Movie')->findAllByCinema($cinema->getId());

        $movies = $this->setImages($movies);

        return $this->render('movie/index_movies_by_cinema.html.twig', array(
            'movies' => $movies,
            'cinema' => $cinema,
        ));
    }

    /**
     * movie for user
     *
     * @Route("/movie_by_cinema/{movie_id}/{cinema_id}", name="movie_by_cinema")
     * @Method("GET")
     */
    public function movieByCinemaAction(Request $request)
    {
        $movieId = $request->attributes->get('movie_id');
        $cinemaId = $request->attributes->get('cinema_id');

        $em = $this->getDoctrine()->getManager();
        $movie = $em->find(Movie::class, ['id'=>$movieId]);
        $cinema = $em->find(Cinema::class, $cinemaId);

        $movie = $this->setImages($movie)[0];
        $moviePriceDisc = $movie->getPrice()*0.8;

        return $this->render('movie/index_movie_by_cinema.html.twig', array(
            'movie' => $movie,
            'cinema' => $cinema,
            'moviePriceDisc' => $moviePriceDisc
        ));
    }

    /**
     * Creates a new movie entity.
     *
     * @Route("/new", name="movie_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();

        if($loggedUserRole !== 'client') {
            $movie = new Movie();
            $form = $this->createForm('AppBundle\Form\MovieType', $movie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $currentImage = $movie->getPoster();
                if ($currentImage) {
                    $orginalUploader = new FileUploader($this->getParameter('movie_images_poster_directory'));
                    $movie->setPoster($orginalUploader->upload($currentImage));
                }

                $currentImage = $movie->getScene();
                if ($currentImage) {
                    $extraUploader = new FileUploader($this->getParameter('movie_images_scene_directory'));
                    $movie->setScene($extraUploader->upload($currentImage));
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($movie);
                $em->flush();

                return $this->redirectToRoute('movie_show', array('id' => $movie->getId()));
            }

            return $this->render('movie/new.html.twig', array(
                'movie' => $movie,
                'form' => $form->createView(),
            ));
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Finds and displays a movie entity.
     *
     * @Route("/{id}", name="movie_show")
     * @Method("GET")
     */
    public function showAction(Movie $movie)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();

        if($loggedUserRole !== 'client') {
            $deleteForm = $this->createDeleteForm($movie);
            //$images = $this->getImages($movie);

            $movie = $this->setImages($movie)[0];

            return $this->render('movie/show.html.twig', array(
                'movie' => $movie,
                'delete_form' => $deleteForm->createView(),
            ));
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Displays a form to edit an existing movie entity.
     *
     * @Route("/{id}/edit", name="movie_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Movie $movie)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();

        if($loggedUserRole !== 'client') {
            $images = $this->getImages($movie);

            if (isset($images['poster'])) {
                $file = new File($images['poster']['serverPath']);
                $file->image_property = $images['poster']['webPath'];
                $movie->setPoster($file);
            }

            if (isset($images['scene'])) {
                $file = new File($images['scene']['serverPath']);
                $file->image_property = $images['scene']['webPath'];
                $movie->setScene($file);
            }

            $deleteForm = $this->createDeleteForm($movie);
            $editForm = $this->createForm('AppBundle\Form\MovieType', $movie);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('movie_edit', array('id' => $movie->getId()));
            }

            return $this->render('movie/edit.html.twig', array(
                'movie' => $movie,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Deletes a movie entity.
     *
     * @Route("/{id}", name="movie_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Movie $movie)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();

        if($loggedUserRole !== 'client') {
            $form = $this->createDeleteForm($movie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($movie);
                $em->flush();
            }

            return $this->redirectToRoute('movie_index');
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Creates a form to delete a movie entity.
     *
     * @param Movie $movie The movie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Movie $movie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('movie_delete', array('id' => $movie->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Creates array which contains full server path, full web path and file name.
     *
     * @param Movie $movie The movie entity
     *
     * @return array
     */
    private function getImages(Movie $movie){
        $images = [
            'poster' => $movie->getPoster(),
            'scene' => $movie->getScene(),
        ];

        foreach($images as $key => $image){

            if($image){
                $arr = [];
                $arr['fileName'] = $image;
                $arr['serverPath'] = $this->getParameter('movie_images_'.$key.'_directory').$image;
                $arr['webPath'] = $this->getParameter('movie_images_'.$key.'_web_directory').$image;
                $images[$key] = $arr;
            }else{
                unset($images[$key]);
            }
        }
        return $images;
    }

    /**
     * sets images url.
     *
     * @param Movie $movie The movie entity
     *
     * @return array
     */
    private function setImages($movies){
        $movies = is_array($movies)?$movies:[$movies];

        foreach ($movies as $key => $movie){
            $images = $this->getImages($movie);

            if(isset($images['poster']))
                $movies[$key]->setPoster($images['poster']['webPath']);

            if(isset($images['scene']))
                $movies[$key]->setScene($images['scene']['webPath']);
        }
        return $movies;
    }


}
