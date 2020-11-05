<?php

namespace App\controllers;

use App\models\DatabaseTest;
use Symfony\Component\HttpFoundation\Response;


class Index extends Base
{

    public function index()
    {


        $model = new DatabaseTest();
        $members = $model->getAllMembers();

        $item = array();
        while ($row = $members->fetch_assoc()) {
            array_push($item, $row);
    }
            return new Response(
                $this->twig->render('index.html.twig',
                    ['name' => $item]));

    }
}