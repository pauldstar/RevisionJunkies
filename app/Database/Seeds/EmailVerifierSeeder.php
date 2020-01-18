<?php namespace App\Database\Seeds;

use App\Models\EmailVerifierModel;
use App\Models\UserModel;
use CodeIgniter\Database\Seeder;
use ReflectionException;

class EmailVerifierSeeder extends Seeder
{
  /**
   * @return mixed|void
   * @throws ReflectionException
   */
  public function run()
  {
    $users = (new UserModel)->findColumn('id');
    $verifier = new EmailVerifierModel;

    foreach ($users as $id)
    {
      $data = [
        'user_id' => $id,
        'verifier' => random_string('alnum', 10),
      ];

      $verifier->insert($data);
    }
  }
}
