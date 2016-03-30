<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Alerte;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Form\AlerteCreationType;



class ContactController extends Controller
{
    /**
     * @Route("/contact/liste", name="list_contacts")
     * @Security("has_role('ROLE_USER')")
     */
    public function listContactsAction(Request $request)
    {

        return $this->render('operateur/contacts.html.twig', [

        ]);
    }

}
