<?php

namespace Yeah\Fw\Image;

/**
 * Imagick adapter with additional methods
 * 
 * @param float $width
 * @param float $height
 * @param string $format
 * @param string $mime_type
 * @param float $aspect_ratio
 * 
 * @author David Cavar
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

    /**
     * Reads image parameters
     */
    public function readParameters() {
        $this->width = $this->getimagewidth();
        $this->height = $this->getimageheight();
        $this->format = $this->getimageformat();
        $this->mime_type = $this->getimagemimetype();
        $this->aspect_ratio = $this->width / $this->height;
    }

    /**
     * Gets maximum image size based on current size and specified aspect ratio,
     * without distorting the picture
     * 
     * @param float $aspect_ratio
     * @return array
     */
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

    /**
     * Crops image based on specified image width, height and start point
     * 
     * @param array $params
     * @return \Yeah\Fw\Image\ImagickAdapter
     */
    public function crop($params) {
        $this->cropimage($params['crop_width'], $params['crop_height'], $params['x'], $params['y']);
        $this->readParameters();
        return $this;
    }

    /**
     * Resize picture based on specified width and height parameters
     * 
     * @param array $params
     * @param bool $force
     */
    public function resize($params, $force = false) {
        if($force) {
            $this->resizeimage($params['width'], $params['height'], \Imagick::FILTER_LANCZOS, 1);
            return;
        }
    }

    /**
     * Resize by width parameter with regard to image aspect ratio
     * @param float $width
     */
    public function resizeByWidth($width) {
        $height = $width / $this->aspect_ratio;
        $this->resizeimage($width, $height, \Imagick::FILTER_LANCZOS, 1);
        $this->readParameters();
    }
    
    /**
     * Resize by height parameter with regard to image aspect ratio
     * @param float $width
     */
    public function resizeByHeight($height) {
        $width = $height * $this->aspect_ratio;
        $this->resizeimage($width, $height, \Imagick::FILTER_LANCZOS, 1);
        $this->readParameters();
    }

    /**
     * Writes centered text on image
     * 
     * @param array $params
     */
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

    /**
     * Gets inverted color based on image histogram
     * 
     * @return \ImagickPixel
     */
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
