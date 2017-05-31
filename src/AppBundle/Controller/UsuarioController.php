<?php

namespace AppBundle\Controller;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Texto;






class UsuarioController extends Controller
{
    /**
     * @Route("/suscripcion/{usuario}", name="app_usuario_suscripcion")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Función encargada de suscribir o eliminar suscripcion
     */
    public function suscribeAction($usuario)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('UserBundle:User');
        $suscripcion = $repo->myFindOneByUsernameOrEmail($usuario);
        $estadoSuscripcion = "realizado la suscripcion de ";

        if(in_array($suscripcion,$user->getSuscripciones()))
        {
            $user->removeSuscripcion($suscripcion);
            $estadoSuscripcion = "has eliminado la suscripción de " ;
        }else {
            $user->addSuscripcion($suscripcion);
        }
        $em->flush();
        $this->addFlash('messages', $user->getUsername() . " ha ".$estadoSuscripcion . " a " . $suscripcion->getUsername());
        return $this->redirectToRoute('app_texto_index');
    }

    /**
     * @Route("/usuarios", name="app_usuarios_index")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Funcion que se encarga de enviar los usuarios a la vista para que el admin pueda trabajar con ellos
     *
     */
    public function indexAction(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('UserBundle:User');

            $usuarios = $repo->findBy(array(), array('id' => 'DESC'));

            /**
             * @var $paginator \knp\Component\Pager\Paginator
             */
            $paginator = $this->get('knp_paginator');
            $usuariospaginados = $paginator->paginate(
                $usuarios,
                $request->query->getInt('page',1),
                6
            );
            return $this->render(':usuario:index.html.twig',
                [
                    'usuarios' => $usuariospaginados,
                ]
            );
       }return $this->redirectToRoute('app_texto_index');
    }


    /**
     * @Route("/cambiarRole/{id}", name="app_usuario_cambiarRole")
     * @return \Symfony\Component\HttpFoundation\Response
     *@Security("has_role('ROLE_ADMIN')")
     *
     * Función encargada de cambiar el role
     */
    public function cambiarRoleAction($id)

    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('UserBundle:User');
        $user = $repo->findOneBy(array('id'=>$id));
        $role = "administrador";

       if ($user->hasRole('ROLE_ADMIN'))
       {
           $user->removeRole('ROLE_ADMIN');
           $role = "usuario";
       }else{
           $user->addRole('ROLE_ADMIN');
       }
        $em->flush();
        $this->addFlash('messages', 'el usuario ' . $user->getUsername() . "ahora tiene el rol de " . $role);
        return $this->redirectToRoute('app_usuarios_index');
    }

    /**
     * @Route("/removeUser/{id}", name="app_usuario_remove")
     *@return \Symfony\Component\HttpFoundation\Response
     *@Security("has_role('ROLE_ADMIN')")
     *
     * Función encargada de eliminar a un usuario
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

    /**
     * @Route("/usuarioPorUsername/{palabra}", name="app_usuariosUsername_show")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     *
     * Función utilizada para enviar usuarios a la vista cuando se busca por su nombre
     */

    public function usuariosUsernameAction($palabra, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $usuarios =$em->getRepository('UserBundle:User')->buscarPorNombre($palabra);

        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $usuariospaginados = $paginator->paginate(
            $usuarios,
            $request->query->getInt('page',1),
            6
        );
        return $this->render(':usuario:indexBusqueda.html.twig',
            [
                'usuarios' => $usuariospaginados,

            ]
        );

    }

    /**
     * @Route("/usuariosPorUsername/{palabra}", name="app_usuariosUsername_index")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Función que ayuda en la vista a buscar usuarios por el username para trabajar con ellos
     */

    public function usuariosIndexAction($palabra, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $usuarios =$em->getRepository('UserBundle:User')->buscarPorNombre($palabra);

        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $usuariospaginados = $paginator->paginate(
            $usuarios,
            $request->query->getInt('page',1),
            6
        );
        return $this->render(':usuario:index.html.twig',
            [
                'usuarios' => $usuariospaginados,

            ]
        );

    }

    /**
     * @Route("/buscarUsuario", name="app_usuario_buscarUsuario")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarUsuarioAction(Request $request)
    {
        $busqueda = $_POST['busquedaUsuario'];

        return $this->redirectToRoute('app_usuariosUsername_index', ['palabra'=>$busqueda]);


    }



}
