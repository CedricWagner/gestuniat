<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\OperateurType;
use AppBundle\Entity\Operateur;


class OperateurController extends Controller
{

    /**
    * @Route("/admin/operateurs", name="list_operateurs")
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function listOperateursAction()
    {

        $operateurs = $this->getDoctrine()
                        ->getRepository('AppBundle:Operateur')
                        ->findAll();

        $operateurForms = array();

        foreach ($operateurs as $operateur) {
            $operateurForm = $this->createForm(OperateurType::class, $operateur,array(
                    'action'=> $this->generateUrl('save_operateur').'?idOperateur='.$operateur->getId(),
                ));
            $operateurForms[$operateur->getId()] = $operateurForm->createView();
        }

        $newOperateurForm = $this->createForm(OperateurType::class, new Operateur() ,array(
                'action'=> $this->generateUrl('save_operateur'),
            ));

        return $this->render('admin/operateurs.html.twig',
            [
                'operateurs' => $operateurs,
                'operateurForms' => $operateurForms,
                'newOperateurForm' => $newOperateurForm->createView(),
            ]);
    }

    /**
    * @Route("/admin/operateur/save", name="save_operateur")
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function saveOperateurAction(Request $request)
    {

        if($request->query->get('idOperateur')){
            $operateur = $this->getDoctrine()
                ->getRepository('AppBundle:Operateur')
                ->find($request->query->get('idOperateur'));
            
            $oldPassword = $operateur->getMdp();

        }else{
            $operateur = new Operateur();
        }

        $operateurForm = $this->createForm(OperateurType::class, $operateur);
        $operateurForm->handleRequest($request);

        if ($operateurForm->isSubmitted() && $operateurForm->isValid()) {
            
            //set new password
            if($operateur->getMdp()!=''){
                $password = $this
                    ->get('security.password_encoder')
                    ->encodePassword($operateur, $operateur->getMdp());
            }else{
                //keep the old password
                $password = $oldPassword;
            }
            
            $operateur->setMdp($password);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($operateur);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
        }

        return $this->redirectToRoute('list_operateurs');
    }


    /**
    * @Route("/admin/operateur/delete/{idOperateur}", name="delete_operateur")
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function deleteOperateurAction($idOperateur)
    {

        $operateur = $this->getDoctrine()
          ->getRepository('AppBundle:Operateur')
          ->find($idOperateur);

        $operateur->setRole('DELETED');

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($operateur);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

        return $this->redirectToRoute('list_operateurs');
    }

}