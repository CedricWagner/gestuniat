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

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();


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

        if ($alerteForm->isSubmitted() && $alerteForm->isValid()) {
                
            $datetime = new \DateTime();

            $alerte->setDateCreation($datetime);
            $alerte->setOperateur($this->getUser());
            $alerte->setIsOk(false);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($alerte);
            $em->flush();

            return  $this->redirectToRoute('dashboard');

        }else{

            $alertes = $this->getDoctrine()
            ->getRepository('AppBundle:Alerte')
            ->findBy(array('operateur'=>$this->getUser()),array('dateEcheance'=>'ASC'));

            $dateYesterday = new \DateTime(date('Y-m-d').' -1 day');
            $dateTomorrow = new \DateTime(date('Y-m-d').' +1 day');

            $lstAlertes = ['late'=>array(),'now'=>array(),'incoming'=>array()];
            $lstHistory = array();

            foreach ($alertes as $alerte) {
                if ($alerte->getDateEcheance()->format('Y-m-d') <= $dateYesterday->format('Y-m-d')) {
                    if(!$alerte->getIsOk()){
                        $lstAlertes['late'][] = $alerte;
                    }
                }elseif ($alerte->getDateEcheance()->format('Y-m-d') >= $dateTomorrow->format('Y-m-d')) {
                    $lstAlertes['incoming'][] = $alerte;
                }else{
                    if(!$alerte->getIsOk()){
                        $lstAlertes['now'][] = $alerte;
                    }
                }
                if($alerte->getIsOk()){
                    $lstHistory[] = $alerte;
                }
            }


            $this->updateAlertesInSession();

            return $this->render('operateur/dashboard.html.twig', [
                'alerteForm' => $alerteForm->createView(),
                'lstAlertes' => $lstAlertes,
                'lstHistory' => $lstHistory,
            ]);
        }

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

            $this->updateAlertesInSession();

            return new Response(json_encode(['state'=>'success'])); 
       }else{
            return new Response(json_encode(['state'=>'noXHR']));     
       }
    }

    /**
     * @Route("/delete-alerte", name="delete_alerte")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAlerteAction(Request $request)
    {
        $idAlerte = $request->query->get('idAlerte');
        $target = $request->query->get('target');

        if($target == 'alerte'){
            $alerte = $this->getDoctrine()
               ->getRepository('AppBundle:Alerte')
               ->find($idAlerte);

            if ($alerte) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($alerte);
                $em->flush();
            }
        }

        return $this->redirectToRoute('dashboard');

    }

    /**
     * @Route("/show-edit-alerte", name="show_edit_alerte")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function showEditAlerteAction(Request $request)
    {

        $idAlerte = $request->request->get('idAlerte');
        $target = $request->request->get('target');

        $alerte = $this->getDoctrine()
            ->getRepository('AppBundle:Alerte')
            ->find($idAlerte);
        $alerteForm = $this->createForm(AlerteCreationType::class, $alerte,array(
            'action'=> $this->generateUrl('edit_alerte').'?idAlerte='.$idAlerte,
        ));
        $alerteForm->handleRequest($request);

        return $this->render('modals/editer-alerte.html.twig', [
            'alerte' => $alerte,
            'alerteForm' => $alerteForm->createView(),
        ]);
    }

    /**
     * @Route("/edit-alerte", name="edit_alerte")
     * @Security("has_role('ROLE_USER')")
     */
    public function editAlerteAction(Request $request)
    {

        $idAlerte = $request->query->get('idAlerte');

        $alerte = $this->getDoctrine()
            ->getRepository('AppBundle:Alerte')
            ->find($idAlerte);
        $alerteForm = $this->createForm(AlerteCreationType::class, $alerte,array(
            'action'=> $this->generateUrl('edit_alerte').'?id='.$idAlerte,
        ));
        $alerteForm->handleRequest($request);

        if ($alerteForm->isSubmitted() && $alerteForm->isValid()) {
                
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($alerte);
            $em->flush();

            $this->updateAlertesInSession();

            return  $this->redirectToRoute('dashboard');

        }else{
            
            return  $this->redirectToRoute('dashboard');
            
        }


    }

    private function updateAlertesInSession(){
        $alertes = $this->getDoctrine()
            ->getRepository('AppBundle:Alerte')
            ->findBy(array('operateur'=>$this->getUser(),'isOk'=>false),array('dateEcheance'=>'ASC'));

        $dateToday = new \DateTime(date('Y-m-d'));

        $lateAlertes = array();
        foreach ($alertes as $alerte) {
            if ($alerte->getDateEcheance()->format('Y-m-d') <= $dateToday->format('Y-m-d')) {
                $lateAlertes[] = $alerte;
            }
        }

        $session = $this->get('session');
        $session->set('nbAlertes', sizeof($lateAlertes));
        $session->set('lateAlertes', $lateAlertes);

    }

}
