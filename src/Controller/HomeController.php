<?php

namespace Alaska\Controller;

use Alaska\Form\Type\UserRegistrationType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Alaska\Domain\Comment;
use Alaska\Form\Type\CommentType;
use Alaska\Domain\User;
use Alaska\Form\Type\UserType;
use Alaska\Domain\CommentReported;

class HomeController {

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
        $articles = $app['manager.article']->findAllVisible();
        return $app['twig']->render('index.html.twig', array('articles' => $articles));
    }
    
    /**
     * Article details controller.
     *
     * @param integer $id Article id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function articleAction($id, Request $request, Application $app) {
        $article = $app['manager.article']->find($id);
        $commentFormView = null;
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            // A user is fully authenticated : he can add comments
            $comment = new Comment();
            $comment->setArticle($article);
            $user = $app['user'];
            $comment->setAuthor($user);
            $comment->setCommentDate(date("Y-m-d H:i:s"));
            $commentForm = $app['form.factory']->create(CommentType::class, $comment);
            $commentForm->handleRequest($request);
            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                $app['manager.comment']->save($comment);
                $app['session']->getFlashBag()->add('success', 'Votre commentaire a été mis en ligne.');
            }
            $commentFormView = $commentForm->createView();
        }
        $comments = $app['manager.comment']->findAllByArticle($id);
        
        return $app['twig']->render('article.html.twig', array(
            'article' => $article,
            'comments' => $comments,
            'commentForm' => $commentFormView));
    }
    
    /**
     * User login controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

    /**
     * User register controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function registerAction(Request $request, Application $app) {
        $user = new User();
        $userForm = $app['form.factory']->create(UserRegistrationType::class, $user);
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
            $user->setRole('ROLE_USER');
            $app['manager.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'Vous êtes bien enregistré.');
        }
        return $app['twig']->render('user_registration_form.html.twig', array(
            'title' => 'Inscription',
            'userForm' => $userForm->createView()));
    }

    /**
     * Comment report controller.
     *
     * @param integer $id comment id
     * @param Application $app Silex application
     */
    public function commentReportAction($idcomment,$idarticle, Application $app) {
        $comment = $app['manager.comment']->find($idcomment);
        $commentReportedBefore=$app['manager.commentReported']->findByComment($idcomment);

        //The comment has already been notified
        if($commentReportedBefore == false){
            $reportedComment = new CommentReported();
            $reportedComment->setComment($comment);
            $reportedComment->setCounter(1);
            $app['manager.commentReported']->save($reportedComment);
            $app['session']->getFlashBag()->add('success', 'Le commentaire a été signalé.');
            return $app->redirect($app['url_generator']->generate('article', array('id' => $idarticle)));
        }
        else {
            $newcounter = $commentReportedBefore->getCounter() + 1;
            $commentReportedBefore->setCounter($newcounter);
            $commentReportedBefore->setComment($comment);
            $app['manager.commentReported']->save($commentReportedBefore);
            $app['session']->getFlashBag()->add('success', 'Le commentaire a été signalé.');
            return $app->redirect($app['url_generator']->generate('article', array('id' =>$idarticle)));
        }
    }
}
