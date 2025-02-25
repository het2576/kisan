<?php
class AuctionValidator {
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }

    public function validate() {
        $errors = [];
        
        if (empty($this->data['title'])) {
            $errors[] = "Title is required";
        }
        
        if (empty($this->data['description'])) {
            $errors[] = "Description is required";
        }
        
        if (!is_numeric($this->data['starting_price']) || $this->data['starting_price'] <= 0) {
            $errors[] = "Starting price must be greater than 0";
        }
        
        return $errors;
    }
} 