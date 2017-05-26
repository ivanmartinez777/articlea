<?php

namespace AppBundle\Controller;


use AppBundle\Entity\RevistaTexto;
use AppBundle\Entity\Texto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;




class RevistaTextoController extends Controller
{

    /**
     * @Route("textosNuevos", name="app_RevistaTexto_textosNuevos")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function textosNuevosRevistaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $revista = $user->getRevista();
        $repositorioRevistaTextos = $em->getRepository('AppBundle:RevistaTexto');
        $textosNuevos=  $repositorioRevistaTextos->numeroNuevosTexto($revista);



        return $this->render(':texto:textoNuevo.html.twig',
            [
                'numero' => $textosNuevos,
                'revista'=> $revista,
            ]
        )
            ;



    }


    /**
     * @Route("addRemove/{id}", name="app_texto_addRemoveRevista")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function addRemoveRevistaAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $revista = $user->getRevista();
        $repo = $em->getRepository('AppBundle:Texto');
        $texto = $repo->findOneBy(array('id'=>$id));
        $repositoryRevistaTexto = $em->getRepository('AppBundle:RevistaTexto');
        $revistaTexto = $repositoryRevistaTexto->buscarPorRevistaTexto($revista,$texto);
        if($revistaTexto == null )
        {
            $revistaTexto = new RevistaTexto();
            $revistaTexto->setTexto($texto);
            $revistaTexto->setRevista($revista);
            $em->persist($revistaTexto);
            $em->flush();
            $this->addFlash('messages', 'Texto añadido a tu revista');

            return $this->redirectToRoute('app_texto_individual', ['id' => $id]);;
        }else{
            $em->remove($revistaTexto);
            $em->flush();



            $this->addFlash('messages', 'Texto eliminado de la revista');

            return $this->redirectToRoute('app_texto_individual', ['id' => $id]);
        }

    }


    /**
     * @Route("/addToRevista/{id}", name="app_texto_addTorevista")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function addToRevistaAction($id)
    {
        $user = $this->getUser();
        $revista = $user->getRevista();
        $em = $this->getDoctrine()->getManager();
        $repositorioTexto = $em->getRepository('AppBundle:Texto');
        $texto = $repositorioTexto->findOneBy(array('id'=>$id));
        $repositorioRevistaTexto = $em->getRepository('AppBundle:RevistaTexto');
        $revistaTexto = $repositorioRevistaTexto->buscarPorRevistaTexto($revista,$texto);
        if($revistaTexto == null)
        {
            $revistaTexto = new RevistaTexto();
            $revistaTexto->setRevista($revista);
            $revistaTexto->setTexto($texto);
            $em = $this->getDoctrine()->getManager();
            $em->persist($revistaTexto);
            $em->flush();
            $this->addFlash('messages', 'Texto añadido a tu revista');
            return $this->redirectToRoute('app_texto_index');

        }else{

            $this->addFlash('messages', 'Este texto ya está actualmente en tu Revista');
            return $this->redirectToRoute('app_texto_index');

        }

    }

    /**
     * @Route("/removeFromRevista/{id}", name="app_texto_removeFromRevista")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function removeFromRevistaAction($id)
    {
        $user = $this->getUser();
        $revista = $user->getRevista();
        $em = $this->getDoctrine()->getManager();
        $repositorioTexto = $em->getRepository('AppBundle:Texto');
        $texto = $repositorioTexto->findOneBy(array('id'=>$id));
        $repositorioRevistaTexto = $em->getRepository('AppBundle:RevistaTexto');
        $revistaTexto = $repositorioRevistaTexto->buscarPorRevistaTexto($revista,$texto);


        $em->remove($revistaTexto);
        $em->flush();



        $this->addFlash('messages', 'Texto eliminado de la revista');
        return $this->redirectToRoute('app_texto_revista');

    }

    /**
     * @Route("/FavRevista/{id}", name="app_texto_favRevista")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function favRevistaAction($id)
    {
        $user = $this->getUser();
        $revista = $user->getRevista();
        $em = $this->getDoctrine()->getManager();
        $repositorioTexto = $em->getRepository('AppBundle:Texto');
        $texto = $repositorioTexto->findOneBy(array('id'=>$id));
        $repositorioRevistaTexto = $em->getRepository('AppBundle:RevistaTexto');
        $revistaTexto = $repositorioRevistaTexto->buscarPorRevistaTexto($revista,$texto);



        if ($revistaTexto == null) {
            $revistaTexto = new RevistaTexto();
            $revistaTexto->setRevista($revista);
            $revistaTexto->setTexto($texto);
            $revistaTexto->setFav(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($revistaTexto);
            $em->flush();
            $this->addFlash('messages', 'Texto añadido a tu revista y añadido a favoritos');
            return $this->redirectToRoute('app_texto_individual', ['id' => $id]);

        } elseif ($revistaTexto->getFav() == true) {
            $revistaTexto->setFav(false);
            $em->persist($revistaTexto);
            $em->flush();
            $this->addFlash('messages', 'Este texto ha dejado de ser de sus favoritos');
            return $this->redirectToRoute('app_texto_individual', ['id' => $id]);

        } else {

            $revistaTexto->setFav(true);
            $em->persist($revistaTexto);
            $em->flush();
            $this->addFlash('messages', 'Este texto ha sido añadido a tus favoritos');
            return $this->redirectToRoute('app_texto_individual', ['id' => $id]);

        }

        return $this->redirectToRoute('app_texto_revista');

    }








}
