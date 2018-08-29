<?php
  class Pages extends Controller {
    public function __construct() {
      
    }
 
    public function index() {

      $this->view('pages/index', ['title' => 'Bienvenue sur mon site']);
    }

    public function about() {
      $this->view('pages/about', ['title' => 'about us']);
    
    }
  }