<?php
class Seo {

    public $baslik;
    public $aciklama;
    public $kelime;
    public $link;
    public $resim;
    public $dil;

    public function __construct($baslik, $aciklama, $kelime, $link, $resim,$dil) {
        $this->baslik = $baslik;
        $this->aciklama = $aciklama;
        $this->kelime = $kelime;
        $this->link = $link;
        $this->resim = $resim;
        $this->dil = $dil;
    }
}