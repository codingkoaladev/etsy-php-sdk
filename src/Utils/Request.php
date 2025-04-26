<?php

namespace Etsy\Utils;

/**
 *  HTTP request utilities.
 *
 * @author Rhys Hall hello@rhyshall.com
 */
class Request {

  /**
   * Prepares the request query parameters.
   *
   * @param array $params
   * @return string
   */
  public static function prepareParameters(array $params) {
    $query = http_build_query($params);
    return $query;
  }

  /**
   * Prepares any files in the POST data. Expects a path for files.
   *
   * @param array $params
   * @return array
   */
  public static function prepareFile(array $params) {
    if(!isset($params['image']) && !isset($params['file'])) {
      return false;
    }
    $type = isset($params['image']) ? 'image' : 'file';
    $data[] = [
      'name' => $type,
      'contents' => self::getFileContent($params[$type])
    ];
    foreach ($params as $key => $value) {
      if ($key == $type) continue;
      $data[] = [
        'name' => $key,
        'contents' => $value
      ];
    }
    return $data;
  }

  /**
   * Returns a query string as an array.
   *
   * @param string $query
   * @return array
   */
  public static function getParamaters($query) {
    parse_str($query, $params);
    return $params;
  }

    /**
     * Prepares a video file for upload.
     *
     * $params['video'] - a path to the file or an already loaded resource
     * $params['name'] - filename
     * $params['video_id'] - null or existing video id to assign to the listing
     *
     * @param mixed $params
     * @return array|false
     */
    public static function prepareVideo($params)
    {
        if (!isset($params['video'])) {
            return false;
        }
        $video = self::getVideoContent($params['video']);
        $videoId = $params['video_id'] ?? null;
        $name = $params['name'] ?? null;
        $params = [];
        if ($videoId) {
            $params[] = ['name' => 'video_id', 'contents' => $videoId];
        }
        if ($name) {
            $params[] = ['name' => 'name', 'contents' => $name];
        }
        $params[] = ['name' => 'video', 'contents' => $video];
        return $params;
    }

    /**
     * Get video content from path or resource
     *
     * @param string|resource $video Path to video file or video resource
     * @return false|resource Returns file resource or false on failure
     */
    private static function getVideoContent($video)
    {
        if (is_string($video) && file_exists($video)) {
            return self::getFileContent($video);
        }
        return $video;
    }

    /**
     * Get file content from a path
     *
     * @param string $path
     * @return false|resource
     */
    private static function getFileContent($path)
    {
        return fopen($path, 'r');
    }

}
