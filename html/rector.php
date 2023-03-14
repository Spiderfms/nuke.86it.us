<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\Php70\Rector\FuncCall\EregToPregMatchRector;

return static function (RectorConfig $rectorConfig): void {
    // A. run whole set
    $rectorConfig->sets([
        SetList::DEAD_CODE,
		SetList::PHP_82,
    ]);

    // B. or single rule
    //$rectorConfig->rule(TypedPropertyFromAssignsRector::class);
    $rectorConfig->rule(EregToPregMatchRector::class);
		
    $rectorConfig->paths([
        //__DIR__ . '/admin',
        //__DIR__ . '/blocks',
        //__DIR__ . '/db',
        //__DIR__ . '/includes',
        //__DIR__ . '/install',
        //__DIR__ . '/language',
        //__DIR__ . '/modules',
        //__DIR__ . '/themes',
		//__DIR__ . '/admin.php',
		//__DIR__ . '/backend.php',
		//__DIR__ . '/footer.php',
		//__DIR__ . '/header.php',
		//__DIR__ . '/index.php',
		__DIR__ . '/mainfile.php',
		//__DIR__ . '/modules.php',
    ]);

};
