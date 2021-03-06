<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Support;
use AppBundle\Form\SupportType;


class SupportController extends Controller
{


	/**
	* @Security("has_role('ROLE_SPECTATOR')")
 	*/
    public function displaySupportFormAction(){

    	$support = new Support();

        $supportForm =  $this->createForm(SupportType::class, $support,array(
			'action'=>$this->generateUrl('send_support')
		));

		return new Response($this->render('modals/support.html.twig',['supportForm'=>$supportForm->createView()])->getContent());
    }


	/**
	* @Route("/support/send", name="send_support")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function sendSupportAction(Request $request)
	{

		$this->get('app.security')->checkAccess('SUPPORT_USE');

		$support = new Support();

		$supportForm = $this->createForm(SupportType::class, $support);
		$supportForm->handleRequest($request);

		if ($supportForm->isSubmitted() && $supportForm->isValid()) {
			$support->setOperateur($this->getUser());
			$support->setDate(new \DateTime());
			
			$message = \Swift_Message::newInstance()
		        ->setSubject('GESTUNIAT - Demande de support')
		        ->setFrom('support@gestuniat.fr')
		        ->setTo('dev@adn-studio.fr')
		        ->setBody(
		            $this->renderView(
		                'emails/support.html.twig',
		                array('support' => $support)
		            ),
		            'text/html'
        		);

	        $this->get('mailer')->send($message);

			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($support);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Demande envoyée !');
		}
		if ($supportForm->isSubmitted() && !$supportForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($supportForm);
		}


		return $this->redirect($request->headers->get('referer'));
	}

}