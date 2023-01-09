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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Automad;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\System\Fields;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Shared data controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SharedController {
	/**
	 * Send form when there is no posted data in the request or save data if there is.
	 *
	 * @return Response the response object
	 */
	public static function data() {
		$Response = new Response();
		$Automad = Automad::fromCache();
		$Shared = $Automad->Shared;
		$data = Request::post('data');

		if (!empty($data) && is_array($data)) {
			if (filemtime(AM_FILE_SHARED_DATA) > Request::post('dataFetchTime')) {
				return $Response->setError(Text::get('preventDataOverwritingError'))->setCode(403);
			}

			$Messenger = new Messenger();

			if (!$Shared->save($data, $Messenger)) {
				return $Response->setError($Messenger->getError());
			}

			if (!empty($data[AM_KEY_THEME]) && $data[AM_KEY_THEME] != $Shared->get(AM_KEY_THEME)) {
				$Response->setReload(true);
			}

			return $Response;
		}

		$ThemeCollection = new ThemeCollection();
		$mainThemeName = $Shared->get(AM_KEY_THEME) ? $Shared->get(AM_KEY_THEME) : array_keys($ThemeCollection->getThemes())[0];
		$Theme = $ThemeCollection->getThemeByKey($mainThemeName);
		$keys = Fields::inTheme($Theme);

		$fields = array_merge(
			array_fill_keys(Fields::$reserved, ''),
			array_fill_keys($keys, ''),
			$Shared->data
		);

		ksort($fields);

		return $Response->setData(array('fields' => $fields));
	}
}