<?php

namespace AppBundle\Controller;


use AppBundle\Entity\RevistaTexto;
use AppBundle\Entity\Texto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\TextoType;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



class TextoController extends Controller
{
   /**
    * @Route("/", name="app_texto_index")
    *
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Texto');
        $textos = $repo->findBy(array(), array('id' => 'DESC'));
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $textospaginados = $paginator->paginate(
            $textos,
            $request->query->getInt('page',1),
            6
        );
        return $this->render(':texto:index.html.twig',
        [
            'textos' => $textospaginados,

        ]
        );
    }

    /**
     * @Route("/soloTexto/{id}", name="app_texto_individual")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function individualAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Texto');
        $repo2 =$em->getRepository('UserBundle:User');
        $texto = $repo->find($id);
        $usuario = $repo2->findOneBy(array('id'=>$texto->getAuthor()));
        $textosCategoria = $repo->sugerenciaCategoria($texto->getCategoria());
        $textoAutor = $repo->sugerenciaAutor($texto->getAuthor());
        if ($this->isGranted('ROLE_USER'))
        {
            $user = $this->getUser();
            $revista = $user->getRevista();
            $repositoryRevista = $em->getRepository('AppBundle:RevistaTexto');
            $textoRevista = $repositoryRevista->buscarPorRevistaTexto($revista,$texto);

            if($textoRevista != null)
            {

                $textoRevista->setVisto(true);
                $em->persist($textoRevista);

            }

        }
        $texto->setNumVisitas();
        $em->persist($texto);
        $em->flush();

        return $this->render(':texto:textoInd.html.twig',
            [
                'texto' => $texto,
                'textosCategoria' => $textosCategoria,
                'textoAutor' => $textoAutor,
                'usuario'=> $usuario,
            ]
        );
    }



    /**
     * @Route("/show_{author}", name="app_texto_show")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function showAction($author, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo2 = $em->getRepository('UserBundle:User');
        $repo = $em->getRepository('AppBundle:Texto');

        $user = $repo2->myFindOneByUsernameOrEmail($author);
        //como la variable "author" me viene como username y no como id
        //busco el usuario que tenga ese username

        $idAuthor = $user->getId();
        //una vez tengo al usuario, obtengo su id

        $textos = $repo->findBy(['author' => $idAuthor]);
        //busco los textos que tengan como autor esa id

        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $textospaginados = $paginator->paginate(
            $textos,
            $request->query->getInt('page',1),
            6
        );
        return $this->render(':texto:show.html.twig',
            [
                'textos' => $textospaginados,
                'usuario' => $user,
            ]
        );

    }


    /**
     * @Route("/textoPorCategoria/{categoria}", name="app_textoCategoria_show")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */

    public function textoCategoriaAction($categoria, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Texto');
        $textos = $repo->findBy(['categoria' => $categoria]);

        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $textospaginados = $paginator->paginate(
            $textos,
            $request->query->getInt('page',1),
            6
        );

        return $this->render(':texto:textosPorCategoria.html.twig',
            [
                'textos' => $textospaginados,
                'nomCategoria' => $categoria,
            ]
        );

    }

    /**
     * @Route("/textoPorTag/{tag}", name="app_textoTag_show")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */

    public function textoTagAction($tag, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $textos =$em->getRepository('AppBundle:Texto')->buscarPorTag($tag);
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $textospaginados = $paginator->paginate(
            $textos,
            $request->query->getInt('page',1),
            6
        );
        return $this->render(':texto:textosPorCategoria.html.twig',
            [
                'textos' => $textospaginados,

            ]
        );

    }

    /**
     * @Route("/textoPorTitulo/{palabra}", name="app_textoTitulo_show")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */

