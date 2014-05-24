<?php

namespace Yeah\Fw\Image;

/**
 * @property type $name Description
 */
class ImagickAdapter extends \Imagick {

    public $width;
    public $height;
    public $format;
    public $mime_type;
    public $aspect_ratio;

    public function __construct($path) {
        if(!file_exists($path)) {
            throw new \Exception('Image not found!', 404, null);
        }
        parent::__construct($path);
        $this->width = $this->getimagewidth();
        $this->height = $this->getimageheight();
        $this->format = $this->getimageformat();
        $this->mime_type = $this->getimagemimetype();
        $this->aspect_ratio = $this->width / $this->height;
    }

    public function getMaxSize($aspect_ratio) {
        if($this->aspect_ratio > $aspect_ratio) {
            $height = $this->height;
            $width = $this->height * $aspect_ratio;
        } else {
            $width = $this->width;
            $height = $this->width / $aspect_ratio;
        }
        return array("width" => $width, 'height' => $height);
    }

    public function crop($params) {
        $this->cropimage($params['crop_width'], $params['crop_height'], $params['x'], $params['y']);
        return $this;
    }

    public function resize($params, $force = false) {
        if($force) {
            $this->resizeimage($params['width'], $params['height'], \Imagick::FILTER_LANCZOS, 1);
            return;
        }
    }

    public function resizeByWidth($width) {
        $height = $width / $this->aspect_ratio;
        $this->resizeimage($width, $height, \Imagick::FILTER_LANCZOS, 1);
    }

    public function resizeByHeight($height) {
        $width = $height * $this->aspect_ratio;
        $this->resizeimage($width, $height, \Imagick::FILTER_LANCZOS, 1);
    }

    public function anotate($params) {
        $draw = new \ImagickDraw();
        $draw->setfillcolor($this->getInverted());
        $draw->setFontSize(30);
        $this->annotateimage($draw, 150, 150, 0, $params['text']);
    }

    public function getInverted() {
        $histogram = $this->getimagehistogram();
        $imagick_pixel = end($histogram);
        $colors = $imagick_pixel->getColor();
        foreach($colors as $i => $color) {
            $colors[$i] = 255 ^ $color;
        }
        
        $new_imagick_pixel = new \ImagickPixel('rgb(' . $colors['r'] . ',' . $colors['g'] . ',' . $colors['b'] . ')');
        return $new_imagick_pixel;
    }
}
