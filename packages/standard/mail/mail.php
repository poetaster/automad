<?php
/*
 *	Standard/Mail
 *	
 *	Mail Extension for Automad
 *
 *	Copyright (c) 2017-2019 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Standard;
use Automad\System as System;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 *	The Mail extension provides a basic wrapper for the PHP function mail(), including optional human verification using a honeypot. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2017-2018 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Mail {
	
	
	/**
	 *  Every extension has one main method which will be called when parsing a template file.
	 *	The name of that method is the same name as the name of the class and subnamespace (case insensitive).
	 *	The .php file of the class gets simply the same name as the containing folder:
	 *	/packages/standard/mail/mail.php
	 *	
	 *	In this case the naming pattern looks like:
	 *	- namespace: 	\Standard
	 *	- directory:	/packages/standard/mail
	 *	- class file:	/packages/standard/mail/mail.php
	 *	- class: 		Mail
	 *	- method:		Mail 
	 *	
	 *	This main method must always have two parameters, which will be passed automatically when calling the extension: $obj->Mail($options, $Automad)
	 *	- $options:	An array with all the options
	 *	- $Automad:	The Automad object, to make all the Site's data available for the extension
	 *
	 *	Note: The Mail method is not a kind of constructor (like it would be in PHP 4). Since this is a namespaced class,
	 *	a method with the same name as the last part of the namespace isn't called when creating an instance of the class (PHP 5.3+).
	 *	
	 *  @param array $options
	 *  @param object $Automad
	 *  @return string Success or error message
	 */
	
	public function mail($options, $Automad) {
		
		$defaults = array(
			'to' => false,
			'error' => 'Please fill out all fields!',
			'success' => 'Successfully sent email!'
		);
		
		// Merge defaults with options.
		$options = array_merge($defaults, $options);
		
		return System\Mail::send((object) $options, $Automad);
	
	}
	
		
}
