<?php


namespace App\helpers;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFile {

    const MB = 1048576;
    private $errorMessages = array();


    /**
     * Returns path to currently saved profile image.
     *
     * @param $memberID
     * @return string | false Path to profile image or false if none exist.
     */
    public function getProfileImage($memberID) {
        $profileDirPath = "./assets/img/profile/" . $memberID . "/";
        $placeholder = "./assets/img/profile/placeholder.png";
        $iterator = new \FilesystemIterator($profileDirPath);
        $notEmptyDir = $isDirEmpty = $iterator->valid();

        if (file_exists($profileDirPath) && $notEmptyDir) {
            $dir = opendir($profileDirPath);
            while (false !== ($file = readdir($dir))) {
                $pathInfo = pathinfo($profileDirPath . $file);
                $filename = $pathInfo['filename'];
                $extension = $pathInfo['extension'];
            }
            closedir($dir);
            $profileImage = $profileDirPath . $filename . "." . $extension;
            return $profileImage;
        }
        return false;
    }

    public function uploadProfileImage($file, $memberID): bool {
        $profileDirPath = "./assets/img/profile/" . $memberID . "/";

        if ($file instanceof UploadedFile) {
            $uploadedFile = $file;
            $filename = $uploadedFile->getClientOriginalName();

            if ($this->compareFileSizeToLimit($uploadedFile) && $this->checkExtension($uploadedFile)) {

                // deletes old profile image
                $currentProfileImage = $this->getProfileImage($memberID);
                $this->deleteOldImage($currentProfileImage);

                //moves temporary file to members directory
                $tempFile = $uploadedFile->move($profileDirPath, $filename);

                //defines path to members directory and filename
                $profileImagePath = $profileDirPath . $memberID . "-profile." . $tempFile->getExtension();

                //moves file to members directory and renames file
                $tempFile->move($profileDirPath, $profileImagePath);

                return true;
            }
        }
        return false;
    }

    /*
     * @param UploadedFile $uploadedFile File to check size of.
     * @return bool True if filesize is below limit, false if not.
     */
    private function compareFileSizeToLimit(UploadedFile $uploadedFile): bool {
        $maxFileSize = 2 * self::MB;
        $fileSize = $uploadedFile->getSize();

        if ($fileSize < $maxFileSize) {
            return true;
        } else {
            $sizeError = "Filesize exceeds limit";
            array_push($this->errorMessages, $sizeError);
            return false;
        }
    }

    /**
     * @return array Containing all the error messages.
     */
    public function getErrorMessages(): array {
        return $this->errorMessages;
    }

    /**
     * @param UploadedFile $uploadedFile File to check extension of.
     * @return bool True if extension is allowed, false if not.
     */
    private function checkExtension(UploadedFile $uploadedFile): bool {
        $validExtensions = array("jpg", "png", "jpeg");
        //guesses file extension based on MIME-type.
        $fileExtension = $uploadedFile->guessExtension();
        if (in_array($fileExtension, $validExtensions)) {
            return true;
        } else {
            $extensionError = "Invalid filetype";
            array_push($this->errorMessages, $extensionError);
            return false;
        }
    }

    private function deleteOldImage($filePath){
        if (file_exists($filePath)){
            unlink($filePath);
        }
    }
}