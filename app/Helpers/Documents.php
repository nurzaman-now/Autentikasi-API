<?php

namespace App\Helpers;

class Documents
{
  public static function upload($image, $path, $name): string
  {
    // upload image
    $extension = $image->getClientOriginalExtension(); // Get the original file extension
    $storedFilename = $name . '.' . $extension;
    $image = $image->storeAs($path, $storedFilename);
    return $image;
  }
}
