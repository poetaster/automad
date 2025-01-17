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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { Section } from '../components/Switcher/Switcher';

export interface SystemSectionData {
	section: Section;
	icon: string;
	title: string;
	info: string;
	state: string;
	render: Function;
	narrowIcon?: boolean;
}

interface CacheSettings {
	enabled: boolean | 0 | 1;
	lifetime: number;
	monitorDelay: number;
}

interface FeedSettings {
	enabled: boolean | 0 | 1;
	fields: string;
}

interface UserSettings {
	name: string;
	email: string;
}

export interface SystemSettings {
	cache: CacheSettings;
	debug: boolean | 0 | 1;
	feed: FeedSettings;
	translation: string;
	users: UserSettings[];
	tempDirectory: string;
}

export interface SystemUpdateResponse {
	state: string;
	current: string;
	latest: string;
	items: string[];
}

export interface User {
	name: string;
	email: string;
}
