<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Texto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\TextoType;
use AppBundle\Entity\Tag;




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

        $textos = $repo->findBy(array(), array('id' => 'DESC'));
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
     * @Route("/textoPorCategoria/{categoria}", name="app_textoCategoria_show")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function textoCategoriaAction($categoria, Request $request)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Texto');
        $textos = $repo->findBy(['categoria' => $categoria]);

        return $this->render(':texto:textosPorCategoria.html.twig',
            [
                'textos' => $textos,
                'nomCategoria' => $categoria,
            ]
        );

    }

    /**
     * @Route("/textoPorTag/{tag}", name="app_textoTag_show")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function textoTagAction($tag, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $textos =$em->getRepository('AppBundle:Texto')->buscarPorTag($tag);
        return $this->render(':texto:textosPorCategoria.html.twig',
            [
                'textos' => $textos,

            ]
        );

    }

    /**
     * @Route("/textoPorTitulo/{palabra}", name="app_textoTitulo_show")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function textoPalabraAction($palabra, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $textos =$em->getRepository('AppBundle:Texto')->buscarPorTitulo($palabra);
        return $this->render(':texto:textosPorCategoria.html.twig',
            [
                'textos' => $textos,

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
        //Dentro del texto, creamos los tags y ese texto después será pasado a form

        $tag1 = new Tag();
        $tag2 = new Tag();
        $tag3 = new Tag();
        $tag4 = new Tag();
        $tag5 = new Tag();
        $texto->addTag($tag1);
        $texto->addTag($tag2);
        $texto->addTag($tag3);
        $texto->addTag($tag4);
        $texto->addTag($tag5);


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
            $tag1 = new Tag();
            $tag2 = new Tag();
            $tag3 = new Tag();
            $tag4 = new Tag();
            $tag5 = new Tag();
            $texto->addTag($tag1);
            $texto->addTag($tag2);
            $texto->addTag($tag3);
            $texto->addTag($tag4);
            $texto->addTag($tag5);
            $form = $this->createForm(TextoType::class, $texto);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $this->getUser();
                $texto->setAuthor($user);
                $texto->setCategoria($user->getCategoria());
                $m = $this->getDoctrine()->getManager();

                $m->persist($texto);
                $m->flush();
                $this->enviarSuscriptor();



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
     * @Route("/prueba", name="app_texto_prueba")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function enviarSuscriptor()
    {
        $user = $this->getUser();
        $m = $this->getDoctrine()->getManager();
        $repositorio= $m->getRepository('AppBundle:Texto');
        $texto = $repositorio->findOneBy(array('author'=> $user),
            array('id'=> 'DESC'));
        $repo = $m->getRepository('UserBundle:User');
        $usuarios = $repo->findBy(array('id'=>$user->getSuscriptores()));
        foreach ($usuarios as $usuario)
            $usuario->addTextoPag($texto);
        $m->flush();

        return $this->addFlash('messages', 'Review your form data');

    }


    /**
     * @Route("/prueba", name="app_texto_prueba")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function paginaPrincipal()
    {
        $user = $this->getUser();
        $m = $this->getDoctrine()->getManager();
        $repositorio = $m->getRepository('AppBundle:Texto');
        $textos = $repositorio->findBy(array('id'=>$user->getTextosPag()),
                                        array('id'=> 'DESC'));
        return $this->render('texto/prueba.html.twig',
            [
                'textos' => $textos,
                'usuario'=> $user->getUsername(),
            ]
        );

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
        if($user->getId() === $textoUser->getId() or ($user->getUsername() === "admin")){
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
        if($user->getId() === $textoUser->getId() or ($user->getUsername() === "admin")){
            $form       = $this->createForm(TextoType::class, $texto);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $m->flush();
                $this->addFlash('messages', 'texto actualizado');
                return $this->redirectToRoute('app_texto_individual', ['id' => $id]);
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
        $repositoryUser= $m->getRepository('UserBundle:User');
        $usuarios = $repositoryUser->findBy(array('id' => $texto->getTPag()));
        foreach ($usuarios as $usuario)
            $usuario->removeTextosPagPrincipal($texto);
        $m->remove($texto);
        $m->flush();

        return $this->redirectToRoute('app_texto_index');
    }

    /**
     * @Route("/buscar", name="app_texto_buscar")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarAction(Request $request)
    {
            $busqueda = $_POST['busqueda'];
            $tipo = $_POST['tipo'];
        switch ($tipo) {
            case "texto":
                return $this->redirectToRoute('app_textoTitulo_show', ['palabra' => $busqueda]);
            case "usuario":
                return $this->forward('AppBundle:Usuario:usuariosUsername', ['palabra'=>$busqueda]);
            case "tag":
                return $this->redirectToRoute('app_textoTag_show', ['tag' => $busqueda]);
        }



    }



}
