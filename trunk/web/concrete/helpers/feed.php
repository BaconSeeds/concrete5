<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * A simple wrapper for the 3rd party SimplePie library. Used to parse RSS and ATOM feeds.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

class FeedHelper {

	public function __construct() {
		Loader::library("3rdparty/simplepie");
	}
	
	/**
	 * Loads a newsfeed object.
	 * @param string $feed
	 * @return SimplePie $feed
	 */
	public function load($feed) {
		$feed = new SimplePie($feed, DIR_FILES_CACHE);
		return $feed;
	}
	
	
}