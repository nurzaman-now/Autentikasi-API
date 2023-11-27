<?php

namespace App\Helpers;

/**
 * Format response.
 */
class ResponseFormatter
{
  /**
   * API Response
   *
   * @var array
   */
  private static $response = [
    'meta' => [
      'code' => 200,
      'status' => 'success',
      'message' => null,
    ],
    'data' => null,
  ];

  /**
   * Give success response.
   */
  public static function responseSuccess($data = null, $message = null, $code = 200)
  {
    self::$response['meta']['message'] = $message;
    self::$response['meta']['code'] = $code;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['meta']['code']);
  }

  /**
   * Give error response.
   */
  public static function responseError($data = null, $message = null, $code = 400)
  {
    self::$response['meta']['status'] = 'error';
    self::$response['meta']['code'] = $code;
    self::$response['meta']['message'] = $message;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['meta']['code']);
  }

  public static function validatorError($errors)
  {
    $data = json_decode($errors, true);

    $result = [];
    foreach ($data as $key => $error) {
      $result[] = implode(', ', $error);
    }

    $message = implode(', ', $result);
    return ResponseFormatter::responseError(message: $message);
  }
}
