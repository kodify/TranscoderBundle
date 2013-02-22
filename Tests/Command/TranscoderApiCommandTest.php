<?php

namespace Kodify\TranscoderBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Kodify\TranscoderBundle\Command\TranscoderApiCommand;


/**
 * @group command
 */
class TranscoderApiCommandTest extends WebTestCase
{
    protected $command = null;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new App($kernel);
        $application->add(new TranscoderApiCommand());

        $this->command = $application->find('kodify:transcoder:api');
    }

    public function tearDown()
    {
        \Mockery::close();
        parent::tearDown();
    }


    public function testExecuteWithoutParams()
    {
        $loggerMock = \Mockery::mock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $transcoderMock= \Mockery::mock('Kodify\TranscoderBundle\Service\Driver\DriverInterface');

        $loggerMock->shouldReceive('info')->with('msg="manual trasncoder api call" command="test" param=""')->times(1);
        $transcoderMock->shouldReceive('test')->with('')->times(1);

        $mockContainer = \Mockery::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $mockContainer->shouldReceive('get')->with('kodify_transcoder')->andReturn($transcoderMock);
        $mockContainer->shouldReceive('get')->with('logger')->andReturn($loggerMock);

        $this->command->setContainer($mockContainer);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array('command' => $this->command->getName(), 'operation' => 'test'));
    }

    public function testExecuteWithParams()
    {
        $loggerMock = \Mockery::mock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $transcoderMock= \Mockery::mock('Kodify\TranscoderBundle\Service\Driver\DriverInterface');

        $loggerMock->shouldReceive('info')->with('msg="manual trasncoder api call" command="test" param="param1"')->times(1);
        $transcoderMock->shouldReceive('test')->with('param1')->times(1);

        $mockContainer = \Mockery::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $mockContainer->shouldReceive('get')->with('kodify_transcoder')->andReturn($transcoderMock);
        $mockContainer->shouldReceive('get')->with('logger')->andReturn($loggerMock);

        $this->command->setContainer($mockContainer);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array('command' => $this->command->getName(), 'operation' => 'test', 'parameter' => 'param1'));
    }

}