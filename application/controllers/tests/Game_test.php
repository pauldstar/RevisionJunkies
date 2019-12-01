<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game_test extends QP_Test_Controller
{
   public function __construct()
   {
     parent::__construct();
     $this->load->model('game_model', '_game');
   }

   public function start_time()
   {
     echo 'dance';
   }
 }
