<?php

class File
{
    var $fileName, $fileType, $fileSize, $tmpName;

    /**
     * File constructor.
     * @param $fileName
     * @param $tmpName
     * @param $fileSize
     * @param $fileType
     */
    public function __construct($fileName, $tmpName, $fileSize, $fileType)
    {
        $this->fileName = $fileName;
        $this->tmpName = $tmpName;
        $this->fileSize = $fileSize;
        $this->fileType = $fileType;
    }

    public function getFileType()
    {
        return $this->fileType;
    }

    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    public function getFileSize()
    {
        return $this->fileSize;
    }


    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    public function getTmpName()
    {
        return $this->tmpName;
    }

    public function setTmpName($tmpName)
    {
        $this->tmpName = $tmpName;
    }

    public function readFile()
    {
        $fp = fopen($this->tmpName, 'r');
        $content = fread($fp, filesize($this->tmpName));
        $content = addslashes($content);
        fclose($fp);
    }
    public function moveFile($path)
    {
        if (!get_magic_quotes_gpc()) {
            $this->fileName = addslashes($this->fileName);
        }
        if (!move_uploaded_file($this->tmpName, $path)) {
            die("Không thể di chuyển file vào " . $path);
        }

    }

    public static function createDirectory($path)
    {
        if (!mkdir($path, 0777, true)) {
            die('Failed to create folders...');
        }
    }
    public static function removeDirectoryAllFiles($directory)
    {
        foreach(glob("{$directory}/*") as $file)
        {
            if(is_dir($file)) {
                File::removeDirectoryAllFiles($file);
            } else {
                unlink($file);
            }
        }
        rmdir($directory);
    }
    public function isImageType()
    {
        $fileType= $this->fileType;
        if ($fileType == "image/gif" || $fileType == "image/jpeg" || $fileType == "image/jpg" || $fileType == "image/png"){
            return true;
        }
        return false;
    }
    /**
     * Returns an string clean of UTF8 characters. It will convert them to a similar ASCII character
     * www.unexpectedit.com
     */
   public static function  utf8convert($str) {
        if(!$str) return false;
        $utf8 = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd'=>'đ|Đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i'=>'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach($utf8 as $ascii=>$uni) $str = preg_replace("/($uni)/i",$ascii,$str);
        return $str;
    }
}