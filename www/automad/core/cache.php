<?php defined('AUTOMAD') or die('Direct access not permitted!');
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
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


/**
 *	The Cache class holds all methods for evaluating, reading and writing the HTML output from/to CACHE_DIR.
 *
 *	First a virtual file name of a possibly existing cached version of the visited page gets determined from the PATH_INFO and the QUERY_STRING.
 *	In a second step, the existance of that file gets verified.
 *	Third, the mtime of the site (last modified page mtime) gets determined and compared with the cache file's mtime. (That process gets limited by a certain delay)
 *	If the cache file mtime is smaller than the latest mtime (site), false gets returned.
 *
 *	To determine the latest changed page from all the existing pages, all directories under /pages and all *.txt files get collected in an array.
 *	The mtime for each item in that array gets stored in a new array ($mTimes[$item]). After sorting, all keys are stored in $mTimesKeys.
 *	The last modified item is then = end($mTimesKeys), and its mtime is $mTimes[$lastItem].
 *	Compared to using max() on the $mTime array, this method is a bit more complicated, but also determines, which of the items was last edited and not only its mtime.
 *	(That gives a bit more control for debugging)
 */


class Cache {
	
	
	/**
	 *	The determined matching file of the cached version of the currently visited page.
	 */
	
	private $pageCacheFile;
	
	
	/**
	 *	The constructor just determines $pageCacheFile to make it available within the instance.
	 */
	
	public function __construct() {
		
		$this->pageCacheFile = $this->getPageCacheFilePath();
		
	}
	

	/**
	 *	Verify if the cached version of the visited page is existing and still up to date.
	 *
	 *	@return boolean - true, if the cached version is valid.
	 */

	public function cacheIsApproved() {
		
		if (CACHE_ENABLED) {
	
			if (file_exists($this->pageCacheFile)) {
		
				if (!file_exists(CACHE_LAST_CHECK_FILE)) {
					Debug::pr('Cache: Create ' . CACHE_LAST_CHECK_FILE);
					touch(CACHE_LAST_CHECK_FILE);
				}
		
				// The modification times get only checked every CACHE_CHECK_DELAY seconds, since
				// the process of collecting all mtimes itself takes some time too.
				if ((filemtime(CACHE_LAST_CHECK_FILE) + CACHE_CHECK_DELAY) < time()) {
	
					// Touch file to safe the time when site got last checked for mtimes.
					touch(CACHE_LAST_CHECK_FILE);
		
					$lastestMTime = $this->getLastestMTime();
			
					if (filemtime($this->pageCacheFile) < $lastestMTime) {
						Debug::pr('Cache: Cached version is deprecated!');
						return false;
					} else {
						Debug::pr('Cache: Cached version got approved!');
						return true;
					}
			
				} else {
			
					Debug::pr('Cache: Skipped searching for the latest mtime! Last check was: ' . date('d. M Y, H:i:s', filemtime(CACHE_LAST_CHECK_FILE)));
					return true;
			
				}
	
			} else {
		
				Debug::pr('Cache: Cached file does not exist!');
				return false;
		
			}
	
		} else {
			
			Debug::pr('Cache: Caching is disabled!');
			return false;
			
		}
		
	}


	/**
	 *	Determine the corresponding file in the cache for the visited page in consideration of a possible query string.
	 *	A page gets for each possible query string (to handle sort/filter) an unique cache file.
	 *
	 *	@return The determined file name of the matching cached version of the visited page.
	 */
	
	private function getPageCacheFilePath() {
	
		if (isset($_SERVER['PATH_INFO'])) {
			$currentPath = '/' . trim($_SERVER['PATH_INFO'], '/');
		} else {
			$currentPath = '';
		}
		
		if ($_SERVER['QUERY_STRING']) {
			$queryString = '_' . Parse::sanitize($_SERVER['QUERY_STRING']);
		} else {
			$queryString = '';
		}
		
		$pageCacheFile = BASE_DIR . CACHE_DIR . $currentPath . '/' . CACHE_FILE_PREFIX . $queryString . '.' . CACHE_FILE_EXTENSION;
		
		return $pageCacheFile;
		
	}
	
	
	/**
	 *	Get an array of all subdirectories and *.txt files under /pages and determine the latest mtime among all these items.
	 *	That time basically represents the site's modification time, to find out the lastes edit/removal/add of a page.
	 *
	 *	@return The latest found mtime, which equal basically the site's modification time.
	 */
	
	private function getLastestMTime() {
		
		// Get all page directories
		$dir = BASE_DIR . SITE_PAGES_DIR;	
		$pageDirs = array($dir);
	
		while ($dirs = glob($dir . '/*', GLOB_ONLYDIR)) {
			$dir .= '/*';
			$pageDirs = array_merge($pageDirs, $dirs);
		}

		// Get all page data files
		$pageFiles = array();
	
		foreach ($pageDirs as $pageDir) {
			$pageFiles = array_merge($pageFiles, glob($pageDir . '/*.' . PARSE_DATA_FILE_EXTENSION));
		}
	
		// Collect all modification times and find last modified page
		$pageDirsAndFiles = array_merge($pageDirs, $pageFiles);
		$mTimes = array();
	
		foreach ($pageDirsAndFiles as $item) {
			$mTimes[$item] = filemtime($item);
		}
	
		// Needs to be that complicated to get the key and the mtime for debugging.
		// Can't use max() for that.
		asort($mTimes);
		$mTimesKeys = array_keys($mTimes);
		$lastModifiedItem = end($mTimesKeys);
		$lastestMTime = $mTimes[$lastModifiedItem];
	
		Debug::pr('Cache: Last modified page: "' . $lastModifiedItem . '" - ' . date('d. M Y, H:i:s', $lastestMTime));
		
		return $lastestMTime;
		
	}
	
	
	/**
	 *	Read the rendered page from the cached version.
	 *
	 *	@return The full cached HTML of the page. 
	 */
	
	public function readCache() {
		
		Debug::pr('Cache: Reading: ' . $this->pageCacheFile);
		return file_get_contents($this->pageCacheFile);
		
	}
	
	
	/**
	 *	Write the rendered HTML output to the cache file.
	 */
	
	public function writeCache($output) {
		
		if (CACHE_ENABLED) {
		
			if(!file_exists(dirname($this->pageCacheFile))) {
				mkdir(dirname($this->pageCacheFile), 0700, true);
		    	}
		
			file_put_contents($this->pageCacheFile, $output);
		
			Debug::pr('Cache: Writing: ' . $this->pageCacheFile);
		
		}
		
	}
	
	
}


?>