<?php

namespace Trascastro\UserBundle\Controller;


use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Revista;


class RegistrationController extends BaseController
{


    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->getUser();

        //
        $em = $this->getDoctrine()->getManager();
        $revista = new Revista();
        $user->setRevista($revista);
        $revista->setNombre('Revista de '. $user->getUsernameCanonical());
        $em->persist($user);
        $em->persist($revista);
        $em->flush();
        //


        return $this->redirectToRoute('app_texto_index');

    }


}
