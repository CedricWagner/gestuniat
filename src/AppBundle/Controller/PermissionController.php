<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\PermissionRole;


class PermissionController extends Controller
{

    /**
    * @Route("/admin/permissions", name="list_admin_permissions")
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function listPermissionsAction()
    {

        $permissions = $this->getDoctrine()
                        ->getRepository('AppBundle:Permission')
                        ->findBy(array(),array('contexte'=>'ASC'));

        return $this->render('admin/permissions.html.twig',
            [
                'permissions' => $permissions,
            ]);
    }

    /**
    * @Route("/permission/apply", name="apply_permission")
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function applyPermissionAction(Request $request)
    {

        $idPerm = $request->request->get('idPerm');
        $role = $request->request->get('role');
        $checked = $request->request->get('checked');

        $permission = $this->getDoctrine()
                        ->getRepository('AppBundle:Permission')
                        ->find($idPerm);

        $permissionRole = $this->getDoctrine()
                            ->getRepository('AppBundle:PermissionRole')
                            ->findOneBy(array('permission'=>$permission,'role'=>$role));

        if($checked=="true"){
            //add
            if($permissionRole){
                //well, that's odd
                $em = $this->getDoctrine()->getManager();
                $em->remove($permissionRole);
                $em->flush();
            }
            $permissionRole = new PermissionRole();
            $permissionRole->setPermission($permission);
            $permissionRole->setRole($role);
            $em = $this->getDoctrine()->getManager();
            $em->persist($permissionRole);
            $em->flush();
        }else{
            //remove
            if($permissionRole){
                $em = $this->getDoctrine()->getManager();
                $em->remove($permissionRole);
                $em->flush();
            }
        }

        return new Response('ok');
    }


}