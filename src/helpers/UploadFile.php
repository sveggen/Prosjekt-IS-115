<?php


namespace App\helpers;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFile {

    const MB = 1048576;
    private $request;
    private $errorMessages = array();

    /**
     * UploadFile constructor.
     */
    public function __construct() {
        $this->request = Request::createFromGlobals();
    }

    public function uploadFile($file) {
        $dir_path = "./assets/img/profile/";

        if ($file instanceof UploadedFile){
            $filename = $file->getClientOriginalName();
            $newFilePath = $dir_path . $filename;
            if($this->checkFileSize($file) && $this->checkExtension($file)) {
                move_uploaded_file($file, $newFilePath);
            }
            return true;
        } else {
            return false;
        }
    }

    private function checkFileSize(UploadedFile $uploadedFile){
        $fileSize = $uploadedFile->getSize();

        if ($fileSize < 2 * self::MB){
            $sizeError = "Filesize exceeds limit";
            array_push($this->errorMessages, $sizeError);
            return false;
        } else {
            return true;
        }
    }

    private function checkExtension(UploadedFile $uploadedFile){
        $validExtensions = array("jpg", "png", "jpeg");
        $fileExtension = $uploadedFile->getExtension();

        if (!in_array($fileExtension, $validExtensions)) {
            $extensionError = "Invalid filetype";
            array_push($this->errorMessages, $extensionError);
            return false;
        } else {
            return true;
        }
    }

    public function getErrorMessages(){
        return $this->errorMessages;
    }
}