<?php

namespace Kodify\TranscoderBundle\Tests\Service;

use Kodify\TranscoderBundle\Service\Driver\PandaStream;
use Kodify\TranscoderBundle\Tests\TestBaseClass;

abstract class abstractTranscoderDriverTest extends TestBaseClass
{
    /**
     * @var $object Kodify\TranscoderBundle\Service\Driver\PandaStream
     */
    protected $object;

    public function testGetVideosListCorrectCalls()
    {
        $this->validateCorrectCall(
            'getVideosList',
            array('path' => PandaStream::VIDEOS_REQUEST, 'params' => array('page' => 1, 'per_page' => 100))
        );
        $page = 2;
        $per_page = 100;
        $this->validateCorrectCall(
            'getVideosList',
            array('path' => PandaStream::VIDEOS_REQUEST, 'params' => array('page' => $page, 'per_page' => $per_page)),
            array('page' => $page, 'per_page' => $per_page)
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testGetVideosListByStatusIncorrectStatus()
    {
        $this->object->getVideosListByStatus('wrongStatus');
    }

    public function testGetVideosListByStatusCorrectCall()
    {
        $status = 'fail';
        $this->validateCorrectCall(
            'getVideosListByStatus',
            array(
                'path' => PandaStream::VIDEOS_REQUEST,
                'params' => array('page' => 1, 'per_page' => 100, 'status' => $status)
            ),
            array('status' => $status)
        );
        $status = 'success';
        $page = 2;
        $per_page = 100;
        $status = 'fail';
        $this->validateCorrectCall(
            'getVideosListByStatus',
            array(
                'path' => PandaStream::VIDEOS_REQUEST,
                'params' => array('page' => $page, 'per_page' => $per_page, 'status' => $status)
            ),
            array('status' => $status, 'page' => $page, 'per_page' => $per_page)
        );
    }

    /**
     * There are a bunch of methods that just encapsulate calls to the same method with different params
     * We will test them altogether.
     * @dataProvider getMethodsThatUseBasicGetCallData
     */
    public function testMethodsWithBasicGetCall($method, $validations, $params)
    {
        $this->validateCorrectCall($method, $validations, $params);
    }

    public function testGetVideoCompleteDataById()
    {
        $result = $this->object->GetVideoCompleteDataById('fakeVideoId');
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('params', $result);
        $this->assertArrayHasKey('encodings', $result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertSame('/videos/fakeVideoId.json', $result['path']);
        $this->assertSame(array(), $result['params']);
        $this->assertTrue(is_array($result['encodings']));
        $this->assertArrayHasKey('path', $result['encodings']);
        $this->assertArrayHasKey('params', $result['encodings']);
        $this->assertArrayHasKey('path', $result['metadata']);
        $this->assertArrayHasKey('params', $result['metadata']);
        $this->assertSame('/videos/fakeVideoId/encodings.json', $result['encodings']['path']);
        $this->assertSame('/videos/fakeVideoId/metadata.json', $result['metadata']['path']);
        $this->assertSame(array(), $result['encodings']['params']);
        $this->assertSame(array(), $result['metadata']['params']);
    }

    public function testGetClipsListByStatusCorrectCall()
    {
        $status = 'fail';
        $this->validateCorrectCall(
            'getClipsListByStatus',
            array(
                'path' => PandaStream::CLIPS_REQUEST,
                'params' => array('status' => $status)
            ),
            array('status' => $status)
        );
        $status = 'success';
    }


    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @dataProvider deleteMethodsList
     */
    public function testDeleteXXXByIdThrowingError($method)
    {
        $expected = array('error' => 'error', 'message' => 'message');
        $this->object
            ->shouldReceive('delete')
            ->andReturn(json_encode($expected));
        $this->assertSame(
            $expected,
            $this->object->$method('id')
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @dataProvider deleteMethodsList
     */
    public function testDeleteXXXByIdWithoutDeleted($method)
    {
        $expected = array('deleted' => false);
        $this->object
            ->shouldReceive('delete')
            ->andReturn(json_encode($expected));
        $this->assertSame(
            $expected,
            $this->object->$method('id')
        );
    }

    /**
     * @dataProvider deleteMethodsList
     */
    public function testDeleteXXXByIdOKCall($method)
    {
        $expected = array('deleted' => true);
        $this->object
            ->shouldReceive('delete')
            ->andReturn(json_encode($expected));
        $this->assertSame(
            $expected,
            $this->object->$method('id')
        );
    }

    public function deleteMethodsList()
    {
        return array(
            'deleteVideoById' => array('deleteVideoById'),
            'deleteClipById' => array('deleteClipById'),
            'deleteFormatById' => array('deleteFormatById'),
        );
    }


    public function testDeleteClipByIdOKCall()
    {
        $expected = array('deleted' => true);
        $this->object
            ->shouldReceive('delete')
            ->andReturn(json_encode($expected));
        $this->assertSame(
            $expected,
            $this->object->deleteClipById('clipId')
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testDeleteClipByIdWithoutDeleted()
    {
        $expected = array('deleted' => false);
        $this->object
            ->shouldReceive('delete')
            ->andReturn(json_encode($expected));
        $this->assertSame(
            $expected,
            $this->object->deleteClipById('clipId')
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testDeleteClipByIdThrowingException()
    {
        $expected = array('error' => 'error', 'message' => 'message');
        $this->object
            ->shouldReceive('delete')
            ->andReturn(json_encode($expected));
        $this->assertSame(
            $expected,
            $this->object->deleteClipById('clipId')
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testBasicPostCallErrorResult()
    {
        $obj = \Mockery::mock('Kodify\TranscoderBundle\Service\Driver\PandaStream[post]', array());
        $obj
            ->shouldReceive('post')
            ->andReturn(json_encode(array('error' => 'error', 'message' => 'message')));

        $this->callProtected(
            'Kodify\TranscoderBundle\Service\Driver\PandaStream',
            'basicPostCall',
            array('url' => 'url', 'params' => array()),
            $obj
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testDecodeAndReturnException()
    {
        $this->callProtected(
            'Kodify\TranscoderBundle\Service\Driver\PandaStream',
            'decodeAndReturn',
            array('text' => ''),
            $this->object
        );
    }

    public function testRemoveStdClassFromDecodedResponseWithArray()
    {
        $decodedResponse = array(
            'hola' => new \StdClass(),
            'tupu' => array('ea'),
            'supu' => new \StdClass(),
        );

        $removed = $this->callProtected(
            'Kodify\TranscoderBundle\Service\Driver\PandaStream',
            'removeStdClassFromDecodedResponse',
            array('decodedResponse' => $decodedResponse
            ),
            $this->object
        );
        foreach ($decodedResponse as $key => $value) {
            $this->assertArrayHasKey($key, $removed);
            $this->assertNotInstanceOf('StdClass', $removed[$key]);
        }

    }

    public function testCreateFormat()
    {
        $params = array(array(
            'name' => 'name',
            'title' => 'title',
            'extname' => 'extName',
            'width' => 'width',
            'height' => 'height',
            'upscale' => 'upscale',
            'aspect_mode' => 'aspectMode',
            'two_pass' => 'twoPass',
            'video_bitrate' => 'videoBitrate',
            'fps' => 'fps',
            'keyfrrame_interval' => 'keyframeInterval',
            'keyframe_rate' => 'keyframeRate',
            'audio_bitrate' => 'audioBitrate',
            'audio_sample_rate' => 'audioSampleRate',
            'audio_channels' => 'audioChannels',
            'clip_length' => 'clipLength',
            'clip_offset' => 'clipOffset',
            'frame_count' => 'screenshotsCount')
        );
        $this->validateCorrectCall(
            'createFormat',
            array(
                'path' => PandaStream::FORMATS_REQUEST,
                'params' => reset($params)
            ),
            $params
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFormatWithoutName()
    {
        $params = array(array());
        $this->validateCorrectCall(
            'createFormat',
            array(
                'path' => PandaStream::FORMATS_REQUEST,
                'params' => reset($params)
            ),
            $params
        );
    }

    public function getMethodsThatUseBasicGetCallData()
    {
        return array(
            'getVideoById' => array(
                'getVideoById',
                array('path' => '/videos/videoId.json'),
                array('videoId' => 'videoId')
            ),
            'getVideoEncodingsById' => array(
                'getVideoEncodingsById',
                array('path' => '/videos/videoId/encodings.json'),
                array('videoId' => 'videoId')
            ),
            'getVideoMetadataById' => array(
                'getVideoMetadataById',
                array('path' => '/videos/videoId/metadata.json'),
                array('videoId' => 'videoId')
            ),
            'getClipsList' => array(
                'getClipsList',
                array('path' => PandaStream::CLIPS_REQUEST),
                array()
            ),
            'getFormatsList' => array(
                'getFormatsList',
                array('path' => PandaStream::FORMATS_REQUEST),
                array()
            ),
            'getClipById' => array(
                'getClipById',
                array('path' => '/encodings/clipId.json'),
                array('clipId' => 'clipId')
            ),
            'getFormatById' => array(
                'getFormatById',
                array('path' => 'profiles/formatId.json'),
                array('formatId' => 'formatId')
            ),
            'addVideoFromUrl' => array(
                'addVideoFromUrl',
                array(
                    'path' => PandaStream::VIDEOS_REQUEST,
                    'params' => array('source_url' => 'source_url', 'payload' => 'payload', 'profiles' => 'profiles')
                ),
                array('source_url' => 'source_url', 'payload' => 'payload', 'profiles' => 'profiles')
            ),
            'addVideoFromFile' => array(
                'addVideoFromFile',
                array(
                    'path' => PandaStream::VIDEOS_REQUEST,
                    'params' => array('file' => 'file', 'payload' => 'payload', 'profiles' => 'profiles')
                ),
                array('file' => 'file', 'payload' => 'payload', 'profiles' => 'profiles')
            ),
            'getClipsListByVideoId' => array(
                'getClipsListByVideoId',
                array(
                    'path' => PandaStream::CLIPS_REQUEST,
                    'params' => array('video_id' => 'videoId')
                ),
                array('video_id' => 'videoId')
            ),
            'createClipFromVideo' => array(
                'createClipFromVideo',
                array(
                    'path' => PandaStream::CLIPS_REQUEST,
                    'params' => array('video_id' => 'videoId', 'profile_id' => 'profileId', 'profile_name' => 'profileName'),
                ),
                array('video_id' => 'videoId', 'profile_id' => 'profileId', 'profile_name' => 'profileName')
            ),
            'cancelClipEncodingById' => array(
                'cancelClipEncodingById',
                array(
                    'path' => '/encodings/clipId/cancel.json'
                ),
                array('clipId' => 'clipId')
            )
        );
    }


    protected function validateCorrectCall($method, array $validations, array $params = array())
    {
        $result = call_user_func_array(array($this->object, $method), $params);
        if (isset($validations['path'])) {
            $this->assertSame($validations['path'], $result['path']);
        }
        if (isset($validations['params'])) {
            foreach ($validations['params'] as $key => $value) {
                $this->assertSame($value, $result['params']->$key);
            }
        }
    }

}