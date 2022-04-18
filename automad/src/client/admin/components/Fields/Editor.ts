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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, FormDataProviders } from '../../core';
import { KeyValueMap } from '../../types';
import { BaseFieldComponent } from './BaseField';

/**
 * A block editor field.
 *
 * @extends BaseFieldComponent
 */
class EditorComponent extends BaseFieldComponent {
	/**
	 * The editor value that serves a input value for the parent form.
	 */
	value: KeyValueMap;

	/**
	 * If true the field data is sanitized.
	 */
	protected sanitize = false;

	/**
	 * Render the field.
	 */
	createInput(): void {
		const { name, id, value } = this._data;
		const editor = create('div', [], { id }, this);

		this.setAttribute('name', name);
		this.value = value as KeyValueMap;
	}
}

FormDataProviders.add('am-editor');
customElements.define('am-editor', EditorComponent);