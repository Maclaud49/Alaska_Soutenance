<?php

namespace Alaska\Controller;

use Doctrine\DBAL\Driver\PDOException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Alaska\Domain\Article;
use Alaska\Domain\User;
use Alaska\Form\Type\ArticleType;
use Alaska\Form\Type\CommentType;
use Alaska\Form\Type\UserType;


class AdminController {

    /**
     * Admin home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
        $articles = $app['manager.article']->findAll();
        $comments = $app['manager.comment']->findAll();
        $users = $app['manager.user']->findAll();
        $commentsReported =$app['manager.commentReported']->findAll();
        return $app['twig']->render('admin.html.twig', array(
            'articles' => $articles,
            'comments' => $comments,
            'users' => $users,
            'commentsReported' =>$commentsReported));
    }

    /**
     * Add article controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function addArticleAction(Request $request, Application $app) {
        $article = new Article();
        $articleForm = $app['form.factory']->create(ArticleType::class, $article);
        $articleForm->handleRequest($request);
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            //if the chapter number is not used
            if ($app['manager.article']->checkChapter($article->getChapter())) {
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
            'articleForm' => $articleForm->createView()));
    }

    /**
     * Edit article controller.
     *
     * @param integer $id Article id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editArticleAction($id, Request $request, Application $app)
    {

        $article = $app['manager.article']->find($id);
        $articleForm = $app['form.factory']->create(ArticleType::class, $article);
        $articleForm->handleRequest($request);
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            //if chapter changed
            if ($article->getChapter() != $id) {
                //if the chapter number is not used
                if ($app['manager.article']->checkChapter($article->getChapter())) {
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
            'articleForm' => $articleForm->createView()));
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
        $commentForm = $app['form.factory']->create(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $app['manager.comment']->save($comment);
            $app['session']->getFlashBag()->add('success', 'Le commentaire a été modifié.');
        }
        return $app['twig']->render('comment_form.html.twig', array(
            'title' => 'Mofidier le commentaire',
            'commentForm' => $commentForm->createView()));
    }

    /**
     * Delete comment controller.
     *
     * @param integer $id Comment id
     * @param Application $app Silex application
     */
    public function deleteCommentAction($id, Application $app) {
        // Delete all associated reported comments
        $app['manager.commentReported']->deleteAllByComment($id);
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
        $userForm = $app['form.factory']->create(UserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // generate a random salt value
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);
            $plainPassword = $user->getPassword();
            // find the default encoder
            $encoder = $app['security.encoder.bcrypt'];
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password); 
            $app['manager.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'L\'utilisateur a correctement été enregistré.');
        }
        return $app['twig']->render('user_form.html.twig', array(
            'title' => 'Nouvel utilisateur',
            'userForm' => $userForm->createView()));
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
        $userForm = $app['form.factory']->create(UserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $plainPassword = $user->getPassword();
            // find the encoder for the user
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password); 
            $app['manager.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'The user was successfully updated.');
        }
        return $app['twig']->render('user_form.html.twig', array(
            'title' => 'Edit user',
            'userForm' => $userForm->createView()));
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
        $app['session']->getFlashBag()->add('success', 'The user was successfully removed.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }
}
