<?php

namespace Kodify\TranscoderBundle\Tests\Service;

use Kodify\TranscoderBundle\Service\Transcoder;
use Kodify\TranscoderBundle\Tests\TestBaseClass;

/**
 * @group transcoder
 */
class TranscoderTest extends TestBaseClass
{
    protected $object;

    const DRIVER_INTERFACE = 'Kodify\TranscoderBundle\Service\Driver\DriverInterface';

    public function testAllInterfaceMethodsCanBeCalled()
    {
        $callableMethods = $this->initializeObject();
        foreach ($callableMethods as $method) {
            if ('createFormat' == $method->name) {
                continue;
            }
            $requiredParametersCount = $method->getNumberOfRequiredParameters();
            $parameters = $method->getParameters();
            $parametersCount = count($parameters);
            for ($i = $requiredParametersCount; $i <= $parametersCount; ++$i) {
                $args = array();
                for ($j = 0; $j < $i; ++$j) {
                    $args[] = $parameters[$j]->name;
                }
                $this->assertSame($method->name, call_user_func_array(array($this->object, $method->name), $args));
                for ($j = $i; $j < $parametersCount; ++$j) {
                    $args[] = $parameters[$j]->name;
                    $this->assertSame($method->name, call_user_func_array(array($this->object, $method->name), $args));
                }
            }
        }
    }

    public function testIncorrectMethodCall()
    {
        $this->initializeObject();
        $this->assertFalse($this->object->inventedMethodName());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testNonValidConfigValues()
    {
        $this->object = new Transcoder('inventedDriver', array());
    }


    /**
     * Initializes the object with a Driver that instantiates the correct Interface
     * @return \ReflectionMethod[]
     */
    public function initializeObject()
    {
        $reflection = new \ReflectionClass(static::DRIVER_INTERFACE);

        $methodsList = $reflection->getMethods();

        $this->object = new Transcoder('PandaStream', array(
            'cloudId' => 'aa',
            'accessKey' => 'bb',
            'secretKey' => 'cc',
            'apiUrl' => 'dd',

        ));
        $transcoderDriverMock = \Mockery::mock('Kodify\TranscoderBundle\Service\Driver\DriverInterface');
        foreach ($methodsList as $method) {
            $transcoderDriverMock->shouldReceive($method->name)->andReturn($method->name);
        }
        $this->object->setDriver($transcoderDriverMock);

        return $methodsList;
    }

}