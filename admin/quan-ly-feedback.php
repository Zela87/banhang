<?php
require_once __DIR__ . '/../models_admin/feedback.php';

class FeedbackController
{
    private $fbModel;

    public function __construct($db)
    {
        $this->fbModel = new Feedback($db);
    }
    public function index()
    {
        $feedbacks = $this->fbModel->getAll();
        return $feedbacks;
    }
    public function fillterFeedBack($rating)
    {
        $feedbacks = $this->fbModel->fillterFeedbacks($rating);
        return $feedbacks;
    }
}
?>