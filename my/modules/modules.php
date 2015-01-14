<?php
define('DS', DIRECTORY_SEPARATOR);
$root=str_replace("/",DS , dirname(dirname(__FILE__)));
$root=str_replace('\\',DS , $root);


define('ROOT', $root);

require_once(ROOT .DS . 'modules' . DS . 'AvbDevPlatform.php');
require_once(ROOT .DS . 'modules' . DS . 'APIFunctions.php');
require_once(ROOT .DS . 'modules' . DS . 'APIFunctionsAD.php');
require_once(ROOT .DS . 'modules' . DS . 'App.php');
require_once(ROOT .DS . 'modules' . DS . 'AppCopy.php');
require_once(ROOT .DS . 'modules' . DS . 'auth.php');
require_once(ROOT .DS . 'modules' . DS . 'Billing.php');
require_once(ROOT .DS . 'modules' . DS . 'db.php');
require_once(ROOT .DS . 'modules' . DS . 'dbop.class.php');
require_once(ROOT .DS . 'modules' . DS . 'email.php');
require_once(ROOT .DS . 'modules' . DS . 'errorLogger.php');
require_once(ROOT .DS . 'modules' . DS . 'File.php');
require_once(ROOT .DS . 'modules' . DS . 'logger.php');
require_once(ROOT .DS . 'modules' . DS . 'organization.php');
require_once(ROOT .DS . 'modules' . DS . 'program.php');
require_once(ROOT .DS . 'modules' . DS . 'Report.php');
require_once(ROOT .DS . 'modules' . DS . 'team.php');
require_once(ROOT .DS . 'modules' . DS . 'user.php');
require_once(ROOT .DS . 'modules' . DS . 'UserKindsOperations.php');
require_once(ROOT .DS . 'modules' . DS . 'UserKindsUserOperations.php');
require_once(ROOT .DS . 'modules' . DS . 'WaterCooler.php');
require_once(ROOT .DS . 'modules' . DS . 'uploader.class.php');
require_once(ROOT .DS . 'modules' . DS . 'Accounts.php');


