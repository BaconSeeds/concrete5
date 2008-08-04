<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for validating users in Concrete
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
	class ConcreteValidationHelper {
	
		/** 
		 * Checks whether a passed username is unique or if a user of this name already exists
		 * @param string $uName
		 * @return bool
		 */
		function isUniqueUsername($uName) {
			$db = Loader::db();
			$q = "select uID from Users where uName = '{$uName}'";
			$r = $db->getOne($q);
			if ($r) {
				return false;
			} else {
				return true;
			}
		}


		/**
		 * Checks whether a passed email address is unique
		 * @return bool
		 * @param string $uEmail
		 */
		function isUniqueEmail($uEmail) {
			$db = Loader::db();
			$q = "select uID from Users where uEmail = '{$uEmail}'";
			$r = $db->getOne($q);
			if ($r) {
				return false;
			} else {
				return true;
			}
		}

	}