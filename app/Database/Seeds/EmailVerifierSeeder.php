<?php namespace App\Database\Seeds;

use App\Models\EmailVerifier;
use App\Models\User;
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
    $users = (new User)->findColumn('id');
    $verifier = new EmailVerifier;

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
