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

import { Attr, create, html, listen, titleCase } from '../core';
import { DashboardTheme, getTheme, setTheme } from '../core/theme';
import { BaseComponent } from './Base';

/**
 * A theme toggle component for the dashboard.
 *
 * @extends BaseComponent
 */
class DashboardThemeToggleComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add('am-c-dashboard-theme-toggle');
		this.render();
	}

	/**
	 * Render all toggles.
	 */
	private render(): void {
		document.documentElement.classList.add('am-u-no-transition');

		this.innerHTML = '';
		this.renderThemeToggle(DashboardTheme.light, 'sun');
		this.renderThemeToggle(DashboardTheme.lowContrast, 'cloud-moon');
		this.renderThemeToggle(DashboardTheme.dark, 'moon');

		setTimeout(() => {
			document.documentElement.classList.remove('am-u-no-transition');
		}, 800);
	}

	/**
	 * Render a single toggle.
	 *
	 * @param theme
	 * @param icon
	 */
	private renderThemeToggle = (theme: DashboardTheme, icon: string) => {
		const cls: string[] = ['am-c-dashboard-theme-toggle__button'];

		if (theme == getTheme()) {
			cls.push('am-c-dashboard-theme-toggle__button--active');
		}

		const tooltip = `${titleCase(theme.replace('-', ' '))} Theme`;
		const button = create(
			'span',
			cls,
			{
				[Attr.tooltip]: tooltip,
			},
			this
		);

		button.innerHTML = html`<i class="bi bi-${icon}"></i>`;
		listen(button, 'click', () => {
			setTimeout(() => {
				setTheme(theme);
				this.render();
			}, 400);
		});
	};
}

customElements.define(
	'am-dashboard-theme-toggle',
	DashboardThemeToggleComponent
);
