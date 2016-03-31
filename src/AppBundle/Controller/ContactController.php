<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class ContactController extends Controller
{
    /**
     * @Route("/contact/liste", name="list_contacts")
     * @Security("has_role('ROLE_USER')")
     */
    public function listContactsAction(Request $request)
    {

        $filtresPerso = $this->getDoctrine()
                      ->getRepository('AppBundle:FiltrePerso')
                      ->findBy(array(),array('label'=>'ASC'));

        return $this->render('operateur/contacts.html.twig', [
            'filtresPerso' => $filtresPerso
        ]);
    }

}
