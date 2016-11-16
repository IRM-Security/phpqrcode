<?php

namespace Ruslan03492\phpqrcode;

define('QR_VECT', TRUE);

class QrVect {

  //----------------------------------------------------------------------
  public static function eps($frame, $filename = FALSE, $pixelPerPoint = 4, $outerFrame = 4, $saveandprint = FALSE, $back_color = 0xFFFFFF, $fore_color = 0x000000, $cmyk = FALSE) {
    $vect = self::vectEPS($frame, $pixelPerPoint, $outerFrame, $back_color, $fore_color, $cmyk);

    if ($filename === FALSE) {
      header("Content-Type: application/postscript");
      header('Content-Disposition: filename="qrcode.eps"');
      echo $vect;
    }
    else {
      if ($saveandprint === TRUE) {
        QrTools::save($vect, $filename);
        header("Content-Type: application/postscript");
        header('Content-Disposition: filename="qrcode.eps"');
        echo $vect;
      }
      else {
        QrTools::save($vect, $filename);
      }
    }
  }


  //----------------------------------------------------------------------
  private static function vectEPS($frame, $pixelPerPoint = 4, $outerFrame = 4, $back_color = 0xFFFFFF, $fore_color = 0x000000, $cmyk = FALSE) {
    $h = count($frame);
    $w = strlen($frame[0]);

    $imgW = $w + 2 * $outerFrame;
    $imgH = $h + 2 * $outerFrame;

    if ($cmyk) {
      // convert color value into decimal eps format
      $c = round((($fore_color & 0xFF000000) >> 16) / 255, 5);
      $m = round((($fore_color & 0x00FF0000) >> 16) / 255, 5);
      $y = round((($fore_color & 0x0000FF00) >> 8) / 255, 5);
      $k = round(($fore_color & 0x000000FF) / 255, 5);
      $fore_color_string = $c . ' ' . $m . ' ' . $y . ' ' . $k . ' setcmykcolor' . "\n";

      // convert color value into decimal eps format
      $c = round((($back_color & 0xFF000000) >> 16) / 255, 5);
      $m = round((($back_color & 0x00FF0000) >> 16) / 255, 5);
      $y = round((($back_color & 0x0000FF00) >> 8) / 255, 5);
      $k = round(($back_color & 0x000000FF) / 255, 5);
      $back_color_string = $c . ' ' . $m . ' ' . $y . ' ' . $k . ' setcmykcolor' . "\n";
    }
    else {
      // convert a hexadecimal color code into decimal eps format (green = 0 1 0, blue = 0 0 1, ...)
      $r = round((($fore_color & 0xFF0000) >> 16) / 255, 5);
      $b = round((($fore_color & 0x00FF00) >> 8) / 255, 5);
      $g = round(($fore_color & 0x0000FF) / 255, 5);
      $fore_color_string = $r . ' ' . $b . ' ' . $g . ' setrgbcolor' . "\n";

      // convert a hexadecimal color code into decimal eps format (green = 0 1 0, blue = 0 0 1, ...)
      $r = round((($back_color & 0xFF0000) >> 16) / 255, 5);
      $b = round((($back_color & 0x00FF00) >> 8) / 255, 5);
      $g = round(($back_color & 0x0000FF) / 255, 5);
      $back_color_string = $r . ' ' . $b . ' ' . $g . ' setrgbcolor' . "\n";
    }

    $output =
      '%!PS-Adobe EPSF-3.0' . "\n" .
      '%%Creator: PHPQrcodeLib' . "\n" .
      '%%Title: QRcode' . "\n" .
      '%%CreationDate: ' . date('Y-m-d') . "\n" .
      '%%DocumentData: Clean7Bit' . "\n" .
      '%%LanguageLevel: 2' . "\n" .
      '%%Pages: 1' . "\n" .
      '%%BoundingBox: 0 0 ' . $imgW * $pixelPerPoint . ' ' . $imgH * $pixelPerPoint . "\n";

    // set the scale
    $output .= $pixelPerPoint . ' ' . $pixelPerPoint . ' scale' . "\n";
    // position the center of the coordinate system

    $output .= $outerFrame . ' ' . $outerFrame . ' translate' . "\n";


    // redefine the 'rectfill' operator to shorten the syntax
    $output .= '/F { rectfill } def' . "\n";

    // set the symbol color
    $output .= $back_color_string;
    $output .= '-' . $outerFrame . ' -' . $outerFrame . ' ' . ($w + 2 * $outerFrame) . ' ' . ($h + 2 * $outerFrame) . ' F' . "\n";


    // set the symbol color
    $output .= $fore_color_string;

    // Convert the matrix into pixels

    for ($i = 0; $i < $h; $i++) {
      for ($j = 0; $j < $w; $j++) {
        if ($frame[$i][$j] == '1') {
          $y = $h - 1 - $i;
          $x = $j;
          $output .= $x . ' ' . $y . ' 1 1 F' . "\n";
        }
      }
    }


    $output .= '%%EOF';

    return $output;
  }

