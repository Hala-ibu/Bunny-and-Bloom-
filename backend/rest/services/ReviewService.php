<?php
require_once 'BaseService.php';
require_once 'ReviewDao.php'; 

class ReviewService extends BaseService {

    public function __construct() {
        $dao = new ReviewDao(); 
        parent::__construct($dao);
    }


    public function submitReview($data) {
        $rating = $data['rating'] ?? 0;
        if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
            throw new Exception("Rating must be an integer between 1 and 5.");
        }
        
        if ($this->dao->getReviewByUserAndProduct($data['user_id'], $data['product_id'])) {
            throw new Exception("You have already submitted a review for this product.");
        }
        
        $comment = strtolower($data['comment'] ?? '');
        $profanity_list = ['badword', 'swear', 'inappropriate']; 
        foreach ($profanity_list as $word) {
            if (strpos($comment, $word) !== false) {
                throw new Exception("Review contains restricted language and cannot be submitted.");
            }
        }
        
        $data['date'] = date('Y-m-d');

        return $this->create($data);
    }
    
    public function getReviewsByProduct($product_id) {
        return $this->dao->getByProductId($product_id);
    }

    public function getAllReviews() {
        return $this->dao->getAllReviews();
    }

    public function deleteReview($review_id) {
        return $this->dao->deleteReview($review_id);
    }
}

?>