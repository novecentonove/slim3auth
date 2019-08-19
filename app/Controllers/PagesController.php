<?php

namespace App\Controllers;
use App\Models\User;
use App\Controllers;


class PagesController extends Controller{

   public function home($request, $response) {
        return $this->container->view->render($response, 'home.twig');
   }


}

