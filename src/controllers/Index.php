<?php

namespace App\controllers;

use App\models\DatabaseTest;
use Symfony\Component\HttpFoundation\Response;

class Index
{

    public function index()
    {

        $model = new DatabaseTest();
        $members = $model->getAllMembers();

        while ($row = $members->fetch_assoc()) {

            return new Response($row['firstname']);

        }
    }
}