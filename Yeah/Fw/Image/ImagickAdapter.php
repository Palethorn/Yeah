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
        $this->readParameters();
    }

    public function readParameters() {
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
        $this->readParameters();
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
        $this->readParameters();
    }

    public function resizeByHeight($height) {
        $width = $height * $this->aspect_ratio;
        $this->resizeimage($width, $height, \Imagick::FILTER_LANCZOS, 1);
        $this->readParameters();
    }

    public function anotate($params) {
        $draw = new \ImagickDraw();
        $draw->setfillcolor($this->getInverted());
        $draw->setFontSize(20);
        $lines = explode('%20', $params['text']);
        $i = 0;
        $row = '';
        foreach ($lines as $line) {
            $tmp = trim($row . ' ' . $line);
            $font_metrics = $this->queryfontmetrics($draw, $tmp);
            if(($text_width = ($font_metrics['textWidth'])) < $this->width) {
                $row = $tmp;
            } else {
                $this->annotateimage($draw, $this->width - ($text_width / 2), $i, 0, $row);
                $row = trim($line);
            }
            $i += $font_metrics['textHeight'];
        }
        $this->annotateimage($draw, $this->width - ($text_width / 2), $i, 0, $row);
    }

    public function getInverted() {
        $histogram = $this->getimagehistogram();
        $imagick_pixel = end($histogram);
        $colors = $imagick_pixel->getColor();
        foreach ($colors as $i => $color) {
            $colors[$i] = 255 ^ $color;
        }

        $new_imagick_pixel = new \ImagickPixel('rgb(' . $colors['r'] . ',' . $colors['g'] . ',' . $colors['b'] . ')');
        return $new_imagick_pixel;
    }

}
