<?
	class Loader {

		public function library($lib, $pkgHandle = null) {
			if ($pkgHandle) {
				require_once(DIR_LIBRARIES . '/' . $pkgHandle . '/' . DIRNAME_LIBRARIES . '/' . $lib . '.php');
			} else if (file_exists(DIR_LIBRARIES . '/' . $lib . '.php')) {
				require_once(DIR_LIBRARIES . '/' . $lib . '.php');
			} else {
				require_once(DIR_LIBRARIES_CORE . '/' . $lib . '.php');
			}
		}
		
		public function model($mod, $pkgHandle = null) {
			if ($pkgHandle) {
				require_once(DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . $mod . '.php');
			} else if (file_exists(DIR_MODELS . '/' . $mod . '.php')) {
				require_once(DIR_MODELS . '/' . $mod . '.php');
			} else {
				require_once(DIR_MODELS_CORE . '/' . $mod . '.php');
			}
		}
		
		public function packageElement($file, $pkgHandle, $args = null) {
			if (is_array($args)) {
				extract($args);
			}
			include(DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_ELEMENTS . '/' . $file . '.php');
		}

		public function element($file, $args = null) {
			if (is_array($args)) {
				extract($args);
			}
			if (file_exists(DIR_FILES_ELEMENTS_CORE . '/' . $file . '.php')) {
				include(DIR_FILES_ELEMENTS_CORE . '/' . $file . '.php');
			} else {
				include(DIR_FILES_ELEMENTS . '/' . $file . '.php');
			}
		}
		
		public function block($bl) {
			if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER)) {
				require_once(DIR_FILES_BLOCK_TYPES . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER);
			} else {
				require_once(DIR_FILES_BLOCK_TYPES_CORE . '/' . $bl . '/' . FILENAME_BLOCK_CONTROLLER);
			}
		}
		
		/* database loads the libraries needed to connect. DB Instantiates the object */
		public function database() {
			Loader::library('3rdparty/adodb/adodb.inc');
			//Loader::library('3rdparty/adodb/adodb-pear.inc');
			Loader::library('3rdparty/adodb/adodb-exceptions.inc');
			Loader::library('3rdparty/adodb/adodb-active-record.inc');
			Loader::library('3rdparty/adodb/adodb-xmlschema03.inc');
			Loader::library('database');
		}
		
		public function db($server = null, $username = null, $password = null, $database = null) {
			static $_db;
			if (!isset($_db)) {
				if ($server == null && defined('DB_SERVER')) {	
					$dsn = DB_TYPE . '://' . DB_USERNAME . ':' . DB_PASSWORD . '@' . DB_SERVER . '/' . DB_DATABASE;
				} else if ($server) {
					$dsn = DB_TYPE . '://' . $username . ':' . $password . '@' . $server . '/' . $database;
				}

				if ($dsn) {
					$_dba = @NewADOConnection($dsn);
					if (is_object($_dba)) {
						ADOdb_Active_Record::SetDatabaseAdapter($_dba);
						$_db = new ConcreteDB();
						$_db->setDatabaseObject($_dba);
					}
				} else {
					return false;
				}
			}
			
			return $_db;
		}
		
		public function helper($file) {
			// loads and instantiates the object
			if (file_exists(DIR_HELPERS . '/' . $file . '.php')) {
				require_once(DIR_HELPERS . '/' . $file . '.php');
			} else {
				require_once(DIR_HELPERS_CORE . '/' . $file . '.php');
			}
			
			$class = Object::camelcase($file) . "Helper";
			$cl = new $class;
			return $cl;
		}
		
		public function package($file) {
			// loads and instantiates the object
			if (file_exists(DIR_PACKAGES . '/' . $file . '/' . FILENAME_PACKAGE_CONTROLLER)) {
				require_once(DIR_PACKAGES . '/' . $file . '/' . FILENAME_PACKAGE_CONTROLLER);
				$class = Object::camelcase($file) . "Package";
				$cl = new $class;
				return $cl;
			}
		}

		public function dashboardModuleController($dbhHandle, $pkg = null) {
			$class = Object::camelcase($dbhHandle . 'DashboardModuleController');
			if (!class_exists($class)) {
				$file1 = DIR_FILES_CONTROLLERS . '/' . DIRNAME_DASHBOARD . '/' . DIRNAME_DASHBOARD_MODULES . '/' . $dbhHandle . '.php';
				if (is_object($pkg)) {
					$pkgHandle = $pkg->getPackageHandle();
					$file2 = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_DASHBOARD . '/' . DIRNAME_DASHBOARD_MODULES . '/' . $dbhHandle . '.php';
				}
				$file3 = DIR_FILES_CONTROLLERS_REQUIRED . '/' . DIRNAME_DASHBOARD . '/' . DIRNAME_DASHBOARD_MODULES . '/' . $dbhHandle . '.php';
				if (file_exists($file1)) {
					include($file1);
				} else if (isset($file2) && file_exists($file2)) {
					include($file2);
				} else {
					include($file3);
				}
			}

			$controller = new $class();
			return $controller;
		}
		
		public function dashboardModule($dbhHandle, $pkg = null) {
			$controller = Loader::dashboardModuleController($dbhHandle, $pkg);
			extract($controller->getSets());
			extract($controller->getHelperObjects());
			$this->controller = $controller;

			// now the view
			$file1 = DIR_FILES_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/' . DIRNAME_DASHBOARD_MODULES . '/' . $dbhHandle . '.php';
			if (is_object($pkg)) {
				$pkgHandle = $pkg->getPackageHandle();
				$file2 = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/' . DIRNAME_DASHBOARD_MODULES . '/' . $dbhHandle . '.php';
			}
			$file3 = DIR_FILES_ELEMENTS_CORE . '/' . DIRNAME_DASHBOARD . '/' . DIRNAME_DASHBOARD_MODULES . '/' . $dbhHandle . '.php';
			if (file_exists($file1)) {
				include($file1);
			} else if (isset($file2) && file_exists($file2)) {
				include($file2);
			} else {
				include($file3);
			}
		}
		
		public function controller($item) {
			if ($item instanceof Page) {
				$c = $item;
				if ($c->getCollectionTypeID() > 0) {					
					$ctHandle = $c->getCollectionTypeHandle();
					
					if (file_exists(DIR_FILES_CONTROLLERS . "/" . DIRNAME_PAGE_TYPES . "/{$ctHandle}.php")) {
						require_once(DIR_FILES_CONTROLLERS . "/" . DIRNAME_PAGE_TYPES . "/{$ctHandle}.php");
						$include = true;
					} else if ($item->getPackageID() > 0 && (file_exists(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php'))) {
						require_once(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/' . $ctHandle . '.php');
						$include = true;
					} else if (file_exists(DIR_FILES_CONTROLLERS_REQUIRED . "/" . DIRNAME_PAGE_TYPES . "/{$ctHandle}.php")) {
						require_once(DIR_FILES_CONTROLLERS_REQUIRED . "/" . DIRNAME_PAGE_TYPES . "/{$ctHandle}.php");
						$include = true;
					}
					
					if ($include) {
						$class = Object::camelcase($ctHandle) . 'PageTypeController';
					}
				} else if ($c->isGeneratedCollection()) {
					$file = $c->getCollectionFilename();
					if ($file != '') {
						// strip off PHP suffix for the $path variable, which needs it gone
						if (strpos($file, FILENAME_COLLECTION_VIEW) !== false) {
							$path = substr($file, 0, strpos($file, '/'. FILENAME_COLLECTION_VIEW));
						} else {
							$path = substr($file, 0, strpos($file, '.php'));
						}
					}
				}
			} else if ($item instanceof Block || $item instanceof BlockType) {
				if ($item->getPackageID() > 0) {
					require_once(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $item->getBlockTypeHandle() . '/' . FILENAME_BLOCK_CONTROLLER);
				} else if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $item->getBlockTypeHandle() . '/' . FILENAME_BLOCK_CONTROLLER)) {
					require_once(DIR_FILES_BLOCK_TYPES . "/" . $item->getBlockTypeHandle() . "/" . FILENAME_BLOCK_CONTROLLER);
				} else {
					require_once(DIR_FILES_BLOCK_TYPES_CORE . "/" . $item->getBlockTypeHandle() . "/" . FILENAME_BLOCK_CONTROLLER);
				}
				$class = Object::camelcase($item->getBlockTypeHandle()) . 'BlockController';
				if ($item instanceof BlockType) {
					$controller = new $class($item);
				}
			} else {
				$path = $item;
			}
			
			$controllerFile = $path . '.php';

			if ($path != '') {
				if (file_exists(DIR_FILES_CONTROLLERS . $controllerFile)) {
					include(DIR_FILES_CONTROLLERS . $controllerFile);
					$include = true;
				} else if (file_exists(DIR_FILES_CONTROLLERS . $path . '/' . FILENAME_COLLECTION_CONTROLLER)) {
					include(DIR_FILES_CONTROLLERS . $path . '/' . FILENAME_COLLECTION_CONTROLLER);
					$include = true;
				} else if (file_exists(DIR_FILES_CONTROLLERS_REQUIRED . $controllerFile)) {
					include(DIR_FILES_CONTROLLERS_REQUIRED . $controllerFile);
					$include = true;
				} else if (file_exists(DIR_FILES_CONTROLLERS_REQUIRED . $path . '/' . FILENAME_COLLECTION_CONTROLLER)) {
					include(DIR_FILES_CONTROLLERS_REQUIRED . $path . '/' . FILENAME_COLLECTION_CONTROLLER);
					$include = true;

				} else if (is_object($item)) {
					if ($item->getPackageID() > 0 && (file_exists(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $controllerFile))) {
						include(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $controllerFile);
						$include = true;
					} else if ($item->getPackageID() > 0 && (file_exists(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $path . '/'. FILENAME_COLLECTION_CONTROLLER))) {
						include(DIR_PACKAGES . '/' . $item->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $path . '/'. FILENAME_COLLECTION_CONTROLLER);
						$include = true;
					}
				}
				
				if ($include) {
					$class = Object::camelcase($path) . 'Controller';
				}
			}
			
			if (!isset($controller)) {
				if ($class && class_exists($class)) {
					// now we get just the filename for this guy, so we can extrapolate
					// what our controller is named
					$controller = new $class($item);
				} else {
					$controller = new Controller($item);
				}
			}
			
			if (is_object($c)) {
				$controller->setCollectionObject($c);
			}
			
			$controller->setupRestrictedMethods();
			return $controller;
		}
		
	}