<?php

// Home page
$app->get('/', "Alaska\Controller\HomeController::indexAction")
->bind('home');

// Detailed info about an article
$app->match('/article/{id}', "Alaska\Controller\HomeController::articleAction")
->bind('article');

// Login form
$app->get('/login', "Alaska\Controller\HomeController::loginAction")
->bind('login');

// Register form
$app->match('/register', "Alaska\Controller\HomeController::registerAction")
    ->bind('register');

// Admin zone
$app->get('/admin', "Alaska\Controller\AdminController::indexAction")
->bind('admin');

// Add a new article
$app->match('/admin/article/add', "Alaska\Controller\AdminController::addArticleAction")
->bind('admin_article_add');

// Edit an existing article
$app->match('/admin/article/{id}/edit', "Alaska\Controller\AdminController::editArticleAction")
->bind('admin_article_edit');

// Remove an article
$app->get('/admin/article/{id}/delete', "Alaska\Controller\AdminController::deleteArticleAction")
->bind('admin_article_delete');

// Edit an existing comment
$app->match('/admin/comment/{id}/edit', "Alaska\Controller\AdminController::editCommentAction")
->bind('admin_comment_edit');

// Remove a comment
$app->get('/admin/comment/{id}/delete', "Alaska\Controller\AdminController::deleteCommentAction")
->bind('admin_comment_delete');

// Add a user
$app->match('/admin/user/add', "Alaska\Controller\AdminController::addUserAction")
->bind('admin_user_add');

// Edit an existing user
$app->match('/admin/user/{id}/edit', "Alaska\Controller\AdminController::editUserAction")
->bind('admin_user_edit');

// Remove a user
$app->get('/admin/user/{id}/delete', "Alaska\Controller\AdminController::deleteUserAction")
->bind('admin_user_delete');

// Report a comment
$app->match('/comment/{idComment}/report', "Alaska\Controller\HomeController::commentReportAction")
    ->bind('report_comment');




