<?php
namespace Yeah\Fw\Toolbox;

/**
 * @property type $name Description
 */
class Image extends Imagick {

    public $width;
    public $height;
    public $format;
    public $mime_type;
    public $aspect_ratio;

    public function __construct($path) {
        if(!file_exists($path)) {
            throw new \Exception('Poster not found!', 404, null);
        }
        parent::__construct($path);
        $this->width = $this->getimagewidth();
        $this->height = $this->getimageheight();
        $this->format = $this->getimageformat();
        $this->mime_type = $this->getimagemimetype();
        $this->aspect_ratio = $this->width / $this->height;
    }

    public function getMaxSize($aspect_ratio) {
        if ($this->aspect_ratio > $aspect_ratio) {
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

    public function resize($params) {
        $this->resizeimage($params['width'], $params['height'], Imagick::FILTER_LANCZOS, 1);
    }

    public function anotate($params) {
        $draw = new \ImagickDraw();
        $draw->setfillcolor('white');
        $draw->setFontSize(30);
        $lines = explode('%20', $params['text']);
        for($i = 0; $i < count($lines); $i++) {
            $this->annotateimage($draw, 10, 10 + ($i * 30), 0, $lines[$i]);
        }
    }

}
