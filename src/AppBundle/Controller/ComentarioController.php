<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Comentario;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\ComentarioType;
use AppBundle\Entity\Texto;

class ComentarioController extends Controller
{
    /**
     * @Route("/show/{id}", name="app_comentario_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($id)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Comentario');
        $comentarios = $repo->findByTexto($id);
        return $this->render(':comentario:index.html.twig',
            [
                'comentarios' => $comentarios,
            ]
        );
    }

    /**
     * @Route("/create/{id}", name="app_comentario_create")
     *@return \Symfony\Component\HttpFoundation\Response
     *  @Security("has_role('ROLE_USER')")
     */
    public function createAction($id)
    {

        $comentario = new Comentario();
        $form = $this->createForm(ComentarioType::class, $comentario);

        return $this->render(':comentario:form.html.twig',
            [
                'form'      => $form->createView(),
                'action'    => $this->generateUrl('app_comentario_doCreate', ['id' => $id])
            ]
        );
    }



    /**
     * @Route("/doCreate/{id}", name="app_comentario_doCreate")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function doCreateAction(Request $request, $id)
    {
        if ($this->isGranted('ROLE_USER')) {
            $comentario = new Comentario();
            $form = $this->createForm(ComentarioType::class, $comentario,
                ['action' => $this->generateUrl('app_comentario_doCreate',['id'=>$id])]);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $this->getUser();
                $comentario->setAuthor($user);
                $m = $this->getDoctrine()->getManager();
                $repository = $m->getRepository('AppBundle:Texto');
                $texto = $repository->find($id);
                $comentario->setTexto($texto);
                $m = $this->getDoctrine()->getManager();
                $m->persist($comentario);
                $m->flush();
                $this->addFlash('messages', 'Comentario creado');
                return $this->redirectToRoute('app_texto_individual', ['id' => $id]);
            }
            $this->addFlash('messages', 'Review your form data');
            return $this->render(':texto:form.html.twig',
                [
                    'form' => $form->createView(),
                    'action' => $this->generateUrl('app_comentario_doCreate', ['id' => $id])
                ]
            );
        }

    }

    /**
     * @Route("/updateComentario/{id}", name="app_comentario_update")
     *@return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function updateAction($id)
    {

        $m = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('AppBundle:Comentario');
        $comentario = $repository->find($id);
        $user = $this->getUser();
        $comentarioUser= $comentario->getAuthor();
        if($user->getId() === $comentarioUser->getId()) {
            $form = $this->createForm(ComentarioType::class, $comentario);

            return $this->render(':comentario:form.html.twig',
                [
                    'form' => $form->createView(),
                    'action' => $this->generateUrl('app_comentario_doUpdate', ['id' => $id])
                ]
            );
        }return $this->redirectToRoute('app_texto_index');

    }

    /**
     * @Route("/doUpdateComentario/{id}", name="app_comentario_doUpdate")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function doUpdateAction($id, Request $request)
    {

        $m          = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('AppBundle:Comentario');
        $comentario = $repository->find($id);
        $user = $this->getUser();
        $comentarioUser= $comentario->getAuthor();
        if($user->getId() === $comentarioUser->getId()) {
            $form       = $this->createForm(ComentarioType::class, $comentario);

            $form->handleRequest($request);
            if ($form->isValid()) {
                $m->flush();
                $this->addFlash('messages', 'comentario actualizado');
                return $this->redirectToRoute('app_texto_individual', ['id' => $comentario->getTexto()->getId()]);
            }
            $this->addFlash('messages', 'Review your form');
            return $this->render(':comentario:form.html.twig',
                [
                    'form'      => $form->createView(),
                    'action'    => $this->generateUrl('app_comentario_doUpdate', ['id' => $id]),
                ]
            );
        } return $this->redirectToRoute('app_texto_index');

    }



    /**
     * @Route("/removeComment/{id}", name="app_comentario_remove")
     *@return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function removeAction($id)
    {
        $m = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('AppBundle:Comentario');
        $comentario = $repository->find($id);
        $m->remove($comentario);
        $m->flush();

        return $this->redirectToRoute('app_texto_individual', ['id' => $comentario->getTexto()->getId()]);
    }


}
