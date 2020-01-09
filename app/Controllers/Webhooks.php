<?php namespace App\Controllers;

use Exception;

class Webhooks extends BaseController
{
	/**
	 * @throws Exception
	 */
	public function deploy()
	{
		$signature = 'sha1='.hash_hmac(
			'sha1',
			file_get_contents('php://input'),
			$_SERVER['GITHUB_WEBHOOK_SECRET']
		);

		$validSignature =
			hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE']);

		if (!$validSignature) throw new Exception('Incorrect Signature');

		$this->gitPull();
		$this->obfuscateGameJs();
	}

	/**
	 * @throws Exception
	 */
	private function gitPull()
	{
		helper('shell');

		$gitReset = terminal('
			/usr/local/cpanel/3rdparty/lib/path-bin/git reset --hard origin/master 2>&1
		');

		if ($gitReset['status'] !== 0)
			throw new Exception('Git reset Failure: '.$gitReset['output']);

		$gitPull = terminal('
			/usr/local/cpanel/3rdparty/lib/path-bin/git pull 2>&1
		');

		if ($gitPull['status'] !== 0)
			throw new Exception('Git pull failure: '.$gitPull['output']);

		echo $gitPull['output'];
	}

	/**
	 * @throws Exception
	 */	private function obfuscateGameJs()
	{
		helper('shell');

		$obfuscateGame = terminal('
			/home/pvhhqumha6t1/bin/node /home/pvhhqumha6t1/public_html/assets/js/obfuscate-game.js 2>&1
		');

		if ($obfuscateGame['status'] !== 0)
			throw new Exception('Obfuscation failure: '.$obfuscateGame['output']);

		echo $obfuscateGame['output'];
	}
}
