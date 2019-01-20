<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Movie;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $movies = $em->getRepository('AppBundle:Movie')->findBy([],['dateAdd'=>'ASC']);
        $movies = $this->setImages($movies);

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'movies' => $movies
        ]);
    }

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

}
