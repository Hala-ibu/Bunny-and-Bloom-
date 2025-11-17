<?php
require_once 'BaseDao.php';

class ReviewDao extends BaseDao {
    public function __construct() {
        parent::__construct("reviews");
    }
    

    public function getAllReviews() {
        $stmt = $this->connection->prepare("SELECT * FROM reviews ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByProductId($product_id) {
        $stmt = $this->connection->prepare("SELECT * FROM reviews WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
        public function getReviewByUserAndProduct($user_id, $product_id) {
        $stmt = $this->connection->prepare("SELECT * FROM reviews 
            WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function deleteReview($review_id) {
        $stmt = $this->connection->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $review_id);
        return $stmt->execute();
    }
}

?>