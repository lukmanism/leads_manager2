<?php
class AppError extends ErrorHandler {
    function error404($params) {
        // redirect to homepage
        $this->controller->redirect('/');
    }
} 