  //----------------------------------------------------------------------
  public static function svg($frame, $filename = FALSE, $size = 200, $outerFrame = 4, $saveandprint = FALSE, $back_color = 0xFFFFFF, $fore_color = 0x000000) {
    $vect = self::vectSVG($frame, $size, $outerFrame, $back_color, $fore_color);

    if ($filename === FALSE) {
      header("Content-Type: image/svg+xml");
      //header('Content-Disposition: attachment, filename="qrcode.svg"');
      echo $vect;
    }
    else {
      if ($saveandprint === TRUE) {
        QrTools::save($vect, $filename);
        header("Content-Type: image/svg+xml");
        //header('Content-Disposition: filename="'.$filename.'"');
        echo $vect;
      }
      else {
        QrTools::save($vect, $filename);
      }
    }
  }


  //----------------------------------------------------------------------
  private static function vectSVG($frame, $size = 200, $outerFrame = 4, $back_color = 0xFFFFFF, $fore_color = 0x000000) {
    $h = count($frame);
    $w = strlen($frame[0]);
    $margin = $outerFrame;
    $back_color = str_pad(dechex($back_color), 6, "0", STR_PAD_LEFT);
    $fore_color = str_pad(dechex($fore_color), 6, "0", STR_PAD_LEFT);
    $x_y_size = $size / ($h + 2 * $outerFrame);
    $rect = '<rect x="0" y="0" width="' . $size . '" height="' . $size . '" style="fill:#' . $back_color . ';shape-rendering:crispEdges;"/>';
//            if ($border) {
    $border = self::borderCords(0);
    $x_y_size_border = $size / 52 + 12;
    foreach ($border['cords'] as $b) {
      $px = ($b['x'] / 2 * $x_y_size_border);
      $py = ($b['y'] / 2 * $x_y_size_border);
      $rect .= '<rect x="' . $px . '" y="' . $py . '" width="' . $x_y_size_border . '" height="' . $x_y_size_border . '" style="fill:#' . $fore_color . ';shape-rendering:crispEdges;"/>';
    }
    $margin = ($x_y_size_border * 3) + 4;
//            }

    for ($i = 0; $i < $h; $i++) {
      for ($j = 0; $j < $w; $j++) {
        if ($frame[$i][$j] == '1') {
          $px = ($j * $x_y_size + $margin);
          $py = ($i * $x_y_size + $margin);
          $rect .= '<rect x="' . $px . '" y="' . $py . '" width="' . $x_y_size . '" height="' . $x_y_size . '" style="fill:#' . $fore_color . ';shape-rendering:crispEdges;"/>';
        }
      }
    }

    $output = '<?xml version="1.0" standalone="yes"?><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100%">';
    $output .= $rect;
    $output .= '</svg>';
    return $output;
  }

