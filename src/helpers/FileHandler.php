<?php


namespace App\helpers;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Handles all operations in connection to file uploads,
 * downloads, directory traversals, mostly for image files.
 *
 * Class FileHandler
 * @package App\helpers
 */
class FileHandler {

    const MB = 1048576;
    private $errorMessages = array();

    /**
     * Uploads a new profile image linked to a member.
     *
     * @param $file File | UploadedFile File containing profile image
     * @param $memberID int The members ID
     * @return bool True if image was uploaded, false if not.
     */
    public function uploadProfileImage($file, int $memberID): bool {
        $profileDirPath = "./assets/img/profile/" . $memberID . "/";

        if ($file instanceof UploadedFile) {
            $uploadedFile = $file;
            $filename = $uploadedFile->getClientOriginalName();

            if ($this->compareFileSizeToLimit($uploadedFile) && $this->checkExtension($uploadedFile)) {

                // if users image directory exists
                if (file_exists($profileDirPath)) {
                    // deletes old profile image if there is any
                    $currentProfileImage = $this->getProfileImage($memberID);
                    $this->deleteOldImage($currentProfileImage);
                }

                //moves temporary file to members directory
                $tempFile = $uploadedFile->move($profileDirPath, $filename);

                // defines path to members directory and assigns the file a unique id as filename to
                // force update the image cache on website
                $profileImagePath = $profileDirPath . uniqid() . "." . $tempFile->getExtension();

                //moves file to the member's directory and renames file
                $tempFile->move($profileDirPath, $profileImagePath);

                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the file supplied has a
     * file size beneath the preset limit.
     *
     * @param UploadedFile $uploadedFile
     * @return bool
     */
    private function compareFileSizeToLimit(UploadedFile $uploadedFile): bool {
        $maxFileSizeInBytes = 2 * self::MB;
        $fileSize = $uploadedFile->getSize();

        if ($fileSize < $maxFileSizeInBytes) {
            return true;
        } else {
            $sizeError = "Filesize exceeds limit";
            array_push($this->errorMessages, $sizeError);
            return false;
        }
    }

    /**
     * Checks if the file supplied has an extension
     * which is on the site's list of accepted extensions.
     *
     * @param UploadedFile $uploadedFile Image file
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

    /**
     * Returns path to currently saved profile image.
     *
     * @param $memberID
     * @return string | false Path to profile image or false if none exist.
     */
    public function getProfileImage($memberID) {
        $profileDirPath = "./assets/img/profile/" . $memberID . "/";
        // checks if directory exists and returns true/false
        if (file_exists($profileDirPath)){
            $iterator = new \FilesystemIterator($profileDirPath);
            $notEmptyDir = $iterator->valid();
        }

        // if directory exists and is not empty
        if (file_exists($profileDirPath) && $notEmptyDir == true) {
            $dir = opendir($profileDirPath);
            // iterate through files in dir
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

    /**
     * Deletes an old image in a given path
     * if the image exists.
     *
     * @param $filePath string Path to the image-file.
     */
    private function deleteOldImage(string $filePath) {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * @return array List of all error messages.
     */
    public function getErrorMessages(): array {
        return $this->errorMessages;
    }
}