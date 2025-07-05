<?php
class Popup {

    private $type;
    private $message;
    private $position;
    private $width;
    private $height;
    private $closeButton;
    private $autoClose;
    private $animation;
    private $seconds;

    public function __construct($type, $message, $position, $width, $height, $closeButton = true, $autoClose = false, $animation = true,$seconds = 3.5) {
        $this->type = $type;
        $this->message = $message;
        $this->position = $position;
        $this->width = $width;
        $this->height = $height;
        $this->closeButton = $closeButton;
        $this->autoClose = $autoClose;
        $this->animation = $animation;
        $this->seconds = $seconds;
    }

    public function show() {
        $title = $this->type;
        $active = $this->autoClose ? 'close' : '';
        $status = $this->type;
        if ($status == 'success') {
            $status = 'popup-success';
        } elseif ($status == 'warning') {
            $status = 'popup-warning';
        } elseif ($status == 'error') {
            $status = 'popup-error';
        }
        else{
            $status = 'popup-info';
        }
        $html = '<div class="popup">';
        $html .='<div class="popup-header '.$status.'">';
        $html .= '<h2>' . $title . '</h2>';
        $html .= '</div>';
        $html .= '<div class="popup-content">';
        $html .= '<p>' . $this->message . '</p>';
        if ($this->closeButton) {
            $html .= '<button class="popup-close"> X </button>';
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    public function popupCss()
    {
        $position = $this->position;
        switch ($position){
            case "top-left":
                $position = "top: 150px; left: 20px;";
                break;
            case "top-right":
                $position = "top: 150px; right: 20px;";
                break;
            case "bottom-left":
                $position = "bottom: 150px; left: 20px;";
                break;
            case "bottom-right":
                $position = "bottom: 150px; right: 20px;";
                break;
            case "center":
                $position = "top: 50%; left: 50%; transform: translate(-50%, -50%);";
                break;
            default:
                $position = "top: 150px; right: 20px;";
                break;
        }
        $css = '.popup {
          position: fixed;
            '.$position.'
          width: '.$this->width.';
          height: '.$this->height.';
          background-color: var(--body-bg-color);
          border: var(--border);
          z-index: 9999;
        }';
        $css .= '.popup-header {margin: 0;padding: 10px 20px;font-size: 18px;}';
        if ($this->closeButton) {
            $css .= '.popup-close {background-color: #ddd;border: none;padding: 5px 10px;font-size: 16px;position: absolute;top: 5px;right: 10px;cursor: pointer;}';
        }

        $css .= '.popup-content {padding: 20px;}';
        if($this->type == 'success'){
            $css .= '.popup-success {background-color: #77dd77;color: #fff;}';
        }
        elseif ($this->type == 'warning'){
            $css .= '.popup-warning {background-color: #ffcc00;color: #fff;}';
        }
        elseif ($this->type == 'error'){
            $css .= '.popup-error {background-color: #ff6666;color: #fff;}';
        }
        else{
            $css .= '.popup-info {background-color: var(--accent-color);color: #fff;}';
        }

        if ($this->animation && !$this->autoClose) {
            // Sadece animasyon varsa
            $css .= '
                @keyframes scaleUp {from {transform: scale(0);}to {transform: scale(1);}}
                .popup {animation: scaleUp 0.5s forwards;}';
        } elseif ($this->animation && $this->autoClose) {
            // Hem animasyon hem de otomatik kapanma varsa
            $css .= '
                @keyframes scaleUp {from {transform: scale(0);}to {transform: scale(1);}}
                .popup {animation: scaleUp 0.5s forwards, fadeOut '.$this->seconds.'s forwards;}
                @keyframes fadeOut {0% {opacity: 1;}99% {opacity: 1;}100% {opacity: 0;display: none;}}';
        } elseif (!$this->animation && $this->autoClose) {
            // Sadece otomatik kapanma varsa
            $css .= '
                @keyframes fadeOut {0% {opacity: 1;}99% {opacity: 1;}100% {opacity: 0;display: none;}}
                .popup {animation: fadeOut '.$this->seconds.'s forwards;}';
        }

        $css .= '.popup span{position:absolute;left: 50%;top:50%;transform:translate(-50%, -50%);padding:20px;background: #fff;}';
        return $css;
    }
}
