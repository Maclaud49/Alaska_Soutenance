<?php

// Home page
$app->get('/', "Alaska\Controller\HomeController::indexAction")
->bind('home');

// Credits page
$app->get('/mentions-legales', "Alaska\Controller\HomeController::mentionsLegalesAction")
    ->bind('mentions_legales');

// Home page by pages
$app->get('/page/{pageId}', "Alaska\Controller\HomeController::indexPageAction")
    ->bind('index_page');

// Detailed info about an article
$app->match('/article/{artChap}', "Alaska\Controller\HomeController::articleAction")
->bind('article');

// Login form
$app->get('/connexion', "Alaska\Controller\HomeController::loginAction")
->bind('login');

// Register form
$app->match('/enregistrement', "Alaska\Controller\HomeController::registerAction")
    ->bind('register');

// Admin zone
$app->get('/admin', "Alaska\Controller\AdminController::indexAction")
->bind('admin');

// Add a new article
$app->match('/admin/article/ajouter', "Alaska\Controller\AdminController::addArticleAction")
->bind('admin_article_add');

// Edit an existing article
$app->match('/admin/article/{artChap}/editer', "Alaska\Controller\AdminController::editArticleAction")
->bind('admin_article_edit');

// Remove an article
$app->get('/admin/article/{id}/supprimer', "Alaska\Controller\AdminController::deleteArticleAction")
->bind('admin_article_delete');

// Edit an existing comment
$app->match('/admin/commentaire/{id}/editer', "Alaska\Controller\AdminController::editCommentAction")
->bind('admin_comment_edit');

// Remove a comment
$app->get('/admin/commentaire/{id}/supprimer', "Alaska\Controller\AdminController::deleteCommentAction")
->bind('admin_comment_delete');

// Add a user
$app->match('/admin/utilisateur/ajouter', "Alaska\Controller\AdminController::addUserAction")
->bind('admin_user_add');

// Edit an existing user
$app->match('/admin/utilisateur/{id}/editer', "Alaska\Controller\AdminController::editUserAction")
->bind('admin_user_edit');

// Remove a user
$app->get('/admin/utilisateur/{id}/supprimer', "Alaska\Controller\AdminController::deleteUserAction")
->bind('admin_user_delete');

// Report a comment
$app->match('/comment/{idComment}/notifier', "Alaska\Controller\HomeController::commentReportAction")
    ->bind('report_comment');

// Next article
$app->match('/article/{artChap}/suivant', "Alaska\Controller\HomeController::nextArticleAction")
    ->bind('next_article');

//Forgot password
$app->match('/connexion/oublie-mot-de-passe', "Alaska\Controller\HomeController::forgotPasswordAction")
    ->bind('forgot_password');
//Change password
$app->match('/connexion/changer-mot-de-passe', "Alaska\Controller\AdminController::changePasswordAction")
    ->bind('change_password');




