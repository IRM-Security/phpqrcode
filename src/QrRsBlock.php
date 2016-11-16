<?php
namespace Ruslan03492\phpqrcode;

class QrRsBlock {
  public $dataLength;
  public $data = array();
  public $eccLength;
  public $ecc = array();

  public function __construct($dl, $data, $el, &$ecc, QrRsItem $rs) {
    $rs->encode_rs_char($data, $ecc);

    $this->dataLength = $dl;
    $this->data = $data;
    $this->eccLength = $el;
    $this->ecc = $ecc;
  }
}
