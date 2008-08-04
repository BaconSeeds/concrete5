<?
/**
 *
 * When this file is run it basically queries the database for site config items and sets those up, possibly overriding items in the base.php.
 * The hierarchy basically goes like this:
 * 1. Item defined in config/site.php? Then it will be used.
 * 2. Item saved in database? Then it will be used.
 * 3. Otherwise, we setup the defaults below.
 **/
 
# permissions model - valid options are 'advanced' or 'simple'
if (!defined('PERMISSIONS_MODEL')) {
	Config::getOrDefine('PERMISSIONS_MODEL', 'simple');
}

if (!defined('SITE')) {
	Config::getOrDefine('SITE', 'Concrete5');
}

# Default URL rewriting setting
if (!defined('URL_REWRITING')) {
	Config::getOrDefine('URL_REWRITING', false);
}

if (URL_REWRITING == true) {
	define('URL_SITEMAP', BASE_URL . DIR_REL . '/dashboard/sitemap');
	define('REL_DIR_FILES_TOOLS', DIR_REL . '/tools');
	define('REL_DIR_FILES_TOOLS_REQUIRED', DIR_REL . '/tools/required'); // front-end
	define('REL_DIR_FILES_TOOLS_BLOCKS', REL_DIR_FILES_TOOLS . '/blocks'); // this maps to the /tools/ directory in the blocks subdir
	define('REL_DIR_FILES_TOOLS_BLOCKS_REQUIRED', REL_DIR_FILES_TOOLS_REQUIRED . '/blocks'); // this maps to the /tools/ directory in the blocks subdir
} else {
	define('URL_SITEMAP', BASE_URL . DIR_REL . '/index.php/dashboard/sitemap');
	define('REL_DIR_FILES_TOOLS', DIR_REL . '/index.php/tools');
	define('REL_DIR_FILES_TOOLS_REQUIRED', DIR_REL . '/index.php/tools/required'); // front-end
}

define('REL_DIR_FILES_TOOLS_BLOCKS', REL_DIR_FILES_TOOLS . '/blocks'); // this maps to the /tools/ directory in the blocks subdir
define('REL_DIR_FILES_TOOLS_BLOCKS_REQUIRED', REL_DIR_FILES_TOOLS_REQUIRED . '/blocks'); // this maps to the /tools/ directory in the blocks subdir
