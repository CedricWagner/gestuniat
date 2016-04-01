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
     * @Route("/contact/liste/{idFilter}", name="list_contacts", defaults={"idFilter" = 0})
     * @Security("has_role('ROLE_USER')")
     */
    public function listContactsAction($idFilter)
    {

        $currentFilter = null;

        if ($idFilter!=0) {
            $currentFilter = $this->getDoctrine()
              ->getRepository('AppBundle:FiltrePerso')
              ->find($idFilter);

            $filtreValeurs = $this->getDoctrine()
              ->getRepository('AppBundle:FiltreValeur')
              ->findBy(array('filtrePerso'=>$currentFilter));

            $currentFilter->setFiltreValeurs($filtreValeurs);
        }

        $filtresPerso = $this->getDoctrine()
                          ->getRepository('AppBundle:FiltrePerso')
                          ->findBy(array(),array('label'=>'ASC'));

        $statutsJuridiques = $this->getDoctrine()
                              ->getRepository('AppBundle:StatutJuridique')
                              ->findAll();

        $fonctionsGroupement = $this->getDoctrine()
                              ->getRepository('AppBundle:FonctionGroupement')
                              ->findAll();

        $fonctionsSection = $this->getDoctrine()
                              ->getRepository('AppBundle:FonctionSection')
                              ->findAll();

        $diplomes = $this->getDoctrine()
                              ->getRepository('AppBundle:Diplome')
                              ->findAll();

        return $this->render('operateur/contacts.html.twig', [
            'filtresPerso' => $filtresPerso,
            'statutsJuridiques' => $statutsJuridiques,
            'fonctionsGroupement' => $fonctionsGroupement,
            'fonctionsSection' => $fonctionsSection,
            'diplomes' => $diplomes,
            'currentFilter' => $currentFilter,
        ]);
    }

}
