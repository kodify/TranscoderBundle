<?php

namespace Kodify\TranscoderBundle\Tests;

use \Mockery as M;

abstract class TestBaseClass extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        M::close();
        parent::tearDown();
    }


    /**
     * Call protected method $methodName
     *
     * @param String $className  ClassName should contain namespace info.
     * @param String $methodName Method that will be called
     * @param array  $params     Params to be sent to the method
     * @param mixed  $object     Object we will use in the method
     *
     * @return mixed
     */
    protected function callProtected($className, $methodName, $params, $object)
    {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $params);
    }
}
