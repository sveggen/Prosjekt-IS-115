<?php


namespace App\controllers;


use Symfony\Component\HttpFoundation\Response;

class AddMember extends BaseController {

    public function addMember(): Response {
        if ($this->hasLeaderPrivileges() == false){
            return $this->methodNotAllowed();
        }
    }

}