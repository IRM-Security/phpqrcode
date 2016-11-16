<?php
namespace Ruslan03492\phpqrcode;

define('QR_IMAGE', TRUE);

class QrImage {

  //----------------------------------------------------------------------
  public static function png($frame, $filename = FALSE, $pixelPerPoint = 4, $outerFrame = 4, $saveandprint = FALSE, $back_color, $fore_color) {
    $image = self::image($frame, $pixelPerPoint, $outerFrame, $back_color, $fore_color);

    if ($filename === FALSE) {
      header("Content-type: image/png");
      imagepng($image);
    }
    else {
      if ($saveandprint === TRUE) {
        imagepng($image, $filename);
        header("Content-type: image/png");
        imagepng($image);
      }
      else {
        imagepng($image, $filename);
      }
    }

    imagedestroy($image);
  }

  //----------------------------------------------------------------------
  public static function jpg($frame, $filename = FALSE, $pixelPerPoint = 8, $outerFrame = 4, $q = 85) {
    $image = self::image($frame, $pixelPerPoint, $outerFrame);

    if ($filename === FALSE) {
      header("Content-type: image/jpeg");
      imagejpeg($image, NULL, $q);
    }
    else {
      imagejpeg($image, $filename, $q);
    }

    imagedestroy($image);
  }

  //----------------------------------------------------------------------
  private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4, $back_color = 0xFFFFFF, $fore_color = 0x000000) {
    $h = count($frame);
    $w = strlen($frame[0]);

    $imgW = $w + 2 * $outerFrame;
    $imgH = $h + 2 * $outerFrame;

    $base_image = imagecreate($imgW, $imgH);

    // convert a hexadecimal color code into decimal format (red = 255 0 0, green = 0 255 0, blue = 0 0 255)
    $r1 = round((($fore_color & 0xFF0000) >> 16), 5);
    $g1 = round((($fore_color & 0x00FF00) >> 8), 5);
    $b1 = round(($fore_color & 0x0000FF), 5);

    // convert a hexadecimal color code into decimal format (red = 255 0 0, green = 0 255 0, blue = 0 0 255)
    $r2 = round((($back_color & 0xFF0000) >> 16), 5);
    $g2 = round((($back_color & 0x00FF00) >> 8), 5);
    $b2 = round(($back_color & 0x0000FF), 5);


    $col[0] = imagecolorallocate($base_image, $r2, $g2, $b2);
    $col[1] = imagecolorallocate($base_image, $r1, $g1, $b1);

    imagefill($base_image, 0, 0, $col[0]);

    for ($y = 0; $y < $h; $y++) {
      for ($x = 0; $x < $w; $x++) {
        if ($frame[$y][$x] == '1') {
          imagesetpixel($base_image, $x + $outerFrame, $y + $outerFrame, $col[1]);
        }
      }
    }

    $target_image = imagecreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
    imagecopyresized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
    imagedestroy($base_image);

    return $target_image;
  }
}
