<?php

namespace Kodify\TranscoderBundle\Service\Driver;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class PandaStream extends \Panda implements DriverInterface
{
    const API_VERSION = 2;
    const API_PORT    = 443;

    const VIDEOS_REQUEST  = '/videos.json';
    const CLIPS_REQUEST   = '/encodings.json';
    const FORMATS_REQUEST = '/profiles.json';

    const FAIL_STATUS       = 'fail';
    const SUCCESS_STATUS    = 'success';
    const PROCESSING_STATUS = 'processing';

    protected $videoStatusList = array(self::FAIL_STATUS, self::SUCCESS_STATUS, self::PROCESSING_STATUS);

    public function __construct(array $connectionData)
    {
        // @codingStandardsIgnoreStart
        $this->api_version = static::API_VERSION;
        $this->cloud_id    = $connectionData['cloudId'];
        $this->access_key  = $connectionData['accessKey'];
        $this->secret_key  = $connectionData['secretKey'];
        $this->api_host    = $connectionData['apiUrl'];
        $this->api_port    = static::API_PORT;
        // @codingStandardsIgnoreEnd
    }

    // Videos

    public function getVideosList($page = 1, $pageSize = 100)
    {
        $videosList = $this->get(static::VIDEOS_REQUEST, array('page' => $page, 'per_page' => $pageSize));

        return $this->decodeAndReturn($videosList);
    }

    public function getVideosListByStatus($status, $page = 1, $pageSize = 100)
    {
        $this->validateStatus($status);

        return $this->decodeAndReturn(
            $this->get(static::VIDEOS_REQUEST, array('status' => $status, 'page' => $page, 'per_page' => $pageSize))
        );
    }

    public function getVideoById($videoId)
    {

        return $this->basicGetCall("/videos/{$videoId}.json");
    }

    public function getVideoEncodingsById($videoId)
    {

        return $this->basicGetCall("/videos/{$videoId}/encodings.json");
    }

    public function getVideoMetadataById($videoId)
    {

        return $this->basicGetCall("/videos/{$videoId}/metadata.json");
    }

    /**
     * Will fetch the video Data from the transcoder including encodings and metadata
     * @param mixed $videoId
     */
    public function getVideoCompleteDataById($videoId)
    {
        $videoData              = $this->getVideoById($videoId);
        $videoData['encodings'] = $this->getVideoEncodingsById($videoId);
        $videoData['metadata']  = $this->getVideoMetadataById($videoId);

        return $videoData;
    }

    /**
     * Will add a file to our pandastream account from a Url
     */
    public function addVideoFromUrl($sourceUrl, $payload = '', $profiles = 'none')
    {
        $requestParams = array(
            'payload'    => $payload,
            'profiles'   => $profiles,
            'source_url' => $sourceUrl,
        );

        // TODO: RECHECK ERROR HANDLING
        return $this->decodeAndReturn(
            $this->post(
                static::VIDEOS_REQUEST,
                $requestParams
            )
        );
    }

    public function addVideoFromFile($file, $payload = '', $profiles = 'none', $pathFormat = '')
    {
        $requestParams = array(
            'payload'     => $payload,
            'profiles'    => $profiles,
            'file'        => $file
        );

        if ($pathFormat != '') {
            $requestParams['path_format'] = $pathFormat;
        }

        // TODO: RECHECK ERROR HANDLING
        return $this->decodeAndReturn(
            $this->post(
                static::VIDEOS_REQUEST,
                $requestParams
            )
        );
    }


    public function deleteVideoById($videoId)
    {
        $url         = "/videos/{$videoId}.json";
        $failMessage = "Couldn't delete video with ID={$videoId}";

        return $this->basicDeleteCall($url, $failMessage);
    }


    // Clips

    public function getClipsList()
    {

        return $this->basicGetCall(static::CLIPS_REQUEST);
    }

    public function getClipById($clipId)
    {

        return $this->basicGetCall("/encodings/{$clipId}.json");
    }

    public function getClipsListByStatus($status, $page = 1, $pageSize = 100)
    {
        $this->validateStatus($status);

        return $this->decodeAndReturn(
            $this->get(static::CLIPS_REQUEST, array('status' => $status, 'per_page' => $pageSize, 'page' => $page))
        );
    }

