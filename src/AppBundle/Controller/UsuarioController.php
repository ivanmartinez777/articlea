<?php

namespace AppBundle\Controller;


use Trascastro\UserBundle\Entity;
use AppBundle\Form\ImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Texto;
use AppBundle\Controller\TextoController;





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
        $estadoSuscripcion = "realizado la suscripcion de ";

        if(in_array($suscripcion,$user->getSuscripciones()))
        {
            $user->removeSuscripcion($suscripcion);
            $estadoSuscripcion = "eliminado la suscripcion de " ;
        }else {
            $user->addSuscripcion($suscripcion);
        }
        $m->flush();
        $this->addFlash('messages', $user->getUsername() . " ha ".$estadoSuscripcion . " a " . $suscripcion->getUsername());
        return $this->redirectToRoute('app_texto_index');
    }

    /**
     * @Route("/usuarios", name="app_usuarios_index")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function indexAction()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $m = $this->getDoctrine()->getManager();
            $repo = $m->getRepository('UserBundle:User');

            $usuarios = $repo->findBy(array(), array('id' => 'DESC'));
            return $this->render(':usuario:index.html.twig',
                [
                    'usuarios' => $usuarios,
                ]
            );
       }return $this->redirectToRoute('app_texto_index');
    }


    /**
     * @Route("/cambiarRole/{id}", name="app_usuario_cambiarRole")
     * @return \Symfony\Component\HttpFoundation\Response
     *@Security("has_role('ROLE_ADMIN')")
     */
    public function cambiarRoleAction($id)

    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('UserBundle:User');
        $user = $repo->findOneBy(array('id'=>$id));
        $role = "administrador";

       if ($user->hasRole('ROLE_ADMIN'))
       {
           $user->removeRole('ROLE_ADMIN');
           $role = "usuario";
       }else{
           $user->addRole('ROLE_ADMIN');
       }
        $m->flush();
        $this->addFlash('messages', 'el usuario ' . $user->getUsername() . "ahora tiene el rol de " . $role);
        return $this->redirectToRoute('app_usuarios_index');
    }

    /**
     * @Route("/removeUser/{id}", name="app_usuario_remove")
     *@return \Symfony\Component\HttpFoundation\Response
     *@Security("has_role('ROLE_ADMIN')")
     */
    public function removeUserAction( $id)
    {

        $m = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('UserBundle:User');
        $user = $repository->find($id);
        $repositoryTexto= $m->getRepository('AppBundle:Texto');
        $textos = $repositoryTexto->findBy(array('author'=>$user));
        foreach ($textos as $texto)
        {
               $this->forward('AppBundle:Texto:remove',array(
                   'id'=>$texto
               ));

        }
        $m->remove($user);
        $m->flush();




        return $this->redirectToRoute('app_usuarios_index');
    }



}
