<?php

class Image {
    private string $imagePath;
    private string $cachePath = IMG . "Cache" . DIRECTORY_SEPARATOR;
    private int $quality = 90;
    public string $imageType;

    public function __construct($imagePath) {
        $this->imagePath = $imagePath;
        //$cachePath yoksa oluşturalım
        if (!file_exists($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    private function createUnsupportedImage($width, $height) {
        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);
        imagestring($image, 1, 5, 5, 'Unsupported image type', $textColor);
        return $image;
    }
    public function createImageNotFound($width, $height) {
        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);
        imagestring($image, 1, 5, 5, 'Image not found', $textColor);
        return $image;
    }
    public function resize($width = null, $height = null): string {
        $imageType = exif_imagetype($this->imagePath);
        $this->imageType = $imageType;

        if (!$width && !$height) {
            return $this->imagePath;
        }

        list($originalWidth, $originalHeight) = getimagesize($this->imagePath);

        // Sıfıra bölme hatasını önlemek için kontrol
        if ($originalWidth == 0 || $originalHeight == 0) {
            throw new Exception("Image dimensions cannot be zero.");
        }

        $createNewImage = false;
        if ($width && !$height) {
            $newHeight = ($width / $originalWidth) * $originalHeight;
            $newWidth = $width;
        } elseif ($height && !$width) {
            $newWidth = ($height / $originalHeight) * $originalWidth;
            $newHeight = $height;
        } else {
            $originalRatio = $originalWidth / $originalHeight;
            $targetRatio = $width / $height;

            if ($originalRatio > $targetRatio) {
                $newWidth = $width;
                $newHeight = $width / $originalRatio;
                $createNewImage = true;
            } else {
                $newHeight = $height;
                $newWidth = $height * $originalRatio;
                $createNewImage = true;
            }
        }

        $extension = pathinfo($this->imagePath, PATHINFO_EXTENSION);
        if ($createNewImage) {
            $cacheFile = $this->cachePath . md5($this->imagePath . $width . $height . $this->quality) . '.' . $extension;
        } else {
            $cacheFile = $this->cachePath . md5($this->imagePath . $newWidth . $newHeight . $this->quality) . '.' . $extension;
        }
        if (file_exists($cacheFile)) {
            return $cacheFile;
        }

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($this->imagePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($this->imagePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($this->imagePath);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                break;
            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp($this->imagePath);
                break;
            default:
                $image = $this->createUnsupportedImage($newWidth, $newHeight);
        }

        $resizedImage = imagescale($image, $newWidth, $newHeight);
        if ($createNewImage) {
            $background = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($background, 255, 255, 255);
            imagefill($background, 0, 0, $white);

            $dstX = ($width - $newWidth) / 2;
            $dstY = ($height - $newHeight) / 2;
            imagecopyresampled($background, $resizedImage, $dstX, $dstY, 0, 0, $newWidth, $newHeight, $newWidth, $newHeight);

            $resizedImage = $background;
        }

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($resizedImage, $cacheFile, $this->quality);
                break;
            case IMAGETYPE_GIF:
                imagegif($resizedImage, $cacheFile);
                break;
            case IMAGETYPE_PNG:
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                imagepng($resizedImage, $cacheFile);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($resizedImage, $cacheFile);
                break;
        }

        return $cacheFile;
    }


    public function crop($startX, $startY, $width, $height):string
    {
        $imageType = exif_imagetype($this->imagePath);
        $this->imageType = $imageType;
        // Check if image already exists in cache
        $extension = pathinfo($this->imagePath, PATHINFO_EXTENSION);
        $cacheFile = $this->cachePath . md5($this->imagePath . $startX . $startY . $width . $height . $this->quality) . '.' . $extension;
        if (file_exists($cacheFile)) {
            return $cacheFile;
        }

        // Create image

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($this->imagePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($this->imagePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($this->imagePath);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                break;
            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp($this->imagePath);
                break;
            default:
                $image = $this->createUnsupportedImage($width, $height);
        }

        // Crop image
        $croppedImage = imagecrop($image, ['x' => $startX, 'y' => $startY, 'width' => $width, 'height' => $height]);

        // Save image to cache
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($croppedImage, $cacheFile, $this->quality);
                break;
            case IMAGETYPE_GIF:
                imagegif($croppedImage, $cacheFile);
                break;
            case IMAGETYPE_PNG:
                imagealphablending($croppedImage, false);
                imagesavealpha($croppedImage, true);
                imagepng($croppedImage, $cacheFile);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($croppedImage, $cacheFile);
                break;
        }

        return $cacheFile;
    }

    public function setQuality($quality) {
        $this->quality = $quality;
    }
}