    public function textoPalabraAction($palabra, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $textos =$em->getRepository('AppBundle:Texto')->buscarPorTitulo($palabra);

        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $textospaginados = $paginator->paginate(
            $textos,
            $request->query->getInt('page',1),
            6
        );
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
                $em = $this->getDoctrine()->getManager();
                $em->persist($texto);
                $em->flush();
                $this->enviarSuscriptor();
                $this->setEjemploAction();

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
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Texto');
        $texto = $repository->find($id);
        $user = $this->getUser();
        $textoUser= $texto->getAuthor();
        if($user->getId() === $textoUser->getId() or ($user->hasRole("ROLE_ADMIN"))){
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

        $em          = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Texto');
        $texto = $repository->find($id);
        $user = $this->getUser();
        $textoUser= $texto->getAuthor();
        if($user->getId() === $textoUser->getId() or ($user->hasRole("ROLE_ADMIN"))){
            $form       = $this->createForm(TextoType::class, $texto);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();
                $texto->setEjemplo($texto->getCuerpo());
                $em->flush();
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
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Texto');
        $texto = $repository->find($id);
        $user = $this->getUser();
        if($user == $texto->getAuthor() || $user->hasRole('ROLE_ADMIN')) {
            $em->remove($texto);
            $em->flush();
        }else{

        $this->addFlash('messages', 'Este texto no es suyo');

        }


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
        return $this->addFlash('messages', 'Elija una opcion');
    }


    /**
     * @Route("/enviarTexto", name="app_texto_enviarTexto")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function enviarSuscriptor()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $repositorioTexto= $em->getRepository('AppBundle:Texto');
        $texto = $repositorioTexto->findOneBy(array('author'=> $user),
            array('id'=> 'DESC'));

        $repositorioUsuario = $em->getRepository('UserBundle:User');
        $usuarios = $repositorioUsuario->findBy(array('id'=>$user->getSuscriptores()));
        if( $usuarios == null){
            return $this->redirectToRoute('app_texto_index');
        }else {
            foreach ($usuarios as $usuario)
                $revistaTexto = new RevistaTexto();
                 $revistaTexto->setRevista($usuario->getRevista());
                 $revistaTexto->setTexto($texto);
                  $em->persist($revistaTexto);
                  $em->flush();

            return $this->addFlash('messages', 'Texto añadido a tus suscriptores');
        }
    }

    /**
     * @Route("/revista", name="app_texto_revista")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function textoRevistaAction( Request $request)
    {
        $user = $this->getUser();
        $revista = $user->getRevista();
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:RevistaTexto');
        $revistaTextos = $repo->findBy(array('revista'=>$revista),
            array('createdAt'=>'DESC'));

        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $textospaginados = $paginator->paginate(
            $revistaTextos,
            $request->query->getInt('page',1),
            6
        );

        return $this->render(':texto:revista.html.twig',
            [
                'revistaTextos' => $textospaginados,

            ]
        );

    }

    /**
     * @Route("/revistaTextosFav", name="app_texto_revistaTextosFav")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function revistaTextoFavAction( Request $request)
    {
        $user = $this->getUser();
        $revista = $user->getRevista();
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:RevistaTexto');
       $revistaTextos = $repo->buscarFavs($revista);

        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $textospaginados = $paginator->paginate(
            $revistaTextos,
            $request->query->getInt('page',1),
            6
        );

        return $this->render(':texto:prueba.html.twig',
            [
                'revistaTextos' => $textospaginados,


            ]
        );

    }




    /**
     * @Route("/setEjemplo/{id}", name="app_texto_setEjemplo")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function setEjemploAction()

    {
        $em = $this->getDoctrine()->getManager();
        $user= $this->getUser();
        $repositorioTexto= $em->getRepository('AppBundle:Texto');
        $texto = $repositorioTexto->findOneBy(array('author'=> $user),
            array('id'=> 'DESC'));
        $texto->setEjemplo($texto->getCuerpo());
        $em->persist($texto);
        $em->flush();

        return $this->redirectToRoute('app_texto_individual', ['id'=>$texto->getId()]);
    }





}
