<?php

namespace Kodify\TranscoderBundle\Service\Driver;

interface DriverInterface
{

    // Videos

    public function getVideosList($page = 1, $pageSize = 100);

    public function getVideosListByStatus($status, $page = 1, $pageSize = 100);

    public function getVideoById($videoId);

    public function getVideoEncodingsById($videoId);

    public function getVideoMetadataById($videoId);

    /**
     * Will fetch the video Data from the transcoder including encodings and metadata
     * @param mixed $videoId
     */
    public function getVideoCompleteDataById($videoId);

    /**
     * Will add the video in url to our transcoding service
     * @param string $source_url
     * @param string $payload
     * @param string $profiles comma separated values for the profiles to be used
     * @return mixed
     */
    public function addVideoFromUrl($source_url, $payload = '', $profiles = '');

    public function addVideoFromFile($file, $payload = '', $profiles = '');

    public function deleteVideoById($videoId);

    // Clips

    public function getClipsList();

    public function getClipsListByStatus($status, $page = 1, $pageSize = 100);

    public function getClipById($clipId);

    public function getClipsListByVideoId($videoId);

    public function createClipFromVideo($videoId, $formatId = '', $formatName = '');

    public function cancelClipEncodingById($clipId);

    public function deleteClipById($clipId);

    // Formats

    public function getFormatsList();

    public function getFormatById($formatId);

    public function createFormat(array $args);

    public function deleteFormatById($formatId);

}