  private function borderCords($border_type) {
    $border_1_cords = [
      ['x' => 0, 'y' => 0],
      ['x' => 0, 'y' => 2],
      ['x' => 0, 'y' => 4],
      ['x' => 0, 'y' => 8],
      ['x' => 0, 'y' => 12],
      ['x' => 0, 'y' => 16],
      ['x' => 0, 'y' => 20],
      ['x' => 0, 'y' => 24],
      ['x' => 0, 'y' => 28],
      ['x' => 0, 'y' => 32],
      ['x' => 0, 'y' => 36],
      ['x' => 0, 'y' => 40],
      ['x' => 0, 'y' => 44],
      ['x' => 0, 'y' => 48],
      ['x' => 0, 'y' => 50],
      ['x' => 0, 'y' => 52],
      ['x' => 2, 'y' => 0],
      ['x' => 2, 'y' => 4],
      ['x' => 2, 'y' => 8],
      ['x' => 2, 'y' => 12],
      ['x' => 2, 'y' => 16],
      ['x' => 2, 'y' => 20],
      ['x' => 2, 'y' => 24],
      ['x' => 2, 'y' => 28],
      ['x' => 2, 'y' => 32],
      ['x' => 2, 'y' => 36],
      ['x' => 2, 'y' => 40],
      ['x' => 2, 'y' => 44],
      ['x' => 2, 'y' => 48],
      ['x' => 2, 'y' => 52],
      ['x' => 4, 'y' => 0],
      ['x' => 4, 'y' => 2],
      ['x' => 4, 'y' => 4],
      ['x' => 4, 'y' => 6],
      ['x' => 4, 'y' => 8],
      ['x' => 4, 'y' => 10],
      ['x' => 4, 'y' => 12],
      ['x' => 4, 'y' => 14],
      ['x' => 4, 'y' => 16],
      ['x' => 4, 'y' => 18],
      ['x' => 4, 'y' => 20],
      ['x' => 4, 'y' => 22],
      ['x' => 4, 'y' => 24],
      ['x' => 4, 'y' => 26],
      ['x' => 4, 'y' => 28],
      ['x' => 4, 'y' => 30],
      ['x' => 4, 'y' => 32],
      ['x' => 4, 'y' => 34],
      ['x' => 4, 'y' => 36],
      ['x' => 4, 'y' => 38],
      ['x' => 4, 'y' => 40],
      ['x' => 4, 'y' => 42],
      ['x' => 4, 'y' => 44],
      ['x' => 4, 'y' => 46],
      ['x' => 4, 'y' => 48],
      ['x' => 4, 'y' => 50],
      ['x' => 4, 'y' => 52],
      ['x' => 6, 'y' => 4],
      ['x' => 6, 'y' => 48],
      ['x' => 8, 'y' => 0],
      ['x' => 8, 'y' => 2],
      ['x' => 8, 'y' => 4],
      ['x' => 8, 'y' => 48],
      ['x' => 8, 'y' => 50],
      ['x' => 8, 'y' => 52],
      ['x' => 10, 'y' => 4],
      ['x' => 10, 'y' => 48],
      ['x' => 12, 'y' => 0],
      ['x' => 12, 'y' => 2],
      ['x' => 12, 'y' => 4],
      ['x' => 12, 'y' => 48],
      ['x' => 12, 'y' => 50],
      ['x' => 12, 'y' => 52],
      ['x' => 14, 'y' => 4],
      ['x' => 14, 'y' => 48],
      ['x' => 16, 'y' => 0],
      ['x' => 16, 'y' => 2],
      ['x' => 16, 'y' => 4],
      ['x' => 16, 'y' => 48],
      ['x' => 16, 'y' => 50],
      ['x' => 16, 'y' => 52],
      ['x' => 18, 'y' => 4],
      ['x' => 18, 'y' => 48],
      ['x' => 20, 'y' => 0],
      ['x' => 20, 'y' => 2],
      ['x' => 20, 'y' => 4],
      ['x' => 20, 'y' => 48],
      ['x' => 20, 'y' => 50],
      ['x' => 20, 'y' => 52],
      ['x' => 22, 'y' => 4],
      ['x' => 22, 'y' => 48],
      ['x' => 24, 'y' => 0],
      ['x' => 24, 'y' => 2],
      ['x' => 24, 'y' => 4],
      ['x' => 24, 'y' => 48],
      ['x' => 24, 'y' => 50],
      ['x' => 24, 'y' => 52],
      ['x' => 26, 'y' => 4],
      ['x' => 26, 'y' => 48],
      ['x' => 28, 'y' => 0],
      ['x' => 28, 'y' => 2],
      ['x' => 28, 'y' => 4],
      ['x' => 28, 'y' => 48],
      ['x' => 28, 'y' => 50],
      ['x' => 28, 'y' => 52],
      ['x' => 30, 'y' => 4],
      ['x' => 30, 'y' => 48],
      ['x' => 32, 'y' => 0],
      ['x' => 32, 'y' => 2],
      ['x' => 32, 'y' => 4],
      ['x' => 32, 'y' => 48],
      ['x' => 32, 'y' => 50],
      ['x' => 32, 'y' => 52],
      ['x' => 34, 'y' => 4],
      ['x' => 34, 'y' => 48],
      ['x' => 36, 'y' => 0],
      ['x' => 36, 'y' => 2],
      ['x' => 36, 'y' => 4],
      ['x' => 36, 'y' => 48],
      ['x' => 36, 'y' => 50],
      ['x' => 36, 'y' => 52],
      ['x' => 38, 'y' => 4],
      ['x' => 38, 'y' => 48],
      ['x' => 40, 'y' => 0],
      ['x' => 40, 'y' => 2],
      ['x' => 40, 'y' => 4],
      ['x' => 40, 'y' => 48],
      ['x' => 40, 'y' => 50],
      ['x' => 40, 'y' => 52],
      ['x' => 42, 'y' => 4],
      ['x' => 42, 'y' => 48],
      ['x' => 44, 'y' => 0],
      ['x' => 44, 'y' => 2],
      ['x' => 44, 'y' => 4],
      ['x' => 44, 'y' => 48],
      ['x' => 44, 'y' => 50],
      ['x' => 44, 'y' => 52],
      ['x' => 46, 'y' => 4],
      ['x' => 46, 'y' => 48],
      ['x' => 48, 'y' => 0],
      ['x' => 48, 'y' => 2],
      ['x' => 48, 'y' => 4],
      ['x' => 48, 'y' => 6],
      ['x' => 48, 'y' => 8],
      ['x' => 48, 'y' => 10],
      ['x' => 48, 'y' => 12],
      ['x' => 48, 'y' => 14],
      ['x' => 48, 'y' => 16],
      ['x' => 48, 'y' => 18],
      ['x' => 48, 'y' => 20],
      ['x' => 48, 'y' => 22],
      ['x' => 48, 'y' => 24],
      ['x' => 48, 'y' => 26],
      ['x' => 48, 'y' => 28],
      ['x' => 48, 'y' => 30],
      ['x' => 48, 'y' => 32],
      ['x' => 48, 'y' => 34],
      ['x' => 48, 'y' => 36],
      ['x' => 48, 'y' => 38],
      ['x' => 48, 'y' => 40],
      ['x' => 48, 'y' => 42],
      ['x' => 48, 'y' => 44],
      ['x' => 48, 'y' => 46],
      ['x' => 48, 'y' => 48],
      ['x' => 48, 'y' => 50],
      ['x' => 48, 'y' => 52],
      ['x' => 50, 'y' => 0],
      ['x' => 50, 'y' => 4],
      ['x' => 50, 'y' => 8],
      ['x' => 50, 'y' => 12],
      ['x' => 50, 'y' => 16],
      ['x' => 50, 'y' => 20],
      ['x' => 50, 'y' => 24],
      ['x' => 50, 'y' => 28],
      ['x' => 50, 'y' => 32],
      ['x' => 50, 'y' => 36],
      ['x' => 50, 'y' => 40],
      ['x' => 50, 'y' => 44],
      ['x' => 50, 'y' => 48],
      ['x' => 50, 'y' => 52],
      ['x' => 52, 'y' => 0],
      ['x' => 52, 'y' => 2],
      ['x' => 52, 'y' => 4],
      ['x' => 52, 'y' => 8],
      ['x' => 52, 'y' => 12],
      ['x' => 52, 'y' => 16],
      ['x' => 52, 'y' => 20],
      ['x' => 52, 'y' => 24],
      ['x' => 52, 'y' => 28],
      ['x' => 52, 'y' => 32],
      ['x' => 52, 'y' => 36],
      ['x' => 52, 'y' => 40],
      ['x' => 52, 'y' => 44],
      ['x' => 52, 'y' => 48],
      ['x' => 52, 'y' => 50],
      ['x' => 52, 'y' => 52],
    ];
    $borders = [
      [
        'cords' => $border_1_cords,
      ],
    ];
    return $borders[$border_type];
  }
}
