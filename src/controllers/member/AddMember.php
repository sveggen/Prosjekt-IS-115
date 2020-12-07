<?php


namespace App\controllers\member;


use App\controllers\BaseController;
use Symfony\Component\HttpFoundation\Response;

class AddMember extends BaseController {

    public function addMember(): Response {
        if ($this->hasLeaderPrivileges() == false){
            return $this->methodNotAllowed();
        }
    }

}