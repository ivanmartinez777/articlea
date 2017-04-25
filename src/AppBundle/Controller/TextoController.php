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
use AppBundle\Entity\Comentario;



class TextoController extends Controller
{
   /**
    * @Route("/", name="app_texto_index")
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function indexAction()
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Texto');

        $textos = $repo->findAll();
        return $this->render(':texto:index.html.twig',
        [
            'textos' => $textos,
        ]
        );
    }

    /**
     * @Route("/soloTexto/{id}", name="app_texto_individual")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function individualAction($id)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Texto');

        $texto = $repo->find($id);
        return $this->render(':texto:textoInd.html.twig',
            [
                'texto' => $texto,
            ]
        );
    }



    /**
     * @Route("/show_{author}", name="app_texto_show")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction($author, Request $request)
    {
        $m = $this->getDoctrine()->getManager();
        $repo2 = $m->getRepository('UserBundle:User');
        $repo = $m->getRepository('AppBundle:Texto');

        $user = $repo2->myFindOneByUsernameOrEmail($author);
        //como la variable "author" me viene como username y no como id
        //busco el usuario que tenga ese username

        $idAuthor = $user->getId();
        //una vez tengo al usuario, obtengo su id

        $textos = $repo->findBy(['author' => $idAuthor]);
        //busco los textos que tengan como autor esa id
        return $this->render(':texto:show.html.twig',
            [
                'textos' => $textos,
                'user' => $user,
            ]
        );

    }

    /**
     * @Route("/create", name="app_texto_create")
     *@return \Symfony\Component\HttpFoundation\Response
     *  @Security("has_role('ROLE_USER')")
     */
    public function createAction()

    {

        $texto = new Texto();
        $form = $this->createForm(TextoType::class, $texto);

        return $this->render(':texto:form.html.twig',
            [
                'form'      => $form->createView(),
                'action'    => $this->generateUrl('app_texto_doCreate')
            ]
        );
    }

    /**
     * @Route("/doCreate", name="app_texto_doCreate")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function doCreateAction(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            $texto = new Texto();
            $form = $this->createForm(TextoType::class, $texto);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $this->getUser();
                $user->setNumTextos();
                $texto->setAuthor($user);
                $m = $this->getDoctrine()->getManager();
                $m->persist($texto);
                $m->flush();
                $this->addFlash('messages', 'Texto creado');
                return $this->redirectToRoute('app_texto_index');
            }
            $this->addFlash('messages', 'Review your form data');
            return $this->render(':texto:form.html.twig',
                [
                    'form' => $form->createView(),
                    'action' => $this->generateUrl('app_texto_doCreate')
                ]
            );
        }

    }

    /**
     * @Route("/update/{id}", name="app_texto_update")
     *@return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function updateAction($id)
    {

        $m = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('AppBundle:Texto');
        $texto = $repository->find($id);
        $user = $this->getUser();
        $textoUser= $texto->getAuthor();
        if($user->getId() === $textoUser->getId()) {
            $form = $this->createForm(TextoType::class, $texto);

            return $this->render(':texto:form.html.twig',
                [
                    'form' => $form->createView(),
                    'action' => $this->generateUrl('app_texto_doUpdate', ['id' => $id])
                ]
            );
        }return $this->redirectToRoute('app_texto_index');

    }

    /**
     * @Route("/doUpdate/{id}", name="app_texto_doUpdate")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function doUpdateAction($id, Request $request)
    {

        $m          = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('AppBundle:Texto');
        $texto = $repository->find($id);
        $user = $this->getUser();
        $textoUser= $texto->getAuthor();
        if($user->getId() === $textoUser->getId()) {
            $form       = $this->createForm(TextoType::class, $texto);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $m->flush();
                $this->addFlash('messages', 'texto actualizado');
                return $this->redirectToRoute('app_texto_index');
            }
            $this->addFlash('messages', 'Review your form');
            return $this->render(':texto:form.html.twig',
                [
                    'form'      => $form->createView(),
                    'action'    => $this->generateUrl('app_texto_doUpdate', ['id' => $id]),
                ]
            );
        } return $this->redirectToRoute('app_texto_index');

    }



    /**
     * @Route("/remove/{id}", name="app_texto_remove")
     *@return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function removeAction( $id)
    {
        $m = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('AppBundle:Texto');
        $texto = $repository->find($id);
        $m->remove($texto);
        $m->flush();

        return $this->redirectToRoute('app_texto_index');
    }



}
