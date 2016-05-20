<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\SuiviDefaultType;
use AppBundle\Entity\Suivi;


class SuiviController extends Controller
{

  /**
   * @Route("/suivi/show-edit", name="show_edit_suivi")
   * @Security("has_role('ROLE_USER')")
   */
  public function showEditSuiviAction(Request $request)
  {

    $suivi = $this->getDoctrine()
      ->getRepository('AppBundle:Suivi')
      ->find($request->request->get('idSuivi'));

    $suiviForm = $this->createForm(SuiviDefaultType::class, $suivi,array(
        'action'=> $this->generateUrl('edit_suivi').'?idSuivi='.$suivi->getId(),
    ));

    return new Response($this->render('modals/editer-suivi-default.html.twig', [
            'suiviForm' => $suiviForm->createView(),
            'suivi' => $suivi,
        ])->getContent()); 
  }

  /**
   * @Route("/suivi/edit", name="edit_suivi")
   * @Security("has_role('ROLE_USER')")
   */
  public function editSuiviAction(Request $request)
  {

    $suivi = $this->getDoctrine()
      ->getRepository('AppBundle:Suivi')
      ->find($request->query->get('idSuivi'));

    $suiviForm = $this->createForm(SuiviDefaultType::class, $suivi);
    $suiviForm->handleRequest($request);

    if ($suiviForm->isSubmitted() && $suiviForm->isValid()) {

      $datetime = new \DateTime();

      $suivi->setDateEdition($datetime);

      $em = $this->get('doctrine.orm.entity_manager');
      $em->persist($suivi);
      $em->flush();

      $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
    }
    if ($suiviForm->isSubmitted() && !$suiviForm->isValid()) {
      $this->get('app.tools')->handleFormErrors($suiviForm);
    }

    return $this->redirect($request->headers->get('referer'));
  }

  /**
   * @Route("/suivi/dossier/save", name="save_suivi_dossier")
   * @Security("has_role('ROLE_USER')")
   */
  public function editSuiviDossierAction(Request $request)
  {

    $datetime = new \DateTime();
    
    if($request->query->get('idSuivi')){
      $suivi = $this->getDoctrine()
        ->getRepository('AppBundle:Suivi')
        ->find($request->query->get('idSuivi'));
    }else{
      $suivi = new Suivi();
      $dossier = $this->getDoctrine()
        ->getRepository('AppBundle:Dossier')
        ->find($request->query->get('idDossier'));
      $suivi->setDossier($dossier);
      $suivi->setOperateur($this->getUser());
      $suivi->setDateCreation($datetime);
    }

    $suiviForm = $this->createForm(SuiviDefaultType::class, $suivi);
    $suiviForm->handleRequest($request);

    if ($suiviForm->isSubmitted() && $suiviForm->isValid()) {

      $suivi->setDateEdition($datetime);

      $em = $this->get('doctrine.orm.entity_manager');
      $em->persist($suivi);
      $em->flush();

      $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
    }
    if ($suiviForm->isSubmitted() && !$suiviForm->isValid()) {
      $this->get('app.tools')->handleFormErrors($suiviForm);
    }

    return $this->redirect($request->headers->get('referer'));
  }

  /**
   * @Route("/suivi/delete/{idSuivi}", name="delete_suivi")
   * @Security("has_role('ROLE_USER')")
   */
  public function deleteSuiviAction(Request $request, $idSuivi)
  {

    $suivi = $this->getDoctrine()
       ->getRepository('AppBundle:Suivi')
       ->find($idSuivi);

    if ($suivi) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($suivi);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');
    }

     return $this->redirect($request->headers->get('referer'));
  }

    /**
   * @Route("/check-suivi", name="check_suivi")
   * @Method("POST")
   * @Security("has_role('ROLE_USER')")
   */
  public function checkSuiviAction(Request $request)
  {
      if($request->isXmlHttpRequest()){
          $idSuivi = $request->request->get('idSuivi');
          $target = $request->request->get('target'); 
          $action = $request->request->get('action');

          if($action=='done'){
              $value = false;
          }else{
              $value = true;
          }

          if($target == 'suivi'){
              $suivi = $this->getDoctrine()
                 ->getRepository('AppBundle:Suivi')
                 ->find($idSuivi);

              $suivi->setIsOk($value);

              $em = $this->getDoctrine()->getManager();
              $em->persist($suivi);
              $em->flush();
          }

          return new Response(json_encode(['state'=>'success'])); 
     }else{
          return new Response(json_encode(['state'=>'noXHR']));     
     }
  }
}