    public function getClipsListByVideoId($videoId)
    {

        return $this->decodeAndReturn(
            $this->get(static::CLIPS_REQUEST, array('video_id' => $videoId))
        );
    }

    public function createClipFromVideo($videoId, $formatId = '', $formatName = '')
    {
        $url    = static::CLIPS_REQUEST;
        $params = array(
            'video_id'     => $videoId,
            'profile_id'   => $formatId,
            'profile_name' => $formatName,

        );

        return $this->basicPostCall($url, $params);
    }


    public function cancelClipEncodingById($clipId)
    {
        $url    = "/encodings/{$clipId}/cancel.json";
        $params = array();

        return $this->basicPostCall($url, $params);
    }

    public function deleteClipById($clipId)
    {
        $url         = "/encodings/{$clipId}.json";
        $failMessage = "Couldn't delete clip with ID={$clipId}";

        return $this->basicDeleteCall($url, $failMessage);
    }

    // Formats

    public function getFormatsList()
    {

        return $this->basicGetCall(static::FORMATS_REQUEST);
    }

    public function getFormatById($formatId)
    {

        return $this->basicGetCall("profiles/{$formatId}.json");
    }

    public function deleteFormatById($formatId)
    {
        $url         = "/profiles/{$formatId}.json";
        $failMessage = "Couldn't delete format with ID={$formatId}";

        return $this->basicDeleteCall($url, $failMessage);
    }

    public function createFormat(array $params)
    {
        $compulsoryArguments = array('name', 'title', 'extname', 'width', 'height');
        foreach ($compulsoryArguments as $argument) {
            if (empty($params[$argument])) {
                throw new \InvalidArgumentException("{$argument} is compulsory when creating a profile");
            }
        }

        return $this->basicPostCall(static::FORMATS_REQUEST, $params);
    }

    /**
     * This method makes a simple get request to the url and decodes the result
     * Abstraction over several times repeated behaviour in the class
     * @param string $url
     *
     * @return mixed
     */
    protected function basicGetCall($url)
    {
        $result = $this->get($url);

        return $this->decodeAndReturn($result);
    }

    /**
     * @param $url
     * @param $params
     *
     * @return array
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    protected function basicPostCall($url, $params)
    {
        $result = $this->decodeAndReturn(
            $this->post($url, $params)
        );

        if (isset($result['error'])) {
            throw new InvalidArgumentException($result['message']);
        }

        return $result;
    }

    /**
     * @param $url
     * @param $failMessage
     * @return array
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    protected function basicDeleteCall($url, $failMessage)
    {
        $deletionResult = $this->delete($url);
        $result         = $this->decodeAndReturn($deletionResult);

        if (isset($result['error'])) {
            throw new InvalidArgumentException($result['message']);
        } else {
            if (!isset($result['deleted']) || !($result['deleted'])) {
                throw new InvalidArgumentException($failMessage);
            }
        }

        return $result;
    }


    protected function removeStdClassFromDecodedResponse($decodedResponse)
    {
        if ($decodedResponse instanceof \stdClass) {
            $decodedResponse = get_object_vars($decodedResponse);
        } elseif (is_array($decodedResponse)) {
            $newResponse = array();
            foreach ($decodedResponse as $key => $response) {
                $newResponse[$key] = $this->removeStdClassFromDecodedResponse($response);
            }
            $decodedResponse = $newResponse;
        }

        return $decodedResponse;
    }

    protected function decodeAndReturn($text)
    {
        $this->lastResponse = $text;
        $decoded = json_decode($text);
        if (null === $decoded) {
            throw new InvalidArgumentException('Incorrect arguments for request');
        }

        return $this->removeStdClassFromDecodedResponse($decoded);
    }

    /**
     * @param string $status
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    protected function validateStatus($status)
    {
        if (!in_array($status, $this->videoStatusList)) {
            throw new InvalidArgumentException(
                "Incorrect status, available status: \n" . var_export($this->videoStatusList, true)
            );
        }
    }

    public function getLastRawResponse()
    {
        return $this->lastResponse;
    }
}
