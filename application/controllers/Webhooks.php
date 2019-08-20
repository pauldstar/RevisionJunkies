<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Webhooks extends CI_Controller
{
	public function deploy()
	{
		$signature = 'sha1='.hash_hmac(
			'sha1',
			file_get_contents('php://input'),
			$_SERVER['GITHUB_WEBHOOK_SECRET']
		);

		$valid_signature =
			hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE']);

		if (!$valid_signature) throw new Exception('Incorrect Signature');

		self::_git_pull();
		self::_obfuscate_game_js();
	}

	private function _git_pull()
	{
		$this->load->helper('shell');

		$git_reset = terminal('
			/usr/local/cpanel/3rdparty/lib/path-bin/git reset --hard origin/master 2>&1
		');

		if ($git_reset['status'] !== 0)
			throw new Exception('Git reset Failure: '.$git_reset['output']);

		$git_pull = terminal('
			/usr/local/cpanel/3rdparty/lib/path-bin/git pull 2>&1
		');

		if ($git_pull['status'] !== 0)
			throw new Exception('Git pull failure: '.$git_pull['output']);

		echo $git_pull['output'];
	}

	private function _obfuscate_game_js()
	{
		$this->load->helper('shell');

		$obfuscate_game = terminal('
			/home/pvhhqumha6t1/bin/node /home/pvhhqumha6t1/public_html/assets/js/obfuscate-game.js 2>&1
		');

		if ($obfuscate_game['status'] !== 0)
			throw new Exception('Obfuscation failure: '.$obfuscate_game['output']);

		echo $obfuscate_game['output'];
	}
}
