<?php

namespace Kodify\TranscoderBundle\Service;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Doctrine\DBAL\Driver;
use Kodify\TranscoderBundle\Service\Driver\DriverInterface;

class Transcoder
{
    protected $driver;
    const DRIVER_BASEPATH = 'Kodify\TranscoderBundle\Service\Driver';
    const INTERFACE_NAME = 'DriverInterface';
    const DEFAULT_FORMAT = 'h264';

    public function __construct($driver, $params)
    {
        $className = static::DRIVER_BASEPATH . "\\" . $driver;

        if (!class_exists($className)) {
            throw new InvalidConfigurationException(
                "Driver should contain a valid Driver ClassName for the transcoding  service, {$driver} provided"
            );
        }
        $this->driver = new $className($params);
    }

    public function setDriver(DriverInterface $driver = null)
    {
        if (null !== $driver) {
            $this->driver = $driver;
        }
    }

    public function __call($method, array $params)
    {
        $interfaceName = static::DRIVER_BASEPATH . "\\" . static::INTERFACE_NAME;
        if (method_exists($interfaceName, $method)) {

            return call_user_func_array(array($this->driver, $method), $params);
        }

        return false;
    }

}