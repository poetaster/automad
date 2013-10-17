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
 *	AUTOMAD CMS
 *
 *	(c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 */


/**
 * 	The Tool class holds all methods to be used within the template files.
 */


class Tool {
	

	/**
	 * 	Site object.
	 */
	
	private $S;
	
	
	/**
	 * 	Current Page object.
	 */
	
	private $P;
	
	
	/**
	 * 	The modus defines the way a selection of pages gets sortet.
	 *	
	 *	Default is empty, to make sure the original order is kept when leaving out $this->sortBy().
	 */
	
	private $sortMode = '';
	
	
	/**
	 * 	Sort order for selections.
	 *
	 *	Default is SORT_ASC, to make sure the original order is kept when leaving out $this->sortAscending().
	 */
	
	private $sortOrder = SORT_ASC;
	
	
	/**
	 * 	The Site object is passed as an argument. It shouldn't be created again (performance).
	 */
		
	public function __construct($site) {
		
		$this->S = $site;
		$this->P = $this->S->getCurrentPage();
		
	}
	
	
	/**
	 * 	Return the HTML for a list of pages below the current page.
	 *	The variables to be included in the output are set in a comma separated parameter string ($varStr).
	 *
	 *	@param string $varStr
	 *	@return the HTML of the list
	 */
	
	public function listChildren($varStr) {
		
		$selection = new Selection($this->S->getCollection());
		$selection->filterByParentUrl($this->P->relUrl);
		$selection->sortPages($this->sortMode, $this->sortOrder);
		$pages = $selection->getSelection();
		
		return Html::generateList($pages, $varStr);
		
	}
	
	
	/**
	 * 	Return the HTML for a list of all pages excluding the current page.
	 *	The variables to be included in the output are set in a comma separated parameter string ($varStr).
	 *
	 *	@param string $varStr
	 *	@return the HTML of the list
	 */
	
	public function listAll($varStr) {
	
		$selection = new Selection($this->S->getCollection());	
		$selection->sortPages($this->sortMode, $this->sortOrder);
		$pages = $selection->getSelection();
		
		// Remove current page from selecion
		unset($pages[$this->P->relUrl]);
	
		return Html::generateList($pages, $varStr);	
		
	}
	
	
	/**
	 *	Generate a list for the navigation below a given URL.
	 *
	 *	@param string $parentRelUrl
	 *	@return html of the generated list	
	 */
	
	public function navBelow($parentUrl) {
				
		$selection = new Selection($this->S->getCollection());
		$selection->filterByParentUrl($parentUrl);
		$selection->sortPages($this->sortMode, $this->sortOrder);
		$pages = $selection->getSelection();
		
		return Html::generateNav($pages);
		
	}
	

	/**
	 * 	Generate breadcrumbs to current page.
	 *
	 *	@return html of breadcrumb navigation
	 */
	
	public function navBreadcrumbs() {
		
		$pages = array();
		$urlSegments = explode('/', $this->P->relUrl);
		$tempUrl = '';
		
		foreach ($urlSegments as $urlSegment) {
			
			$tempUrl = '/' . trim($tempUrl . '/' . $urlSegment, '/');
			$pages[] = $this->S->getPageByUrl($tempUrl); 
			
		}
		
		return Html::generateBreadcrumbs($pages);
		
	}
	
		
	/**
	 *	Generate a list for the navigation below the current page.
	 *
	 *	@return html of the generated list	
	 */
	
	public function navChildren() {
	
		return $this->navBelow($this->P->relUrl);
		
	}
	
	
	/**
	 *	Generate a list for the navigation below the current page's parent.
	 *
	 *	@return html of the generated list	
	 */
	
	public function navSiblings() {
		
		return $this->navBelow($this->P->parentRelUrl);
		
	}
	
	
	/**
	 *	Generate a list for the navigation at the top level including the home page (level 0 & 1).
	 *
	 *	@return html of the generated list	
	 */
	
	public function navTop() {
	
		$selection = new Selection($this->S->getCollection());
		$selection->filterByParentUrl('/');
		$selection->sortPages($this->sortMode, $this->sortOrder);
		$pages = $selection->getSelection();
		
		// Add Home Page as well
		$pages = array('/' => $this->S->getPageByUrl('/')) + $pages;
		
		return Html::generateNav($pages);
		
	}

	
	/**
	 * 	Generate a list of pages having at least one tag in common with the current page.
	 *
	 *	@param string $varString
	 *	@return html of the generated list
	 */
	
	public function relatedPages($varStr) {
		
		$pages = array();
		$tags = $this->P->tags;
		
		// Get pages
		foreach ($tags as $tag) {
			
			$selection = new Selection($this->S->getCollection());
			$selection->filterByTag($tag);			
			$pages = array_merge($pages, $selection->getSelection());
						
		}
		
		// Remove current page from selecion
		unset($pages[$this->P->relUrl]);
		
		// Sort pages
		$selection = new Selection($pages);
		$selection->sortPages($this->sortMode, $this->sortOrder);
		$pages = $selection->getSelection();
		
		return Html::generateList($pages, $varStr);
				
	}
	
		
	/**
	 * 	Place a search field with placeholder text.
	 *
	 *	@param string $varStr (placeholder text)
	 *	@return the HTML of the searchfield
	 */
	
	public function searchField($varStr) {
		
		$url = BASE_URL . $this->S->getSiteData('resultsPageUrl');
		
		return Html::generateSearchField($url, $varStr);
		
	}

	
	/**
	 * 	Generate a list of search results.
	 *
	 *	@param string $varString
	 *	@return html of the generated list
	 */
	
	public function searchResults($varStr) {
		
		if (isset($_GET["search"])) {
			
			$search = $_GET["search"];
			
			$selection = new Selection($this->S->getCollection());
			$selection->filterByKeywords($search);
			$selection->sortPages($this->sortMode, $this->sortOrder);
			
			$pages = $selection->getSelection();
			
			return Html::generateList($pages, $varStr);
			
		}
		
	}
	
	
	/**
	 * 	Resets the sort mode to the original file system order.
	 *	
	 *	If Selection::sortPages() gets passed an empty variable as mode, it will fall back to Selection::sortByPath().
	 */
	
	public function sortOriginalOrder() {
		
		$this->sortMode = NULL;
		
	}
	
	
	/**
	 * 	Sets the $key in Page::data[$key] as the sort mode for all following lists and navigations.
	 *
	 *	@param string $var (any variable set in the text file of the page)
	 */
	
	public function sortBy($var) {
		
		$this->sortMode = $var;
		
	}
	
	
	/**
	 * 	Sets the sort order to ascending for all following lists and navigations.
	 */
	
	public function sortAscending() {
		
		$this->sortOrder = SORT_ASC;
		
	}
	
	
	/**
	 * 	Sets the sort order to descending for all following lists and navigations.
	 */
	
	public function sortDescending() {
		
		$this->sortOrder = SORT_DESC;
		
	}
	

	/**
	 * 	Return the site name
	 *
	 *	@return site name
	 */
	
	public function siteName() {
		
		return $this->S->getSiteName();
		
	}
	
	
	/**
	 * 	Return the URL of the page theme
	 *
	 *	@return page theme URL
	 */
	
	public function themeURL() {
		
		return str_replace(BASE_DIR, BASE_URL, $this->S->getThemePath());
		
	}
	
	
}


?>
