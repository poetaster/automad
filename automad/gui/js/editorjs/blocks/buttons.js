/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2020-2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


class AutomadBlockButtons {

	static get isReadOnlySupported() {
		return true;
	}

	static get sanitize() {
		return {
			primaryText: {},
			primaryLink: false,
			secondaryText: {},
			secondaryLink: false
		}
	}

	static get toolbox() {
		return {
			title: AutomadEditorTranslation.get('buttons_toolbox'),
			icon: '<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="15px" viewBox="0 0 18 15"><path d="M16,2.359c0,0,0,0.001,0,0.002C15,0.972,13.623,0,12,0H4C1.791,0,0,1.791,0,4v5c0,1.624,0.972,3,2.362,4 c-0.001,0-0.001,0-0.002,0C2.987,14,4.377,15,6,15h8c2.209,0,4-1.791,4-4V6C18,4.377,17,2.987,16,2.359z M2,4c0-1.103,0.897-2,2-2h8 c1.103,0,2,0.897,2,2v5c0,1.103-0.897,2-2,2H4c-1.103,0-2-0.897-2-2V4z"/><path d="M6,8H5C4.171,8,3.5,7.329,3.5,6.5S4.171,5,5,5h1c0.828,0,1.5,0.671,1.5,1.5S6.828,8,6,8z"/><path d="M11,8h-1C9.172,8,8.5,7.329,8.5,6.5S9.172,5,10,5h1c0.828,0,1.5,0.671,1.5,1.5S11.828,8,11,8z"/></svg>'
		};
	}

	constructor({data, api, config}) {

		var create = Automad.util.create,
			t = AutomadEditorTranslation.get;

		this.api = api;

		this.data = {
			primaryText: data.primaryText || '',
			primaryLink: data.primaryLink || '',
			secondaryText: data.secondaryText || '',
			secondaryLink: data.secondaryLink || '',
			alignment: data.alignment || 'left'
		};

		this.layoutSettings = AutomadLayout.renderSettings(this.data, data, api, config);

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.innerHTML = `
			<div class="am-block-icon">${AutomadBlockButtons.toolbox.icon}</div>
			<div class="am-block-title">${AutomadBlockButtons.toolbox.title}</div>
			<hr>
			<ul class="uk-grid uk-grid-width-medium-1-2 uk-form">
				<li>
					${create.label(t('button_primary_label')).outerHTML}
					${create.editable(['cdx-input', 'am-block-primary-text'], '', this.data.primaryText).outerHTML}
					${create.label(t('button_primary_link')).outerHTML}
					<div class="am-form-icon-button-input uk-flex">
						<button type="button" class="uk-button uk-button-large">
							<i class="uk-icon-link"></i>
						</button>
						<input type="text" class="am-block-primary-link uk-form-controls uk-width-1-1" value="${this.data.primaryLink}" />
					</div>
				</li>
				<li>
					${create.label(t('button_secondary_label')).outerHTML}
					${create.editable(['cdx-input', 'am-block-secondary-text'], '', this.data.secondaryText).outerHTML}
					${create.label(t('button_secondary_link')).outerHTML}
					<div class="am-form-icon-button-input uk-flex">
						<button type="button" class="uk-button uk-button-large">
							<i class="uk-icon-link"></i>
						</button>
						<input type="text" class="am-block-secondary-link uk-form-controls uk-width-1-1" value="${this.data.secondaryLink}" />
					</div>
				</li>
			</ul>`;

		var linkButtons = this.wrapper.querySelectorAll('button');

		for (let i = 0; i < linkButtons.length; ++i) {
			api.listeners.on(linkButtons[i], 'click', function() {
				Automad.link.click(linkButtons[i]);
			});
		}
		
		this.inputs = {
			primaryText: this.wrapper.querySelector('.am-block-primary-text'),
			primaryLink: this.wrapper.querySelector('.am-block-primary-link'),
			secondaryText: this.wrapper.querySelector('.am-block-secondary-text'),
			secondaryLink: this.wrapper.querySelector('.am-block-secondary-link')
		}

		this.settings = [
			{
				title: t('left'),
				name: 'left',
				icon: AutomadEditorIcons.get.alignLeft
			},
			{
				title: t('center'),
				name: 'center',
				icon: AutomadEditorIcons.get.alignCenter
			}
		]
		
	}

	render() {

		return this.wrapper;

	}

	save() {

		return Object.assign(this.data, {
			primaryText: this.inputs.primaryText.innerHTML,
			primaryLink: this.inputs.primaryLink.value.trim(),
			secondaryText: this.inputs.secondaryText.innerHTML,
			secondaryLink: this.inputs.secondaryLink.value.trim()
		});

	}

	renderSettings() {

		var wrapper = document.createElement('div'),
			inner = document.createElement('div'),
			block = this;

		inner.classList.add('cdx-settings-1-2');

		this.settings.map(function(tune) {

			var el = document.createElement('div');

			el.innerHTML = tune.icon;
			el.classList.add(block.api.styles.settingsButton);
			el.classList.toggle(block.api.styles.settingsButtonActive, tune.name === block.data.alignment);

			block.api.tooltip.onHover(el, tune.title, { placement: 'top' });
			inner.appendChild(el);

			return el;

		}).forEach(function(element, index, elements) {

			element.addEventListener('click', function() {

				block.toggleTune(block.settings[index].name);

				elements.forEach((el, i) => {

					var name = block.settings[i].name;

					el.classList.toggle(block.api.styles.settingsButtonActive, name === block.data.alignment);
				
				});
			});

		});

		wrapper.appendChild(inner);
		wrapper.appendChild(this.layoutSettings);

		return wrapper;

	}

	toggleTune(tune) {
		this.data.alignment = tune;
	}

}