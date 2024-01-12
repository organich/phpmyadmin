<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Normalization;

use PhpMyAdmin\Config;
use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\Controllers\Normalization\CreateNewColumnController;
use PhpMyAdmin\Current;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Normalization;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer;
use PhpMyAdmin\Transformations;
use PhpMyAdmin\UserPrivileges;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateNewColumnController::class)]
class CreateNewColumnControllerTest extends AbstractTestCase
{
    public function testDefault(): void
    {
        Config::getInstance()->selectedServer['DisableIS'] = false;
        UserPrivileges::$column = false;
        Current::$database = 'test_db';
        Current::$table = 'test_table';

        $dbiDummy = $this->createDbiDummy();

        $dbi = $this->createDatabaseInterface($dbiDummy);
        DatabaseInterface::$instance = $dbi;
        $response = new ResponseRenderer();
        $template = new Template();
        $request = self::createStub(ServerRequest::class);
        $request->method('getParsedBodyParam')->willReturnMap([['numFields', null, '1']]);

        $controller = new CreateNewColumnController(
            $response,
            $template,
            new Normalization($dbi, new Relation($dbi), new Transformations(), $template),
        );
        $controller($request);

        self::assertStringContainsString('<table id="table_columns"', $response->getHTMLResult());
    }
}
