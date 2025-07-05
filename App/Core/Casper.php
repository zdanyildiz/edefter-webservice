<?php

class Casper
{
    private Config $config;
    private array $siteConfig=[];
    private array $visitor = [];
    private ?Menu $allMenu = null;
    private string $jsContents = "";

    private string $cssContents = "";
    private string $schema = "";
    private array $bannerCache = [];

    public function getConfig(): ?Config
    {
        return $this->config;
    }

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function getSiteConfig(): array
    {
        return $this->siteConfig;
    }

    public function setSiteConfig(array $siteConfig): void
    {
        $this->siteConfig = $siteConfig;
    }

    public function getVisitor(): array
    {
        return $this->visitor;
    }

    public function setVisitor(array $visitor): void
    {
        $this->visitor = $visitor;
    }

    public function getAllMenu(): ?Menu
    {
        return $this->allMenu;
    }

    public function setAllMenu(Menu $allMenu): void
    {
        $this->allMenu = $allMenu;
    }

    public function getJsContents(): string
    {
        return $this->jsContents;
    }

    public function setJsContents(string $jsContents): void
    {
        $this->jsContents = $jsContents;
    }

    public function getCssContents(): string
    {
        return $this->cssContents;
    }

    public function setCssContents(string $cssContents): void
    {
        $this->cssContents = $cssContents;
    }

    public function getSchema(): string
    {
        return $this->schema;
    }    public function setSchema(string $schema): void
    {
        $this->schema = $schema;
    }

    public function getBannerCache(): array
    {
        return $this->bannerCache;
    }

    public function setBannerCache(array $bannerCache): void
    {
        $this->bannerCache = $bannerCache;
    }

    public function getBannerCacheByKey(string $key): ?array
    {
        return $this->bannerCache[$key] ?? null;
    }

    public function setBannerCacheByKey(string $key, array $data): void
    {
        $this->bannerCache[$key] = $data;
    }

    public function clearBannerCache(): void
    {
        Log::write("Casper Banner Cache Temizlendi");
        $this->bannerCache = [];
    }

}