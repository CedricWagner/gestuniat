<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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

        $alerte = new Alerte();
        $alerteForm = $this->createForm(AlerteCreationType::class, $alerte);
        $alerteForm->handleRequest($request);

        if ($alerteForm->isValid()) {
                
            $datetime = new \DateTime();

            $alerte->setDateCreation($datetime);
            $alerte->setOperateur($this->getUser());

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($alerte);
            $em->flush();

        }

        return $this->render('operateur/dashboard.html.twig', [
            'alerteForm' => $alerteForm->createView(),
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

}
