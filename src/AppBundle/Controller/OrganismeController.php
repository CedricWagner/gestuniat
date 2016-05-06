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


class OrganismeController extends Controller
{

	 /**
     * @Route("/organisme/liste/{idFilter}/{page}/{nb}", name="list_organismes", defaults={"idFilter" = 0,"page" = 1,"nb" = 0})
     * @Security("has_role('ROLE_USER')")
     */
    public function listOrganismesAction($idFilter,$page,$nb)
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
            'pagination' => array('count'=>count($organismes),'nb'=>$nb,'page'=>$page),
        ]);

    }

    /**
     * @Route("/organisme/show-edit", name="show_edit_organisme")
     * @Security("has_role('ROLE_USER')")
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
    		])->getContent());
    }

	/**
	* @Route("/organisme/save", name="save_organisme")
	* @Security("has_role('ROLE_USER')")
	*/
	public function saveOrganismeAction(Request $request)
	{

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
		}

		return $this->redirectToRoute('list_organismes');
	}


	/**
	* @Route("/organisme/delete/{idOrganisme}", name="delete_organisme")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteOrganismeAction($idOrganisme)
	{

	    $organisme = $this->getDoctrine()
	      ->getRepository('AppBundle:Organisme')
	      ->find($idOrganisme);
		
		$contact = $organisme->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($organisme);
		$em->flush();

	    return $this->redirectToRoute('list_organismes',array('idContact'=>$contact->getId()));
	}

}