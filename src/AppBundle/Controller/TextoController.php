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



class TextoController extends Controller
{
   /**
    * @Route("/", name="app_texto_index")
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Texto');
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
     * @Security("has_role('ROLE_USER')")
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
     * @Security("has_role('ROLE_USER')")
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
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Texto');
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
        $em->remove($texto);
        $em->flush();

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
        $revistaTextos = $repo->findBy(array('revista'=>$revista));

        return $this->render(':texto:revista.html.twig',
            [
                'revistaTextos' => $revistaTextos,

            ]
        );

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
