<?php

namespace Alaska\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Alaska\Domain\Article;
use Alaska\Domain\User;
use Alaska\Form\Type\ArticleType;
use Alaska\Form\Type\CommentType;
use Alaska\Form\Type\UserType;
use Alaska\Form\Type\ChangePasswordType;


class AdminController {

    /**
     * Admin home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
        $articles = $app['manager.article']->findAll();
        $comments = $app['manager.comment']->findAll();
        $articlesVisible = $app['manager.article']->findAllVisible();
        $users = $app['manager.user']->findAll();
        $commentsReported =$app['manager.comment']->findAllReportedComments();
        return $app['twig']->render('admin.html.twig', array(
            'articles' => $articles,
            'comments' => $comments,
            'users' => $users,
            'commentsReported' =>$commentsReported,
            'articlesVisible' => $articlesVisible));
    }

    /**
     * Add article controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function addArticleAction(Request $request, Application $app) {
        $article = new Article();
        $articles = $app['manager.article']->findAll();
        $articlesVisible = $app['manager.article']->findAllVisible();
        $articleForm = $app['form.factory']->create(ArticleType::class, $article);
        $articleForm->handleRequest($request);
        //if form is submitted and accepted
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            //if the chapter number is not used
            if ($app['manager.article']->checkChapter($article->getChapter())) {
                $article->setViewsNb(0);
                $article->setCommentsNb(0);
                $article->setLastUpdatedDate(date("Y-m-d H:i:s"));
                $app['manager.article']->save($article);
                $app['session']->getFlashBag()->add('success', 'L\'article a été créé.');
                $app->get('/admin', "Alaska\Controller\AdminController::indexAction");
                return $app->redirect($app['url_generator']->generate('admin'));
            } else {
                $app['session']->getFlashBag()->add('error', 'Ce numéro de chapitre est déjà assigné.');
            }
        }
        return $app['twig']->render('article_form.html.twig', array(
            'title' => 'Nouvel article',
            'articleForm' => $articleForm->createView(),
            'articles' => $articles,
            'articlesVisible' => $articlesVisible));

    }

    /**
     * Edit article controller.
     *
     * @param integer $artChap Article chapter
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editArticleAction($artChap, Request $request, Application $app)
    {

        $article = $app['manager.article']->find($artChap);
        $articles = $app['manager.article']->findAll();
        $articlesVisible = $app['manager.article']->findAllVisible();
        $chapter=$article->getChapter();
        $articleForm = $app['form.factory']->create(ArticleType::class, $article);
        $articleForm->handleRequest($request);
        //if form is submitted and accepted
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            //if chapter changed
            if ($article->getChapter() != $chapter) {
                //if the chapter number is not used
                if ($app['manager.article']->checkChapter($article->getChapter())) {
                    $article->setLastUpdatedDate(date("Y-m-d H:i:s"));
                    $app['manager.article']->save($article);
                    $app['session']->getFlashBag()->add('success', 'L\'article a été modifié.');
                    return $app->redirect($app['url_generator']->generate('admin'));
                } else {
                    $app['session']->getFlashBag()->add('error', 'Ce numéro de chapitre est déjà assigné.');
                }
            }
            else{
                    $app['manager.article']->save($article);
                    $app['session']->getFlashBag()->add('success', 'L\'article a été modifié.');
                    $app->get('/admin', "Alaska\Controller\AdminController::indexAction");
                    return $app->redirect($app['url_generator']->generate('admin'));
                }
            }
        return $app['twig']->render('article_form.html.twig', array(
            'title' => 'Editer l\'article',
            'articleForm' => $articleForm->createView(),
            'articles' => $articles,
            'articlesVisible' => $articlesVisible));
    }

    /**
     * Delete article controller.
     *
     * @param integer $id Article id
     * @param Application $app Silex application
     */
    public function deleteArticleAction($id, Application $app) {
        // Delete all associated comments
        $app['manager.comment']->deleteAllByArticle($id);
        // Delete the article
        $app['manager.article']->delete($id);
        $app['session']->getFlashBag()->add('success', 'L\'article a été supprimé.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
     * Edit comment controller.
     *
     * @param integer $id Comment id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editCommentAction($id, Request $request, Application $app) {
        $comment = $app['manager.comment']->find($id);
        $articlesVisible = $app['manager.article']->findAllVisible();
        $commentForm = $app['form.factory']->create(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        //if form is submitted and accepted
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $app['manager.comment']->save($comment);
            $app['session']->getFlashBag()->add('success', 'Le commentaire a été modifié.');
            return $app->redirect($app['url_generator']->generate('admin'));
        }
        return $app['twig']->render('comment_form.html.twig', array(
            'title' => 'Mofidier le commentaire',
            'commentForm' => $commentForm->createView(),
            'articlesVisible' => $articlesVisible));
    }

    /**
     * Delete comment controller.
     *
     * @param integer $id Comment id
     * @param Application $app Silex application
     */
    public function deleteCommentAction($id, Application $app) {
        $app['manager.comment']->delete($id);
        $app['session']->getFlashBag()->add('success', 'Le commentaire a été supprimé.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
     * Add user controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function addUserAction(Request $request, Application $app) {
        $user = new User();
        $articlesVisible = $app['manager.article']->findAllVisible();
        $userForm = $app['form.factory']->create(UserType::class, $user);
        $userForm->handleRequest($request);
        //if form is submitted and accepted
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user1 = $app['manager.user']->loadUserByUsername($user->getUsername());
            $user2 = $app['manager.user']->loadUserByEmail($user->getEmail());
            //if username exist
            if ($user1 != false) {
                $app['session']->getFlashBag()->add('warning', 'Ce pseudo est déjà utilisé. Merci d\'en choisir un autre.');
            }
            //if email exist
            else if($user2 != false){
                $app['session']->getFlashBag()->add('warning', 'Cette adresse email est déjà utilisée. Merci d\'en choisir une autre.');
            }
            //The email and username are confirmed not used, creation of user
            else {
            // generate a random salt value
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);
            $plainPassword = $user->getPassword();
            // find the default encoder
            $encoder = $app['security.encoder.bcrypt'];
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password);
            $user->setLastViewArt(1);
            $app['manager.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'L\'enregistrement s\'est bien déroulé.');
            return $app->redirect($app['url_generator']->generate('admin'));
        }}
        return $app['twig']->render('user_form.html.twig', array(
            'title' => 'Nouvel utilisateur',
            'userForm' => $userForm->createView(),
            'articlesVisible' => $articlesVisible));
    }

    /**
     * Edit user controller.
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editUserAction($id, Request $request, Application $app) {
        $user = $app['manager.user']->find($id);
        $articlesVisible = $app['manager.article']->findAllVisible();
        $userForm = $app['form.factory']->create(UserType::class, $user);
        $userForm->handleRequest($request);
        //if form is submitted and accepted
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $plainPassword = $user->getPassword();
            // find the encoder for the user
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password); 
            $app['manager.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'L\'utilisateur a bien été modifié.');
            return $app->redirect($app['url_generator']->generate('admin'));
        }
        return $app['twig']->render('user_form.html.twig', array(
            'title' => 'Edit user',
            'userForm' => $userForm->createView(),
            'articlesVisible' => $articlesVisible));
    }

    /**
     * Delete user controller.
     *
     * @param integer $id User id
     * @param Application $app Silex application
     */
    public function deleteUserAction($id, Application $app) {
        // Delete all associated comments
        $app['manager.comment']->deleteAllByUser($id);
        // Delete the user
        $app['manager.user']->delete($id);
        $app['session']->getFlashBag()->add('success', 'L\'utilisateur a été supprimé.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
     * Change password controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function changePasswordAction(Request $request, Application $app)
    {
        $articlesVisible = $app['manager.article']->findAllVisible();
        $user = $app['user'];
        $changePasswordForm = $app['form.factory']->create(ChangePasswordType::class, $user);
        $changePasswordForm->handleRequest($request);
        //if form is submitted and accepted
        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);
            $plainPassword = $user->getPasswordNew();
            $encoder = $app['security.encoder.bcrypt'];
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password);
            $app['manager.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'Votre mot de passe a été mis à jour.');

            return $app['twig']->render('change_password_form.html.twig', array(
                'title' => 'Vous souhaitez changer votre mot de passe?',
                'changePasswordForm' => $changePasswordForm->createView(),
                'articlesVisible' => $articlesVisible,
            ));
        }
        return $app['twig']->render('change_password_form.html.twig', array(
            'title' => 'Vous souhaitez changer votre mot de passe?',
            'changePasswordForm' => $changePasswordForm->createView(),
            'articlesVisible' => $articlesVisible,));

    }
}
