<?php
namespace Ruslan03492\phpqrcode\Images;

use Ruslan03492\phpqrcode\QrCode;

class QrImage {

  public static function getImage($data, QrCode $qr_code) {
    $image = self::image(
      $data,
      $qr_code->getSize(),
      $qr_code->getPadding(),
      $qr_code->getColorBackground(),
      $qr_code->getColorForeground(),
      $qr_code->isImageBorderSize(),
      $qr_code->getImageBorder(),
      $qr_code->isRotate()
    );

    ob_start();
    call_user_func('image' . $qr_code->getExtensions(), $image);
    $contents = ob_get_clean();
    imagedestroy($image);
    return $contents;
  }

  //----------------------------------------------------------------------
  private function image($frame, $size = 4, $outerFrame = 4, $back_color = 0xFFFFFF, $fore_color = 0x000000, $image_border_size = 200, $image_border = FALSE, $is_rotate = FALSE) {
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

    $target_image = imagecreate($size, $size);
    imagecopyresized($target_image, $base_image, 0, 0, 0, 0, $size, $size, $imgW, $imgH);
    imagedestroy($base_image);

    // Set background image for qr code.
    if (!empty($image_border)) {
      $base_background_image = imagecreatefrompng($image_border);

      // Resize background image.
      list($width, $height) = getimagesize($image_border);
      $background_image = imagecreatetruecolor($image_border_size, $image_border_size);
      imagecopyresized($background_image, $base_background_image, 0, 0, 0, 0, $image_border_size, $image_border_size, $width, $height);
      imagedestroy($base_background_image);

      // Set color background image.
      imagetruecolortopalette($background_image, FALSE, 255);
      $index = imagecolorclosest($background_image, 0, 0, 0);
      imagecolorset($background_image, $index, $r1, $g1, $b1);

      // Center image.
      $dst_x = (imagesx($background_image) - imagesx($target_image)) / 2;
      $dst_y = (imagesy($background_image) - imagesy($target_image)) / 2;
      imagecopy($background_image, $target_image, $dst_x, $dst_y, 0, 0, imagesx($target_image), imagesy($target_image));
      $target_image = $background_image;
    }

    // Add rotate for QR Code.
    if (!empty($is_rotate)) {
      // TODO: Fix this code. Offset image edge.
      $padding = 2;
      $size = imagesx($target_image);
      $target_image_resized = imagecreatetruecolor($size + $padding, $size + $padding);
      imagecopyresampled($target_image_resized, $target_image, $padding, $padding, 0, 0, $size, $size, $size, $size);
      imagedestroy($target_image);
      $bgd_color = 0xFFFFFF; // white
      $target_image = imagerotate($target_image_resized, 45, $bgd_color);
    }

    return $target_image;
  }
}
