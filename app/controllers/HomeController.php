<?php

namespace App\controllers;

class HomeController {

    public function index(): void {
        require_once "app/views/home.php";
    }

}