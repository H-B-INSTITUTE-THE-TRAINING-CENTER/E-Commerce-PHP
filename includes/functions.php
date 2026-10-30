<?php 
    function calculateTotal(float $price, float $quantity): float {
        return $price * $quantity;
    }

    function calculateDiscountedPrice(float $price, float $discountPercent): float{
        $discount = ($price * $discountPercent) / 100;
        return $price - $discount;
    }

    function isProductAvailable(int $stock): bool{
        return $stock > 0;
    }

    function formatPrice(float $price){
        return "₹" . number_format($price, 2);
    }

    function uploadProductImage(array $file): array{
        $result = [
            'filename' => null,
            'error' => null,
            'provided' => null
        ];
        if(!isset($file['error'])){
            $result['error'] = "Invalid upload request.";
            return $result;
        }

        if($file['error'] === UPLOAD_ERR_NO_FILE){
            return $result;
        }

        $result['provided'] = true;

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $result['error'] = "Image upload failed.";
            return $result;
        }

        if (!isset($file['tmp_name'], $file['size']) || !is_uploaded_file($file['tmp_name'])) {
            $result['error'] = "Invalid uploaded file";
            return $result;
        }

        // Maximum Upload size: 2MB
        if ($file['size'] <= 0 || $file['size'] > 2 * 1024 * 1024) {
            $result['error'] = "Image must be smaller than 2 MB.";
            return $result;
        }

        // Inspect actual file, not a submitted file
        $imageInfo = @getimagesize($file['tmp_name']);

        if ($imageInfo === false) {
            $result['error'] = "The uploaded file is not a valid image.";
            return $result;
        }

        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($file['tmp_name']);

        $allowed_type = [
            'image/jpeg' => "jpg",
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowed_type[$mimeType]) || ($imageInfo['mime'] ?? '') !== $mimeType ) {
            $result['error'] = "Only JPEG, PNG and WEBP images are allowed.";
            return $result;
        }

        $uploadDirectory = __DIR__.'/../uploads/products';

        if (!is_dir($uploadDirectory) || !is_writable($uploadDirectory)) {
            $result['error'] = "The product upload directory is not writable.";
            return $result;
        }

        //Never trust or reuse original uploaded file name.
        try {
            $filename = bin2hex(random_bytes(16)).'.'.$allowed_type[$mimeType];
        } catch (Throwable $e) {
            $result['error'] = "Unable to generate a safe file name.";
            return $result;
        }

        $destination = $uploadDirectory .'/'.$filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $result['error'] = "Unable to save the uploaded image.";
            return $result;
        }

        $result['filename'] = $filename;
        return $result;
    }
?>