<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Questions_test extends QP_Test_Controller
{
   public function __construct()
   {
     parent::__construct();
     $this->load->model('questions');
   }
 }
