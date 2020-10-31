<?php

namespace App\controllers;

use Symfony\Component\HttpFoundation\Response;

class IndexController{

    public function index(){

        return new Response(
            '<html><body>Lucky number</body></html>'
        );
        
    }


}




?>