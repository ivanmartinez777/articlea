<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Categoria;
use AppBundle\Form\CategoriaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;





class CategoriaController extends Controller
{
   /**
    * @Route("/categoria/", name="app_categoria_index")
    * @return \Symfony\Component\HttpFoundation\Response
    *
    *Función que devuelve todas las categorias
    */
    public function indexAction()
    {
        $user= $this->getUser();
        if ($user->hasRole('ROLE_ADMIN')) {
            $m = $this->getDoctrine()->getManager();
            $repo = $m->getRepository('AppBundle:Categoria');

            $categorias = $repo->findBy(array(), array('nombre' => 'ASC'));
            return $this->render(':categoria:index.html.twig',
                [
                    'categorias' => $categorias,
                ]
            );
        }return $this->redirectToRoute('app_texto_index');
    }

    /**
     * @Route("/categoriaBase/", name="app_categoria_indexBase")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     *Función que devuelve todas las categorias
     */
    public function indexBaseAction()
    {

            $m = $this->getDoctrine()->getManager();
            $repo = $m->getRepository('AppBundle:Categoria');

            $categorias = $repo->findBy(array(), array('nombre' => 'ASC'));
            return $this->render(':categoria:base.html.twig',
                [
                    'categorias' => $categorias,
                ]
            );

    }



    /**
     * @Route("/categoriaCreate", name="app_categoria_create")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Función que crea categorias
     */
    public function createAction()

    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $categoria = new Categoria();
            $form = $this->createForm(CategoriaType::class, $categoria);

            return $this->render(':categoria:form.html.twig',
                [
                    'form' => $form->createView(),
                    'action' => $this->generateUrl('app_categoria_doCreate')
                ]
            );
        }
        return $this->redirectToRoute('app_texto_index');
    }


    /**
     * @Route("/categoriaDoCreate", name="app_categoria_doCreate")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function doCreateAction(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $categoria = new Categoria();
            $form = $this->createForm(CategoriaType::class, $categoria);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $m = $this->getDoctrine()->getManager();
                $m->persist($categoria);
                $m->flush();
                $this->addFlash('messages', 'Categoria creada');
                return $this->redirectToRoute('app_categoria_index');
            }
            $this->addFlash('messages', 'Review your form data');
            return $this->render(':categoria:form.html.twig',
                [
                    'form' => $form->createView(),
                    'action' => $this->generateUrl('app_categoria_doCreate')
                ]
            );
        }
        $this->addFlash('messages', 'Si no es administrador no tiene acceso a esta sección');
        return $this->redirectToRoute('app_texto_index');

    }

    /**
     * @Route("/categoriaUpdate/{id}", name="app_categoria_update")
     *@return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Función que actualiza categorias
     */
    public function updateAction($id)
    {

        $m = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('AppBundle:Categoria');
        $categoria = $repository->find($id);

            $form = $this->createForm(CategoriaType::class, $categoria);

            return $this->render(':categoria:form.html.twig',
                [
                    'form' => $form->createView(),
                    'action' => $this->generateUrl('app_categoria_doUpdate', ['id' => $id])
                ]
            );


    }

    /**
     * @Route("/categoriaDoUpdate/{id}", name="app_categoria_doUpdate")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     *
     *
     */
    public function doUpdateAction($id, Request $request)
    {

        $m          = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('AppBundle:Categoria');
        $categoria = $repository->find($id);



            $form       = $this->createForm(CategoriaType::class, $categoria);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $m->flush();
                $this->addFlash('messages', 'categoria actualizada');
                return $this->redirectToRoute('app_categoria_index');
            }
            $this->addFlash('messages', 'Review your form');
            return $this->render(':categoria:form.html.twig',
                [
                    'form'      => $form->createView(),
                    'action'    => $this->generateUrl('app_categoria_doUpdate', ['id' => $id]),
                ]
            );


    }



    /**
     * @Route("/categoriaRemove/{id}", name="app_categoria_remove")
     *@return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Función que elimina categorias
     */
    public function removeAction( $id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Categoria');
        $miscelanea = $repository->findOneBy(array('nombre'=>'miscelanea'));
        $repositoryUsers = $em->getRepository('UserBundle:User');
        $categoria = $repository->findOneBy(array('id'=>$id));
        $users = $repositoryUsers->findBy(array('categoria'=>$categoria));
        $textos = $categoria->getTexto();
        if ($textos != null){
            foreach ($textos as $texto){
                $texto->setCategoria($miscelanea);
                $em->persist($texto);
                $em->flush();
            }

        }

        if ($users != null){
            foreach ($users as $user){
                $user->setCategoria($miscelanea);
                $em->persist($user);
                $em->flush();
            }

        }
        $em->remove($categoria);
        $em->flush();

        return $this->redirectToRoute('app_categoria_index');
    }



}
