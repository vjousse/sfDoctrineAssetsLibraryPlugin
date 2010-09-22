<?php
/**
 * @author MaGénération
 */

include(dirname(__FILE__).'/../../../../test/bootstrap/unit.php');

define('SF_APP',         'backend');
define('SF_ENVIRONMENT', 'dev');
define('SF_DEBUG',       1);
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();
$con = Propel::getConnection();

// run the test
$t = new lime_test(5, new lime_output_color());
$t->diag('assets tools test');

list ($base, $name) = sfAssetsLibraryTools::splitPath('simple');
$t->is($name, 'simple', 'splitPath() gives same name on simple strings');
$t->is($base, '',  'splitPath() gives empty base on simple strings');

list ($base, $name) = sfAssetsLibraryTools::splitPath('root/file');
$t->is($name ,'file', 'splitPath() splits by / gives good name');
$t->is($base ,'root', 'splitPath() splits by / gives good simple base');

list ($base, $name) = sfAssetsLibraryTools::splitPath('/Articles/People/Sarkozy');
$t->is($base ,'/Articles/People', 'splitPath() splits by / gives good composed base');