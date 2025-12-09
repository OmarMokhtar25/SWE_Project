<?php

class UploadHelper {
    
    public static function uploadFile($file, $directory, $allowedTypes = [], $maxSize = 2097152) {
        // Check for errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload error'];
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File too large. Maximum size: ' . ($maxSize / 1024 / 1024) . 'MB'];
        }
        
        // Check file type
        $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedTypes) && !in_array($fileType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $fileType;
        $uploadPath = $directory . '/' . $filename;
        
        // Create directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $uploadPath,
                'url' => BASE_URL . str_replace($_SERVER['DOCUMENT_ROOT'], '', $uploadPath)
            ];
        }
        
        return ['success' => false, 'error' => 'Failed to upload file'];
    }
    
    public static function uploadImage($file, $directory) {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        return self::uploadFile($file, $directory, $allowedTypes);
    }
    
    public static function uploadDocument($file, $directory) {
        $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
        return self::uploadFile($file, $directory, $allowedTypes);
    }
    
    public static function deleteFile($filePath) {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
    
    public static function resizeImage($filePath, $maxWidth, $maxHeight) {
        list($width, $height, $type) = getimagesize($filePath);
        
        // Calculate new dimensions
        $ratio = $width / $height;
        if ($maxWidth / $maxHeight > $ratio) {
            $newWidth = $maxHeight * $ratio;
            $newHeight = $maxHeight;
        } else {
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $ratio;
        }
        
        // Create new image
        $image = imagecreatetruecolor($newWidth, $newHeight);
        
        // Load source image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filePath);
                break;
            default:
                return false;
        }
        
        // Resize
        imagecopyresampled($image, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save resized image
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($image, $filePath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($image, $filePath, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($image, $filePath);
                break;
        }
        
        // Free memory
        imagedestroy($image);
        imagedestroy($source);
        
        return true;
    }
}