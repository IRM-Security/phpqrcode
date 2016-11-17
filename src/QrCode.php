<?php

namespace Ruslan03492\phpqrcode;

use Ruslan03492\phpqrcode\Images\QrImage;
use Ruslan03492\phpqrcode\Images\QrVect;
use Ruslan03492\phpqrcode\Inputs\QrInput;
use Exception;

define('QR_CACHEABLE', TRUE);                                                               // use cache - more disk reads but less CPU power, masks and format templates are stored there
define('QR_CACHE_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);  // used when QR_CACHEABLE === true
define('QR_LOG_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);                                // default error logs dir

define('QR_FIND_BEST_MASK', TRUE);                                                          // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
define('QR_FIND_FROM_RANDOM', FALSE);                                                       // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
define('QR_DEFAULT_MASK', 2);                                                               // when QR_FIND_BEST_MASK === false

define('QR_PNG_MAXIMUM_SIZE', 1024);

// Encoding modes
define('QR_MODE_NUL', -1);
define('QR_MODE_NUM', 0);
define('QR_MODE_AN', 1);
define('QR_MODE_8', 2);
define('QR_MODE_KANJI', 3);
define('QR_MODE_STRUCTURE', 4);

// Levels of error correction.
define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);

// Supported output formats
define('QR_FORMAT_TEXT', 0);
define('QR_FORMAT_PNG', 1);

define('QRSPEC_VERSION_MAX', 40);
define('QRSPEC_WIDTH_MAX', 177);

define('QRCAP_WIDTH', 0);
define('QRCAP_WORDS', 1);
define('QRCAP_REMINDER', 2);
define('QRCAP_EC', 3);
define('QR_IMAGE', TRUE);

define('STRUCTURE_HEADER_BITS', 20);
define('MAX_STRUCTURED_SYMBOLS', 16);

define('N1', 3);
define('N2', 3);
define('N3', 40);
define('N4', 10);
define('QR_VECT', TRUE);

class QrCode {

  /**
   * @var null
   */
  protected $data = NULL;

  /**
   * @var null
   */
  protected $width;

  /**
   * @var int
   */
  protected $version = 1;

  /**
   * @var int
   */
  protected $level = QR_ECLEVEL_L;

  /**
   * @var int
   */
  protected $hint = QR_MODE_8;

  /**
   * @var bool
   */
  protected $caseSensitive = TRUE;

  /**
   * @var string
   */
  protected $text = '';

  /**
   * @var int
   */
  protected $colorForeground = 0x000000;

  /**
   * @var int
   */
  protected $colorBackground = 0xFFFFFF;

  /**
   * @var int
   */
  protected $size = 200;

  /**
   * @var int
   */
  protected $padding = 0;

  /**
   * @var string
   */
  protected $extensions = 'png';

  /**
   * @var bool
   */
  protected $filePath = FALSE;

  protected $isRotate = FALSE;

  protected $imageBorder;

  protected $vectorBorder;

  protected $imageBorderSize;

  /**
   * @return boolean
   */
  public function isImageBorderSize() {
    return $this->imageBorderSize;
  }

  /**
   * @param boolean $image_border_size
   * @return $this
   */
  public function setImageBorderSize($image_border_size) {
    $this->imageBorderSize = $image_border_size;
    return $this;
  }

  /**
   * @return boolean
   */
  public function isRotate() {
    return $this->isRotate;
  }

  /**
   * @param boolean $is_rotate
   * @return $this
   */
  public function setIsRotate($is_rotate) {
    $this->isRotate = $is_rotate;
    return $this;
  }

  /**
   * @return boolean
   */
  public function getImageBorder() {
    return $this->imageBorder;
  }

  /**
   * @param boolean $image_border
   * @return $this
   */
  public function setImageBorder($image_border) {
    $this->imageBorder = $image_border;
    return $this;
  }

  /**
   * @return boolean
   */
  public function isVectorBorder() {
    return $this->vectorBorder;
  }

  /**
   * @param boolean $vector_border
   * @return $this
   */
  public function setVectorBorder($vector_border) {
    $this->vectorBorder = $vector_border;
    return $this;
  }

  /**
   * @return string
   */
  public function getExtensions() {
    return $this->extensions;
  }

  /**
   * @param string $extensions
   * Allowed extensions JPEG, PNG, EPS, SVG.
   * @return $this
   */
  public function setExtensions($extensions) {
    if ($extensions == 'jpg') {
      $extensions = 'jpeg';
    }
    $this->extensions = $extensions;
    return $this;
  }

  /**
   * @return string
   */
  public function getText() {
    return $this->text;
  }

  /**
   * @param string $text
   * @return $this
   */
  public function setText($text) {
    $this->text = $text;
    return $this;
  }

  /**
   * @return int
   */
  public function getColorForeground() {
    return $this->colorForeground;
  }

  /**
   * @param int $color_foreground
   * @return $this
   */
  public function setColorForeground($color_foreground) {
    $this->colorForeground = $color_foreground;
    return $this;
  }

  /**
   * @return int
   */
  public function getColorBackground() {
    return $this->colorBackground;
  }

  /**
   * @param int $color_background
   * @return $this
   */
  public function setColorBackground($color_background) {
    $this->colorBackground = $color_background;
    return $this;
  }

  /**
   * @return int
   */
  public function getSize() {
    return $this->size;
  }

  /**
   * @param int $size
   * @return $this
   */
  public function setSize($size) {
    $this->size = $size;
    return $this;
  }

  /**
   * @return int
   */
  public function getPadding() {
    return $this->padding;

  }

  /**
   * @param int $padding
   * @return $this
   */
  public function setPadding($padding) {
    $this->padding = $padding;
    return $this;
  }

  /**
   * @return string
   * File path.
   */
  public function getFilePath() {
    return $this->filePath;
  }

  /**
   * @param boolean $file_path
   * @return $this
   */
  public function setFilePath($file_path) {
    $this->filePath = $file_path;
    return $this;
  }

  /**
   * Helper function for binarize text.
   */
  public function binarize($frame) {
    $len = count($frame);
    foreach ($frame as &$frameLine) {

      for ($i = 0; $i < $len; $i++) {
        $frameLine[$i] = (ord($frameLine[$i]) & 1) ? '1' : '0';
      }
    }
    return $frame;
  }

  /**
   * Helper function for encode mask.
   */
  protected function encodeMask(QrInput $input, $mask) {
    if ($input->getVersion() < 0 || $input->getVersion() > QRSPEC_VERSION_MAX) {
      throw new Exception('wrong version');
    }
    if ($input->getErrorCorrectionLevel() > QR_ECLEVEL_H) {
      throw new Exception('wrong level');
    }

    $raw = new QrRawCode($input);

    $version = $raw->version;
    $width = QrSpec::getWidth($version);
    $frame = QrSpec::newFrame($version);

    $filler = new FrameFiller($width, $frame);
    if (is_null($filler)) {
      return NULL;
    }

    // inteleaved data and ecc codes
    for ($i = 0; $i < $raw->dataLength + $raw->eccLength; $i++) {
      $code = $raw->getCode();
      $bit = 0x80;
      for ($j = 0; $j < 8; $j++) {
        $addr = $filler->next();
        $filler->setFrameAt($addr, 0x02 | (($bit & $code) != 0));
        $bit = $bit >> 1;
      }
    }
    unset($raw);

    // remainder bits
    $j = QrSpec::getRemainder($version);
    for ($i = 0; $i < $j; $i++) {
      $addr = $filler->next();
      $filler->setFrameAt($addr, 0x02);
    }

    $frame = $filler->frame;
    unset($filler);


    // masking
    $maskObj = new QrMask();
    if ($mask < 0) {

      if (QR_FIND_BEST_MASK) {
        $masked = $maskObj->mask($width, $frame, $input->getErrorCorrectionLevel());
      }
      else {
        $masked = $maskObj->makeMask($width, $frame, (intval(QR_DEFAULT_MASK) % 8), $input->getErrorCorrectionLevel());
      }
    }
    else {
      $masked = $maskObj->makeMask($width, $frame, $mask, $input->getErrorCorrectionLevel());
    }

    if ($masked == NULL) {
      return NULL;
    }

    $this->version = $version;
    $this->width = $width;
    $this->data = $masked;

    return $this;
  }

  /**
   * Helper function for encode input.
   */
  protected function encodeInput(QrInput $input) {
    return $this->encodeMask($input, -1);
  }

  /**
   * Helper function for encode string.
   */
  protected function encodeString() {

    if ($this->hint != QR_MODE_8 && $this->hint != QR_MODE_KANJI) {
      throw new Exception('bad hint');
    }

    $input = new QrInput($this->version, $this->level);
    if ($input == NULL) {
      return NULL;
    }

    $ret = QrSplit::splitStringToQRinput($this->text, $input, $this->hint, $this->caseSensitive);
    if ($ret < 0) {
      return NULL;
    }
    return $this->encodeInput($input);
  }

  /**
   * @return mixed
   * @throws \Exception
   */
  public function encode() {
    $data = $this
      ->encodeString()
      ->binarize($this->data);

    if ($this->filePath !== FALSE) {
      file_put_contents($this->filePath, join("\n", $data));
    }
    else {
      return $data;
    }
  }

  /**
   * Get Content.
   */
  public function get() {
    $data = $this->encode();
    $contents = '';
    switch ($this->extensions) {
      case 'png' :
      case 'jpeg' :
        $contents = QrImage::getImage($data, $this);
        break;
      case 'svg' :
      case 'eps' :
        $contents = QrVect::getVect($data, $this);
        break;
    }
    return $contents;
  }

  /**
   * Get Data uri.
   */
  public function getDataUri() {
    return 'data:image/' . $this->extensions . ';base64,'.base64_encode($this->get());
  }

}
