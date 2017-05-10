<?php

namespace AppBundle\Controller;


use Trascastro\UserBundle\Entity;
use AppBundle\Form\ImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;





class UsuarioController extends Controller
{
    /**
     * @Route("/suscripcion/{usuario}", name="app_usuario_suscripcion")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function suscribeAction($usuario)
    {
        $user = $this->getUser();
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('UserBundle:User');
        $suscripcion = $repo->myFindOneByUsernameOrEmail($usuario);
        $user->addSuscripcion($suscripcion);
        $m->flush();
        $this->addFlash('messages', $user->getUsername() . " se ha suscrito a " .$suscripcion->getUsername());
        return $this->redirectToRoute('app_texto_index');
    }

    /**
     * @Route("/usuarios", name="app_usuarios_index")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('UserBundle:User');

        $usuarios = $repo->findBy(array(), array('id' => 'DESC'));
        return $this->render(':usuario:index.html.twig',
            [
                'usuarios' => $usuarios,
            ]
        );
    }





}
