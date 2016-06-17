<?php

namespace AppBundle\Utils;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Security extends Controller
{

	function __construct() {
	
	}

	public function checkAccess($action){
		if($this->hasAccess($action)){
			
		}else{
			throw new \Exception("Permission denied : ".$action, 1);
		}
	}

	public function hasAccess($action){
		$permission = $this->getDoctrine()
						->getRepository('AppBundle:Permission')
						->findOneBy(array('code'=>$action));

		if(!$permission){
			throw new \Exception("Permission not found : ".$action, 1);
			return false;
		}

		$roles = $this->getUser()->getRoles();
		
		foreach ($permission->getPermissionRoles() as $permissionRole) {
			foreach ($roles as $role) {
				if($role == 'ROLE_'.$permissionRole->getRole()){
					return true;
				}
			}
		}

		return false;
	}

}