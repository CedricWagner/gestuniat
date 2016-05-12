<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\ParametreType;
use AppBundle\Entity\Parametre;


class ParametreController extends Controller
{

    /**
    * @Route("/admin/parametres", name="list_parametres")
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function listParametresAction()
    {

        $parametres = $this->getDoctrine()
                        ->getRepository('AppBundle:Parametre')
                        ->findAll();

        $parametreForms = array();

        foreach ($parametres as $parametre) {
            $parametreForm = $this->createForm(ParametreType::class, $parametre,array(
                    'action'=> $this->generateUrl('save_parametre').'?idParametre='.$parametre->getId(),
                ));
            $parametreForms[$parametre->getId()] = $parametreForm->createView();
        }

        $newParametreForm = $this->createForm(ParametreType::class, new Parametre() ,array(
                'action'=> $this->generateUrl('save_parametre'),
            ));

        return $this->render('admin/parametres.html.twig',
            [
                'parametres' => $parametres,
                'parametreForms' => $parametreForms,
                'newParametreForm' => $newParametreForm->createView(),
            ]);
    }

    /**
    * @Route("/admin/parametre/save", name="save_parametre")
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function saveParametreAction(Request $request)
    {

        if($request->query->get('idParametre')){
            $parametre = $this->getDoctrine()
                ->getRepository('AppBundle:Parametre')
                ->find($request->query->get('idParametre'));
        }else{
            $parametre = new Parametre();
        }

        $parametreForm = $this->createForm(ParametreType::class, $parametre);
        $parametreForm->handleRequest($request);

        if ($parametreForm->isSubmitted() && $parametreForm->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($parametre);
            $em->flush();
        }

        return $this->redirectToRoute('list_parametres');
    }


    /**
    * @Route("/admin/parametre/delete/{idParametre}", name="delete_parametre")
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function deleteParametreAction($idParametre)
    {

        $parametre = $this->getDoctrine()
          ->getRepository('AppBundle:Parametre')
          ->find($idParametre);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->remove($parametre);
        $em->flush();

        return $this->redirectToRoute('list_parametres');
    }

}