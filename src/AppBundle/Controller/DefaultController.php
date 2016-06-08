<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Alerte;
use AppBundle\Entity\Section;
use AppBundle\Entity\Effectif;
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
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function logoutAction()
    {

    }


    /**
     * @Route("/dashboard", name="dashboard")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function dashboardAction(Request $request)
    {
        $session = $this->get('session');

        $alerte = new Alerte();
        $alerteForm = $this->createForm(AlerteCreationType::class, $alerte);
        $alerteForm->handleRequest($request);
        $datetime = new \DateTime();

        if ($alerteForm->isSubmitted() && $alerteForm->isValid()) {
                

            $alerte->setDateCreation($datetime);
            $alerte->setOperateur($this->getUser());
            $alerte->setIsOk(false);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($alerte);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

            return  $this->redirectToRoute('dashboard');

        }else{

            $alertes = $this->getDoctrine()
            ->getRepository('AppBundle:Alerte')
            ->findBy(array('operateur'=>$this->getUser()),array('dateEcheance'=>'ASC'));

            $suivis = $this->getDoctrine()
            ->getRepository('AppBundle:Suivi')
            ->findBy(array(),array('dateEcheance'=>'ASC'));

            $lstTerms = array();

            foreach ($alertes as $_alerte) {
                $lstTerms[$_alerte->getDateEcheance()->format('Y-m-d').'_'.$_alerte->getId()] = $_alerte;
            }
            foreach ($suivis as $suivi) {
                if ($suivi->getDateEcheance()) {
                    $lstTerms[$suivi->getDateEcheance()->format('Y-m-d').'_'.$suivi->getId()] = $suivi;
                }
            }

            ksort($lstTerms);

            $dateYesterday = new \DateTime(date('Y-m-d').' -1 day');
            $dateToday = new \DateTime();
            $dateTomorrow = new \DateTime(date('Y-m-d').' +1 day');
            $dateNextWeek = new \DateTime(date('Y-m-d').' +7 day');

            $lstAlertes = ['late'=>array(),'now'=>array(),'incoming'=>array()];
            $lstHistory = array();
            $lstFuturTerms = array();

            foreach ($lstTerms as $key => $term) {
                if ($term->getDateEcheance()->format('Y-m-d') <= $dateYesterday->format('Y-m-d')) {
                    if(!$term->getIsOk()){
                        $lstAlertes['late'][] = $term;
                    }
                }elseif ($term->getDateEcheance()->format('Y-m-d') >= $dateTomorrow->format('Y-m-d') && $term->getDateEcheance()->format('Y-m-d') <= $dateNextWeek->format('Y-m-d')) {
                    if(!$term->getIsOk()){
                        $lstAlertes['incoming'][] = $term;
                    }
                }elseif($term->getDateEcheance()->format('Y-m-d') == $dateToday->format('Y-m-d') ){
                    if(!$term->getIsOk()){
                        $lstAlertes['now'][] = $term;
                    }
                }

                if($term->getDateEcheance()->format('Y-m-d') > $dateNextWeek->format('Y-m-d') ){
                    if(!$term->getIsOk()){
                        $lstFuturTerms[] = $term;
                    }
                }

                if($term->getIsOk()){
                    $lstHistory[] = $term;
                }
            }

            $this->updateAlertesInSession();

            // retrieve missing timbres
            $lateSections = array();

            $echeanceRemise = new \DateTime($datetime->format('Y')."-02-15");
            if($datetime>$echeanceRemise){
                $sections = $this->getDoctrine()
                    ->getRepository('AppBundle:Section')
                    ->findAll();

                foreach ($sections as $section) {
                    $remiseTimbre = $this->getDoctrine()
                        ->getRepository('AppBundle:RemiseTimbre')
                        ->findOneBy(array('annee'=>$datetime->format('Y')-1,'section'=>$section));

                    if(!$remiseTimbre){
                        $lateSections[] = $section;
                    }

                }
            }

            // retrieve late vignettes
            $vignettes = $this->getDoctrine()
                ->getRepository('AppBundle:Vignette')
                ->findBy(array('datePaiement'=>null));


            // retrieve expiring OffreDecouvertes
            $contactsDecouvertes = $this->getDoctrine()
                ->getRepository('AppBundle:Contact')
                ->findBy(array('isOffreDecouverte'=>true));

            $prevTrimestre = new \DateTime();
            $prevTrimestre->sub(new \DateInterval('PT9M'));

            $expiringOffres = 0;
            foreach ($contactsDecouvertes as $contactD) {
                if($contactD->getDateOffreDecouverte() && $contactD->getDateOffreDecouverte() < $prevTrimestre){
                    $expiringOffres++;
                }
            }

            //late cotisations
            $sectionDivers = $this->getDoctrine()
                ->getRepository('AppBundle:Section')
                ->find(Section::getIdSectionDivers());

            $contactsDivers = $this->getDoctrine()
                ->getRepository('AppBundle:Contact')
                ->findBy(array('section'=>$sectionDivers));

            dump($contactsDivers);
            $nbUnpaidCotisations = 0;
            foreach ($contactsDivers as $_contact) {
                $paid = false;
                $cotisations = $this->getDoctrine()
                    ->getRepository('AppBundle:cotisation')
                    ->findBy(array('contact'=>$_contact));
                
                foreach ($cotisations as $cotisation) {
                    if($cotisation->getDatePaiement() && $cotisation->getDatePaiement()->format('Y') == $dateToday->format('Y')){
                        $paid = true;
                    }
                }
                if(!$paid){
                    $nbUnpaidCotisations++;
                }
            }

            return $this->render('operateur/dashboard.html.twig', [
                'alerteForm' => $alerteForm->createView(),
                'lstAlertes' => $lstAlertes,
                'lstHistory' => $lstHistory,
                'lateSections' => $lateSections,
                'unpaidVignettes' => sizeof($vignettes),
                'nbUnpaidCotisations' => $nbUnpaidCotisations,
                'expiringOffres' => $expiringOffres,
                'lstFuturTerms' => $lstFuturTerms,
            ]);
        }

    }


    /**
     * @Route("/admin/reglages", name="reglages")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function reglagesAction(Request $request)
    {

        return $this->render('admin/reglages.html.twig');
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

                $this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');
            }
        }

        return $this->redirectToRoute('dashboard');

    }

    /**
     * @Route("/show-edit-alerte", name="show_edit_alerte")
     * @Method("POST")
     * @Security("has_role('ROLE_SPECTATOR')")
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

            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

            $this->updateAlertesInSession();

        }
        if ($alerteForm->isSubmitted() && !$alerteForm->isValid()) {
            $this->get('app.tools')->handleFormErrors($alerteForm);
        }
        
        return  $this->redirectToRoute('dashboard');

    }

    private function updateAlertesInSession(){
        $alertes = $this->getDoctrine()
            ->getRepository('AppBundle:Alerte')
            ->findBy(array('operateur'=>$this->getUser(),'isOk'=>false),array('dateEcheance'=>'ASC'));

        $suivis = $this->getDoctrine()
            ->getRepository('AppBundle:Suivi')
            ->findBy(array('operateur'=>$this->getUser(),'isOk'=>false),array('dateEcheance'=>'ASC'));


        $lstTerms = array();

        foreach ($alertes as $_alerte) {
            $lstTerms[$_alerte->getDateCreation()->format('Y-m-d H:i:s')] = $_alerte;
        }
        foreach ($suivis as $suivi) {
            if ($suivi->getDateEcheance()) {
                $lstTerms[$suivi->getDateCreation()->format('Y-m-d H:i:s')] = $suivi;
            }
        }

        ksort($lstTerms);

        $dateToday = new \DateTime(date('Y-m-d'));

        $lateAlertes = array();
        foreach ($lstTerms as $key => $term) {
            if ($term->getDateEcheance()->format('Y-m-d') <= $dateToday->format('Y-m-d')) {
                $lateAlertes[] = $term;
            }
        }

        $session = $this->get('session');
        $session->set('nbAlertes', sizeof($lateAlertes));
        $session->set('lateAlertes', $lateAlertes);

    }

    /**
     * @Route("/routines/effectifs", name="save_effectif")
     */
    public function saveEffectifAction(Request $request)
    {

        $datetime = new \DateTime();

        $sections = $this->getDoctrine()
            ->getRepository('AppBundle:Section')
            ->findAll();

        foreach ($sections as $section) {
            $nbAdhs = $this->getDoctrine()
                ->getRepository('AppBundle:Contact')
                ->countContactsBySection($section);

            $effectif = new Effectif();
            $effectif->setAnnee($datetime->format('Y'));
            $effectif->setValeur($nbAdhs);
            $effectif->setSection($section);

            $em = $this->getDoctrine()->getManager();
            $em->persist($effectif);
            $em->flush();

            $this->get('app.suivi')->createForSection($section,'Sauvegarde automatique des effectifs effectuée ('.$nbAdhs.' pour l\'année '.$datetime->format('Y').')');
        }

        return new Response('',Response::HTTP_OK);
    }
}