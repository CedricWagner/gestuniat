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



class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        // replace this example code with whatever you need
        return $this->render('front/authentication.html.twig', [
            'error' => $error,
        ]);
    }


    /**
     * @Route("/login-check", name="login_check")
     * @Method("POST")
     */
    public function loginCheckAction()
    {

    }

    /**
     * @Route("/logout", name="logout")
     * @Security("has_role('ROLE_USER')")
     */
    public function logoutAction()
    {

    }

    /**
     * @Route("/dashboard", name="dashboard")
     * @Security("has_role('ROLE_USER')")
     */
    public function dashboardAction(Request $request)
    {
        $session = $this->get('session');

        $alerte = new Alerte();
        $alerteForm = $this->createForm(AlerteCreationType::class, $alerte);
        $alerteForm->handleRequest($request);

        if ($alerteForm->isValid()) {
                
            $datetime = new \DateTime();

            $alerte->setDateCreation($datetime);
            $alerte->setOperateur($this->getUser());
            $alerte->setIsOk(false);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($alerte);
            $em->flush();

        }

        $alertes = $this->getDoctrine()
        ->getRepository('AppBundle:Alerte')
        ->findBy(array('operateur'=>$this->getUser()),array('dateEcheance'=>'ASC'));

        $dateYesterday = new \DateTime(date('Y-m-d').' -1 day');
        $dateTomorrow = new \DateTime(date('Y-m-d').' +1 day');

        $lstAlertes = ['late'=>array(),'now'=>array(),'incoming'=>array()];
        $lstHistory = array();
        $nbAlertes = 0;

        foreach ($alertes as $alerte) {
            if ($alerte->getDateEcheance() <= $dateYesterday) {
                if(!$alerte->getIsOk()){
                    $lstAlertes['late'][] = $alerte;
                    $nbAlertes++;
                }
            }elseif ($alerte->getDateEcheance() >= $dateTomorrow) {
                $lstAlertes['incoming'][] = $alerte;
            }else{
                if(!$alerte->getIsOk()){
                    $lstAlertes['now'][] = $alerte;
                    $nbAlertes++;
                }
            }
            if($alerte->getIsOk()){
                $lstHistory[] = $alerte;
            }
        }

        $session->set('nbAlertes', $nbAlertes);

        return $this->render('operateur/dashboard.html.twig', [
            'alerteForm' => $alerteForm->createView(),
            'lstAlertes' => $lstAlertes,
            'lstHistory' => $lstHistory,
        ]);

    }

    /**
     * @Route("/insert-operateur", name="insert_operateur")
     * @Method("GET")
     */
    public function insertOperateurAction(Request $request)
    {
        // $operateur = $this->get('app.operateur.factory')->createForRegistration();
        
        // $password = $this
        //     ->get('security.password_encoder')
        //     ->encodePassword($operateur, $request->query->get('password'));
        // $operateur->setMdp($password);
        // $operateur->setLogin($request->query->get('login'));
        // $operateur->setRole('admin');
        // $operateur->setNom('nom');
        // $operateur->setPrenom('prenom');
        
        // $em = $this->get('doctrine.orm.entity_manager');
        // $em->persist($operateur);
        // $em->flush();
    }

    /**
     * @Route("/check-alerte", name="check_alerte")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function checkAlerteAction(Request $request)
    {
        if($request->isXmlHttpRequest()){
            $idAlerte = $request->request->get('idAlerte');
            $target = $request->request->get('target'); 
            $action = $request->request->get('action');

            if($action=='done'){
                $value = false;
            }else{
                $value = true;
            }

            if($target == 'alerte'){
                $alerte = $this->getDoctrine()
                   ->getRepository('AppBundle:Alerte')
                   ->find($idAlerte);

                $alerte->setIsOk($value);

                $em = $this->getDoctrine()->getManager();
                $em->persist($alerte);
                $em->flush();
            }


            return new Response(json_encode(['state'=>'success'])); 
       }else{
            return new Response(json_encode(['state'=>'noXHR']));     
       }
    }

}