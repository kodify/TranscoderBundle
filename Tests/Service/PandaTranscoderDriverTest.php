<?php

namespace Kodify\TranscoderBundle\Tests\Service;

use Kodify\TranscoderBundle\Service\Driver\PandaStream;
use Kodify\TestsBundle\Tests\BaseClass;

/**
 * @group transcoder
 */
class PandaTranscoderDriverTest extends abstractTranscoderDriverTest
{

    /**
     * @var $object Kodify\TranscoderBundle\Service\Driver\PandaStream
     */
    protected $object;

    public function setUp()
    {
        $this->object = \Mockery::mock('Kodify\TranscoderBundle\Service\Driver\PandaStream[get,post,put,delete]', array());
        $returnPathsMethod = function ($request_path, $params = array()) {
            return json_encode(array('path' => $request_path, 'params' => $params));
        };
        $this->object
            ->shouldReceive('get')
            ->andReturnUsing($returnPathsMethod);
        $this->object
            ->shouldReceive('post')
            ->andReturnUsing($returnPathsMethod);
        $this->object
            ->shouldReceive('put')
            ->andReturnUsing($returnPathsMethod);
    }


}