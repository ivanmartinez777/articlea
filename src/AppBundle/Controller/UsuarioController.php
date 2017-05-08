<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Texto;
use Trascastro\UserBundle\Entity;
use AppBundle\Form\ImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\TextoType;




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

    

}
