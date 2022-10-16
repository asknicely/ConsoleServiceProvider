<?php

namespace Knp\Tests\Provider;

use Knp\Provider\ConsoleServiceProvider;
use Knp\Provider\WebServerServiceProvider;
use Silex\Application;
use Symfony\Bundle\WebServerBundle\Command\ServerRunCommand;
use PHPUnit\Framework\TestCase;

class WebServerServiceProviderTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!class_exists(ServerRunCommand::class)) {
            self::markTestSkipped('The web-server-bundle component is not installed');
        }
    }

    public function testCommandsAreRegistered()
    {
        $app = new Application();
        $app->register(new ConsoleServiceProvider());
        $app->register(new WebServerServiceProvider(), [
            'web_server.document_root' => __DIR__,
        ]);
        /** @var \Knp\Console\Application $console */
        $console = $app['console'];

        $this->assertTrue($console->has('server:run'));
        $this->assertTrue($console->has('server:start'));
        $this->assertTrue($console->has('server:stop'));
        $this->assertTrue($console->has('server:status'));
        $this->assertTrue($console->has('server:log'));
    }

    public function testRegistrationFailsIfNoConsoleProvider()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You must register the ConsoleServiceProvider to use the WebServerServiceProvider.');

        $app = new Application();
        $app->register(new WebServerServiceProvider(), [
            'web_server.document_root' => __DIR__,
        ]);
    }

    public function testCannotLoadRunCommandIfNoDocumentRootSet()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You must set the web_server.document_root parameter to use the development web server.');

        $app = new Application();
        $app->register(new ConsoleServiceProvider());
        $app->register(new WebServerServiceProvider());

        echo $app['web_server.command.server_run'];
    }

    public function testCannotLoadStartCommandIfNoDocumentRootSet()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You must set the web_server.document_root parameter to use the development web server.');

        $app = new Application();
        $app->register(new ConsoleServiceProvider());
        $app->register(new WebServerServiceProvider());

        echo $app['web_server.command.server_start'];
    }
}
