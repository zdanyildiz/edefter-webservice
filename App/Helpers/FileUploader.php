<?php
class FileUploader {
    private $targetDirectory;
    private $maxFile =20;
    private $maxFileSize;
    private $allowedFileTypes;

    public function __construct($targetDirectory, $maxFile, $maxFileSize, $allowedFileTypes) {
        $this->targetDirectory = $targetDirectory;
        $this->maxFile = $maxFile;
        $this->maxFileSize = $maxFileSize;
        $this->allowedFileTypes = $allowedFileTypes;
    }

    public function upload($file) {
        $fileName = basename($file["name"]);
        $targetFilePath = $this->targetDirectory . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (in_array($fileType, $this->allowedFileTypes)) {
            if ($file["size"] <= $this->maxFileSize) {
                if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                    return "File uploaded successfully.";
                } else {
                    return "There was an error uploading your file.";
                }
            } else {
                return "Your file is too large.";
            }
        } else {
            return "Only " . implode(", ", $this->allowedFileTypes) . " files are allowed.";
        }
    }
}