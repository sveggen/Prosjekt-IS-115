<?php


namespace App\controllers;

use Symfony\Component\HttpFoundation\Response;

class LoginController{

    public function login(){

        return new Response(
            '<html><body>Lucky place</body></html>'
        );

    }


}

?>