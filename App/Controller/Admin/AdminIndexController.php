<?php

class AdminRouter
{

    public $url;
    public $referrer;

    public function __construct() {

        $url = $this->getUrl();
        $this->setReferrer($url);
    }
    private function getUrl() {
        $url = $_SERVER['REQUEST_URI'] ?? null;
        if ($url === "favicon.ico") exit;
        $this->url = $url;
        return $url;
    }

    private function setReferrer($url){
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $this->referrer = 'json';
        }
        else
        {
            $referrer = $_SERVER['HTTP_REFERER'] ?? null;

            if (empty($referrer)) {
                $this->referrer = "/Admin";
            }
            else
            {
                $this->referrer = $referrer;
            }
        }
    }

}