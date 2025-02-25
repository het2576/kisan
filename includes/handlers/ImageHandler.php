<?php
class ImageHandler {
    public function processImages($files) {
        $processed = [];
        foreach ($files['tmp_name'] as $key => $tmp_name) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $processed[] = $this->processImage($files['tmp_name'][$key], $files['name'][$key]);
            }
        }
        return $processed;
    }

    private function processImage($tmp_name, $filename) {
        $upload_dir = 'uploads/auction_items/';
        $new_filename = uniqid() . '_' . $filename;
        move_uploaded_file($tmp_name, $upload_dir . $new_filename);
        return $new_filename;
    }
} 