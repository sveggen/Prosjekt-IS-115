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

    public function getProfileImage($memberID) {
        $profileDirPath = "./assets/img/profile/" . $memberID . "/";
        $dir = opendir($profileDirPath);

        while (false !== ($file = readdir($dir))){
            $pathinfo = pathinfo($profileDirPath . $file);
            $filename = $pathinfo['filename'];
            $extension = $pathinfo['extension'];
        }
        closedir($dir);
        return $profileDirPath .$filename . "." .$extension;
    }

    public function uploadProfileImage($file, $memberID): bool {
        $profileDirPath = "./assets/img/profile/" . $memberID . "/";

        if ($file instanceof UploadedFile){
            $uploadedFile = $file;
            $filename = $uploadedFile->getClientOriginalName();

            //if($this->checkFileSize($file) && $this->checkExtension($file)) {

            $tempFile = $uploadedFile->move($profileDirPath, $filename);
            $profileImagePath = $profileDirPath . $memberID ."-profile." . $tempFile->getExtension();

            $tempFile->move($profileDirPath, $profileImagePath);


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
        $fileExtension = $uploadedFile->guessExtension();

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