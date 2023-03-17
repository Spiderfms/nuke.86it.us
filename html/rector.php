<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\Php70\Rector\FuncCall\EregToPregMatchRector;
//use Rector\MysqlToMysqli\Rector\Assign\MysqlAssignToMysqliRector;
//use Rector\MysqlToMysqli\Rector\FuncCall\MysqlFuncCallToMysqliRector;
//use Rector\MysqlToMysqli\Rector\FuncCall\MysqlPConnectToMysqliConnectRector;
//use Rector\MysqlToMysqli\Rector\FuncCall\MysqlQueryMysqlErrorWithLinkRector;

return static function (RectorConfig $rectorConfig): void {
    // A. run whole set
    $rectorConfig->sets([
		SetList::PHP_82,
    ]);

    // B. or single rule
    //$rectorConfig->rule(TypedPropertyFromAssignsRector::class);
    $rectorConfig->rule(EregToPregMatchRector::class);
	//$rectorConfig->rule(MysqlAssignToMysqliRector::class);
	//$rectorConfig->rule(MysqlFuncCallToMysqliRector::class);
	//$rectorConfig->rule(MysqlPConnectToMysqliConnectRector::class);
	//$rectorConfig->rule(MysqlQueryMysqlErrorWithLinkRector::class);
		
    $rectorConfig->paths([
        //__DIR__ . '/admin',
        //__DIR__ . '/blocks',
        //__DIR__ . '/install/includes/database.php',
		//__DIR__ . '/install/install2.php',
		//__DIR__ . '/install',
        //__DIR__ . '/includes',
		//__DIR__ . '/includes/classes/class.debugger.php',
		//__DIR__ . '/includes/counter.php',
		//__DIR__ . '/includes/ipban.php',
        //__DIR__ . '/install',
        //__DIR__ . '/language',
        //__DIR__ . '/modules',
		//__DIR__ . '/modules/News/index.php',
		  __DIR__ . '/modules/Surveys/index.php',
        //__DIR__ . '/themes',
		//__DIR__ . '/admin.php',
		//__DIR__ . '/backend.php',
		//__DIR__ . '/footer.php',
		//__DIR__ . '/header.php',
		//__DIR__ . '/index.php',
		//__DIR__ . '/mainfile.php',
		//__DIR__ . '/modules.php',
    ]);

};
