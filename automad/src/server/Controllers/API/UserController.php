<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2016-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Session;
use Automad\Core\Text;
use Automad\Models\UserCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The User class provides all methods related to a user account.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UserController {
	/**
	 * Change the password of the currently logged in user based on $_POST.
	 *
	 * @return Response the response object
	 */
	public static function changePassword(): Response {
		$Response = new Response();
		$currentPassword = Request::post('currentPassword');
		$newPassword1 = Request::post('newPassword1');
		$newPassword2 = Request::post('newPassword2');

		if (!$currentPassword || !$newPassword1 || !$newPassword2) {
			return $Response->setError(Text::get('invalidFormError'));
		}

		if ($newPassword1 !== $newPassword2) {
			return $Response->setError(Text::get('passwordRepeatError'));
		}

		if ($currentPassword === $newPassword1) {
			return $Response->setError(Text::get('passwordReuseError'));
		}

		$UserCollection = new UserCollection();
		$User = $UserCollection->getUser(Session::getUsername());
		$Messenger = new Messenger();

		if (!$User) {
			return $Response->setError(Text::get('userNotFoundError'));
		}

		$User->changePassword($currentPassword, $newPassword1, $UserCollection, $Messenger);

		return $Response
				->setError($Messenger->getError())
				->setSuccess($Messenger->getSuccess());
	}

	/**
	 * Edit user account info such as username and email.
	 *
	 * @return Response the response
	 */
	public static function edit(): Response {
		$Response = new Response();
		$Messenger = new Messenger();
		$UserCollection = new UserCollection();

		$username = Request::post('username');
		$email = Request::post('email');

		if ($UserCollection->editCurrentUserInfo($username, $email, $Messenger)) {
			if ($UserCollection->save($Messenger)) {
				return $Response->setSuccess(Text::get('savedSuccess'));
			}
		}

		return $Response->setError($Messenger->getError());
	}

	/**
	 * Reset a user password by email.
	 *
	 * @return Response the Response object
	 */
	public static function resetPassword(): Response {
		$Response = new Response();
		$UserCollection = new UserCollection();
		$Messenger = new Messenger();

		// Only one field will be defined, so they can just be concatenated here.
		$nameOrEmail = trim(Request::post('name-or-email') . Request::post('username'));

		$token = Request::post('token');
		$newPassword1 = Request::post('password1');
		$newPassword2 = Request::post('password2');

		$User = $UserCollection->getUser($nameOrEmail);

		if ($nameOrEmail && !$User) {
			return $Response->setError(Text::get('userNotFoundError'));
		}

		if (!$User) {
			return $Response->setData(array('state' => 'requestToken'));
		}

		$responseData = array('username' => $User->name);

		if ($token && $newPassword1 && $newPassword2) {
			if ($User->verifyPasswordResetToken($token)) {
				if ($User->resetPassword($newPassword1, $newPassword2, $UserCollection, $Messenger)) {
					$responseData['state'] = 'success';

					return $Response->setData($responseData);
				}

				$responseData['state'] = 'setPassword';

				return $Response->setData($responseData)->setError($Messenger->getError());
			}

			$responseData['state'] = 'setPassword';

			return $Response->setData($responseData)->setError(Text::get('passwordResetVerificationError'));
		}

		if ($User->sendPasswordResetToken($Messenger)) {
			$responseData['state'] = 'setPassword';

			return $Response->setData($responseData);
		}

		return $Response->setError($Messenger->getError());
	}
}
