<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Webhooks extends QP_Controller
{
	public function __construct()
	{
		parent::__construct();

		$signature = 'sha1='.hash_hmac(
			'sha1',
			$_POST['payload'],
			$_ENV['github_webhook_secret']
		);

		$valid_signature = hash_equals($signature, $_SERVER['x-hub-signature']);

		if (!$valid_signature) throw new Exception('Incorrect Signature');
	}

	public function deploy()
	{
		// secret QDjzkg574GjPw29B
		echo '<pre>';
		var_dump($_POST);
		echo '</pre>';
		echo '<pre>';
		var_dump($_SERVER);
		echo '</pre>';

		$signature = 'sha1='.
			hash_hmac('sha1', Request::getContent(), $_ENV['github_webhook_secret']);
	}
}
