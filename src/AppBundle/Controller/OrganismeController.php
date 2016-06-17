<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\OrganismeType;
use AppBundle\Entity\Organisme;
use AppBundle\Utils\FPDF\templates\Etiquette as PDF_Etiquette;


class OrganismeController extends Controller
{

	 /**
     * @Route("/organisme/liste/{idFilter}/{page}/{nb}", name="list_organismes", defaults={"idFilter" = 0,"page" = 1,"nb" = 0,"orderby" = "nom","order" = "ASC"})
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function listOrganismesAction($idFilter,$page,$nb,$orderby,$order)
    {

      $this->get('app.security')->checkAccess('ORGANISME_READ');

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

      $session = $this->get('session');
      if($nb==0){
          if($session->get('pagination-nb')){
              $nb = $session->get('pagination-nb');
          }else{
              $nb=20;
          }
      }else{
          $session->set('pagination-nb', $nb);
      }

      $filtresPerso = $this->getDoctrine()
                ->getRepository('AppBundle:FiltrePerso')
                ->findBy(array('operateur'=>$this->getUser(),'contexte'=>'organisme'),array('label'=>'ASC'));

      if($currentFilter){
  			$organismes = $this->getDoctrine()
  			  ->getRepository('AppBundle:Organisme')
  			  ->findByFilter($filtreValeurs,$page,$nb);
      }else{
  			$organismes = $this->getDoctrine()
  			  ->getRepository('AppBundle:Organisme')
  			  ->findAllWithPagination($page,$nb);
      }

      $organisme = new Organisme();
      $newOrganismeForm =  $this->createForm(OrganismeType::class, $organisme,array(
      		'action'=>$this->generateUrl('save_organisme')
      	));

      $types = $this->getDoctrine()
      	->getRepository('AppBundle:TypeOrganisme')
      	->findAll();

      return $this->render('operateur/organismes/organismes.html.twig', [
          'filtresPerso' => $filtresPerso,
          'currentFilter' => $currentFilter,
          'types' => $types,
          'newOrganismeForm' => $newOrganismeForm->createView(),
          'items' => $organismes,
          'pagination' => array('count'=>count($organismes),'nb'=>$nb,'page'=>$page,'orderby'=>$orderby,'order'=>$order),
      ]);

    }

    /**
     * @Route("/organisme/show-edit", name="show_edit_organisme")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function showEditOrganismeAction(Request $request)
    {

    	$organisme = $this->getDoctrine()
    		->getRepository('AppBundle:Organisme')
    		->find($request->request->get('idOrganisme'));

	    $organismeForm = $this->createForm(OrganismeType::class, $organisme,array(
	    ));

    	return new Response($this->render('modals/default.html.twig',
    		[
    			'object' => $organisme,
    			'entity' => 'organisme',
    			'title' => 'Editer un organisme',
    			'form' => $organismeForm->createView(),
          'action' => $this->generateUrl('save_organisme').'?idOrganisme='.$organisme->getId(),
    			'isWritable' => $this->get('app.security')->hasAccess('ORGANISME_WRITE'),
    		])->getContent());
    }

	/**
	* @Route("/organisme/save", name="save_organisme")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function saveOrganismeAction(Request $request)
	{

    $this->get('app.security')->checkAccess('ORGANISME_WRITE');

		if($request->query->get('idOrganisme')){
			$organisme = $this->getDoctrine()
				->getRepository('AppBundle:Organisme')
				->find($request->query->get('idOrganisme'));
		}else{
			$organisme = new Organisme();
		}

		$organismeForm = $this->createForm(OrganismeType::class, $organisme);
		$organismeForm->handleRequest($request);

		if ($organismeForm->isSubmitted() && $organismeForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($organisme);
			$em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
        if ($organismeForm->isSubmitted() && !$organismeForm->isValid()) {
            $this->get('app.tools')->handleFormErrors($organismeForm);
        }

		return $this->redirectToRoute('list_organismes');
	}


    /**
     * @Route("/organisme/listing/action", name="organisme_action_listing")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function organismeListingAction(Request $request)
    {

      $this->get('app.security')->checkAccess('ORGANISME_READ');

      $action = $request->request->get('action');
      switch ($action) {
        case 'DELETE_ITEMS':
          $this->get('app.security')->checkAccess('ORGANISME_DELETE');
          $selection = $request->request->get('selection');
          foreach ($selection as $id) {
            $this->deleteOrganismeAction($id);
          }
          $path = $this->generateUrl('list_organismes');
          break;
        case 'ETIQUETTES':
          $this->get('app.security')->checkAccess('ORGANISME_ET_PRINT');
          $selection = $request->request->get('selection');
          $pdf = new PDF_Etiquette('L7163');
          $pdf->AddPage();
          foreach ($selection as $id) {
            $organisme = $this->getDoctrine()
              ->getRepository('AppBundle:Organisme')
              ->find($id);
            $text = sprintf("%s\n%s\n%s\n%s %s, %s", $organisme->getNomTitulaire(), $organisme->getAdresse(), $organisme->getAdresseComp(), $organisme->getCp(), $organisme->getVille(), $organisme->getPays());
            $pdf->Add_Label(utf8_decode($text));
          }
          $pdf->Output('F','pdf/last-'.$this->getUser()->getId().'.pdf');
          $path = $this->generateUrl('download_last_pdf',['fileName'=>'export-etiquettes']);
          break;
        case 'EXPORT':
          $this->get('app.security')->checkAccess('ORGANISME_PRINT');
          $selection = $request->request->get('selection');
          $csv = $this->get('app.csvgenerator');
          $csv->setName('export_liste-organismes');
          $csv->addLine(array('Nom','Titulaire','Fonction','Adresse','Adresse complémentaire','Code postal','Ville'));

          foreach ($selection as $id) {
            $organisme = $this->getDoctrine()
              ->getRepository('AppBundle:Organisme')
              ->find($id);
            $fields = array(
              $organisme->getNom(),
              $organisme->getNomTitulaire(),
              $organisme->getFonctionTitulaire(),
              $organisme->getAdresse(),
              $organisme->getAdresseComp(),
              $organisme->getCp(),
              $organisme->getVille(),
            );
            $csv->addLine($fields);
          }
          $csv->generateContent('exports/last-'.$this->getUser()->getId().'.csv');
          
          $path = $this->generateUrl('download_last_export',['fileName'=>'export_liste-organismes','type'=>'csv']);
          break;
        default:
          
          break;
      }

      return new Response($path);
    }


	/**
	* @Route("/organisme/delete/{idOrganisme}", name="delete_organisme")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function deleteOrganismeAction($idOrganisme)
	{

    $this->get('app.security')->checkAccess('ORGANISME_DELETE');

    $organisme = $this->getDoctrine()
      ->getRepository('AppBundle:Organisme')
      ->find($idOrganisme);
		
		$contact = $organisme->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($organisme);
		$em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_organismes',array('idContact'=>$contact->getId()));
	}

}