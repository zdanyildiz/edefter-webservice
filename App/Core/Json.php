<?php

class Json {

    protected string $rootDir;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    protected function getPath($filename): string
    {
        $filePath = $this->rootDir . implode('/', $filename);

        $dir = dirname($filePath);
        //die("$dir - $filePath");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        return $filePath . '.json';
    }

    public function createJson(array $filename, $data): void
    {
        $filePath = $this->getPath($filename);
        //die($filePath);
        $handle = fopen($filePath, 'w');
        fwrite($handle, json_encode($data));
        fclose($handle);
    }

    public function readJson(array $filename)
    {
        $filePath = $this->getPath($filename);
        //die($filePath);
        if(file_exists($filePath))
        {
            try {
                $data = file_get_contents($filePath);
                return json_decode($data, true);
            } catch (Exception) {
                // handle error
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    public function deleteJson(array $filename): void
    {
        $filePath = $this->getPath($filename);
        if(file_exists($filePath))
        {
            unlink($filePath);
        }
    }
}