<?php 
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
 *	Copyright (c) 2016-2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


if (User::get()) {

	// Get form handler to be submitted. If no matching handler exists, set an empty string.
	$context = Core\Parse::queryKey('context');
	$handlers = array('edit_page' => 'page_data', 'edit_shared' => 'shared_data');

	if (isset($handlers[$context])) {
		$submit = $handlers[$context];
	} else {
		$submit = '';
	}
		
?>
	
	<nav class="am-navbar">
		<ul class="am-navbar-nav">
			<!-- Logo -->
			<li class="am-navbar-logo">
				<a href="<?php echo AM_BASE_URL . AM_PAGE_GUI; ?>"><i class="uk-icon-automad"></i></a>
			</li>
			<!-- Search -->
			<li class="am-navbar-search">
				<form class="uk-form uk-width-1-1" action="" method="get">
					<input type="hidden" name="context" value="search">	
					<div class="uk-autocomplete uk-width-1-1" data-uk-autocomplete="{source: Automad.autocomplete.data, minLength: 2}">
						<div class="uk-form-icon uk-width-1-1">
							<i class="uk-icon-search"></i>
							<input class="uk-form-controls uk-form-large uk-width-1-1" name="query" type="text" placeholder="<?php echo Text::get('search_placeholder') . ' ' . htmlspecialchars($this->sitename); ?>" required>
						</div>
					</div>
				</form>
			</li>
			<!-- Add Page -->
			<li class="uk-hidden-small">
				<a href="#am-add-page-modal" class="uk-button uk-button-primary" data-uk-modal>
					<i class="uk-icon-plus"></i>&nbsp;&nbsp;<?php Text::e('btn_add_page'); ?>
				</a>
			</li>
			<!-- Save -->
			<?php if ($submit) { ?>
			<li>
				<button class="uk-button uk-button-success" data-am-submit="<?php echo $submit; ?>" disabled>
					<span class="uk-hidden-small"><i class="uk-icon-check"></i>&nbsp;&nbsp;</span><?php Text::e('btn_save'); ?>
				</button>
			</li>
			<?php } ?>
			<!-- Search modal for small screens -->
			<li class="uk-visible-small">
				<a href="#am-search-modal" class="am-navbar-icon" data-uk-modal>
					<i class="uk-icon-search"></i>
				</a>
			</li>
			<!-- Sidebar -->
			<li class="uk-hidden-large">
				<a href="#" class="am-navbar-icon" data-am-toggle-sidebar="#am-sidebar">
					<i class="uk-icon-navicon uk-icon-justify"></i>
				</a>
			</li>
		</ul>
	</nav>
	
	<!-- Search modal for small screens -->
	<div id="am-search-modal" class="uk-modal">
		<div class="uk-modal-dialog uk-modal-dialog-lightbox">
			<form class="uk-form" action="" method="get">
				<input type="hidden" name="context" value="search">	
				<div class="uk-autocomplete uk-width-1-1" data-uk-autocomplete="{source: Automad.autocomplete.data, minLength: 2}">
					<div class="uk-form-icon uk-width-1-1">
						<i class="uk-icon-search"></i>
						<input class="uk-form-controls uk-form-large uk-width-1-1" name="query" type="text" required>
					</div>	
				</div>
			</form>
		</div>
	</div>
	
<?php 

} 

?>