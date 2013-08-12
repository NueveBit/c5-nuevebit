<?php
class ImageUtil {

    // TODO: especificar formato de imagen, asumimos JPEG!
    public static function scale($srcFile, $destWidth, $destHeight = false, $transparent = true) {
        $destroyImage = false;

        if (is_string($srcFile)) {
            $destroyImage = true;
            $srcImage = imagecreatefromjpeg($srcFile);
            list($srcWidth, $srcHeight) = getimagesize($srcFile);
        } else {
            $srcImage = $srcFile;
            $srcWidth = imagesx($srcImage);
            $srcHeight = imagesy($srcImage);
        }
        
        
        if ($destWidth && $destHeight === false) {
            $scaleFactor = $destWidth;
            
            $destWidth = $srcWidth * $scaleFactor;
            $destHeight = $srcHeight * $scaleFactor;
        }

        // creamos la nueva imagen
        $destImage = imagecreatetruecolor($destWidth, $destHeight);

        if ($transparent) {
            imagealphablending($destImage, false);
            $col_transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);

            imagefill($destImage, 0, 0, $col_transparent);  // set the transparent colour as the background.
            imagecolortransparent ($destImage, $col_transparent); // actually make it transparent

            imagesavealpha($destImage, true);
        }

        imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);
        
        if ($destroyImage) {
            imagedestroy($srcImage);
        }
        
        return $destImage;
    }

    // TODO: especificar formato de imagen, asumimos PNG para la marca de agua!
    public static function mergeWatermark($srcImage, $watermarkFile) {
        $destroyImage = false;
        $destroyWatermark = false;
        
        if (is_string($srcImage)) {
            $destroyImage = true;
            $srcImage = imagecreatefromjpeg($srcImage);
        } 
        
        $srcWidth = imagesx($srcImage);
        $srcHeight = imagesy($srcImage);
//        list($width, $height) = getimagesize($watermarkFile);

        if (is_string($watermarkFile)) {
            $destroyWatermark = true;
            $watermark = imagecreatefrompng($watermarkFile);
        } else {
            $watermark = $watermarkFile;
        }
        
        $width = imagesx($watermark);
        $height = imagesy($watermark);

        $destX = $srcWidth - $width;
        $destY = $srcHeight - $height;

        imagecopy($srcImage, $watermark, $destX, $destY, 0, 0, $width, $height);
        imagesavealpha($srcImage, true);

        if ($destroyWatermark) {
            imagedestroy($watermark);
        }
        
        return $srcImage;
    }


}

?>