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
 * Copyright (c) 2013-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine;

use Automad\Core\Automad;
use Automad\Core\Debug;
use Automad\Core\Resolve;
use Automad\Engine\Processors\ContentProcessor;
use Automad\Engine\Processors\PostProcessor;
use Automad\Engine\Processors\TemplateProcessor;
use Automad\UI\InPage;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The View class is responsible for rendering the requeste page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class View {
	/**
	 * The main Automad instance.
	 */
	private $Automad;

	/**
	 * A boolean variable that contains the headless state
	 */
	private $headless;

	/**
	 * The view constructor.
	 *
	 * @param Automad $Automad
	 * @param bool $headless
	 */
	public function __construct(Automad $Automad, bool $headless = false) {
		$this->Automad = $Automad;
		$this->headless = $headless;

		$Page = $Automad->Context->get();

		// Redirect page, if the defined URL variable differs from the original URL.
		if ($Page->url != $Page->origUrl) {
			$url = Resolve::absoluteUrlToRoot(Resolve::relativeUrlToBase($Page->url, $Page));
			header('Location: ' . $url, true, 301);
			exit();
		}

		// Set template.
		if ($this->headless) {
			$this->template = Headless::getTemplate();
		} else {
			$this->template = $Page->getTemplate();
		}

		Debug::log($Page, 'New instance created for the current page');
	}

	/**
	 * Render a page.
	 *
	 * @return string the rendered page
	 */
	public function render() {
		Debug::log($this->template, 'Render template');

		$Runtime = new Runtime($this->Automad);
		$InPage = new InPage();

		$ContentProcessor = new ContentProcessor(
			$this->Automad,
			$Runtime,
			$InPage,
			$this->headless
		);

		$TemplateProcessor = new TemplateProcessor(
			$this->Automad,
			$Runtime,
			$ContentProcessor
		);

		$output = $TemplateProcessor->process(
			$this->Automad->loadTemplate($this->template),
			dirname($this->template)
		);

		$PostProcessor = new PostProcessor($InPage, $this->headless);

		$output = $PostProcessor->process($output);

		return trim($output);
	}
}