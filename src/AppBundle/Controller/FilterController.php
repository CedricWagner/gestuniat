<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\FiltrePerso;
use AppBundle\Entity\FiltreValeur;
use AppBundle\Entity\Champ;



class FilterController extends Controller
{
    /**
     * @Route("/filter/save", name="save_filter")
     * @Security("has_role('ROLE_USER')")
     */
    public function saveFilterAction(Request $request)
    {

    	$context = $request->request->get('context');
    	$fields = $request->request->get('fields');
    	$name = $request->request->get('name');

    	$operateur = $this->getUser();

        //Get the FiltrePerso
    	$filtresPerso = $this->getDoctrine()
                   ->getRepository('AppBundle:FiltrePerso')
                   ->findBy(array('contexte'=>$context,'label'=>$name,'operateur'=>$operateur));

        if(sizeof($filtresPerso)>0){
        	$filtrePerso = $filtresPerso[0];
        
        	//delete old filtreValeur
            $em = $this->getDoctrine()->getManager();
        	$qb = $em->createQueryBuilder();
			$qb->delete('AppBundle:FiltreValeur', 'fv');
			$qb->where('fv.filtrePerso = :filtrePerso');
			$qb->setParameter('filtrePerso', $filtrePerso);
			$qb->getQuery()->execute();

            $action = 'edit';

        }else{
            $filtrePerso = new FiltrePerso();
            $filtrePerso->setLabel($name);
            $filtrePerso->setContexte($context);
            $filtrePerso->setOperateur($operateur);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($filtrePerso);
            $em->flush();

            $action = 'add';

        }

        foreach ($fields as $field) {
	    	
	    	if(array_key_exists('type',$field)&&array_key_exists('name',$field)&&array_key_exists('value',$field)){

	        	//Get the Champ
		    	$champs = $this->getDoctrine()
		     	      ->getRepository('AppBundle:Champ')
		     	      ->findBy(array('contexte'=>$context,'label'=>$field['name']));

		        if(sizeof($champs)>0){
		        	$champ = $champs[0];
		        }else{
		        	$champ = new Champ();
		        	$champ->setLabel($field['name']);
		        	$champ->setType($field['type']);
		        	$champ->setContexte($context);

		            $em = $this->get('doctrine.orm.entity_manager');
		            $em->persist($champ);
		            $em->flush();
		        }

		        //Add FiltreValeur
		        $filtreValeur = new FiltreValeur();
	        	$filtreValeur->setValeur($field['value']);
	        	$filtreValeur->setFiltrePerso($filtrePerso);
	        	$filtreValeur->setChamp($champ);

	            $em = $this->get('doctrine.orm.entity_manager');
	            $em->persist($filtreValeur);
	            $em->flush();

	    	}

        }



        return new Response(json_encode(array('state'=>'success','action'=>$action,'id'=>$filtrePerso->getId(),'label'=>$filtrePerso->getLabel())));
    }

}
