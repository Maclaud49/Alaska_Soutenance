<?php

namespace Alaska\Controller;

use Alaska\Form\Type\UserRegistrationType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Alaska\Domain\Comment;
use Alaska\Form\Type\CommentType;
use Alaska\Domain\User;
use Alaska\Domain\CommentReported;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

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
     * Set cookie controller.
     *
     * @param Application $app Silex application
     */
    /*public function setCookieAction($username, Application $app) {
        $response = new Response();
        $dt = time() + 365*24*3600;
        $response->headers->setCookie(new Cookie('username', $username,$dt, null, null, false, true));
        return $response;
    }*/
    
    /**
     * Article details controller.
     *
     * @param integer $id Article id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function articleAction($id, Request $request, Application $app) {
        $article = $app['manager.article']->find($id);

        //Add 1 to the article view counter if visitor
        if (!$app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $article->setViewsNb($article->getViewsNb() + 1);
            $app['manager.article']->save($article);
        }
        $commentFormView = null;
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            // A user is fully authenticated : he can add comments
            $comment = new Comment();
            $comment->setArticle($article);
            $user = $app['user'];

            //Register the viewed art id so we can welcome with the last read chapter
            $user->setLastViewArt($id);
            $app['manager.user']->save($user);
            $comment->setAuthor($user);
            $commentForm = $app['form.factory']->create(CommentType::class, $comment);
            $commentForm->handleRequest($request);
            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                $app['manager.comment']->save($comment);
                $app['session']->getFlashBag()->add('success', 'Votre commentaire a été mis en ligne.');
            }
            $commentFormView = $commentForm->createView();
        }
        $comments = $app['manager.comment']->findAllByArticle($id);
        $article = $app['manager.article']->find($id);
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

    //$request->cookies->get('username')

    /**
     * User register controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function registerAction(Request $request, Application $app) {
        $user = new User();
        $user->setLastViewArt(1);
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
            $app['session']->getFlashBag()->add('success', 'Vous êtes bien enregistré. Tapez votre pseudo et mot de passe pour vous loguer');
            return $app->redirect($app['url_generator']->generate('login'));

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
    public function commentReportAction($idComment, Application $app) {
        $comment = $app['manager.comment']->find($idComment);
        $article = $app['manager.comment']->findArticleIdByComId($idComment);
        $comment->setCommentReportedNb($comment->getCommentReportedNb() + 1);
        $app['manager.comment']->save($comment);

            $app['session']->getFlashBag()->add('warning', 'Le commentaire a été signalé.');
            return $app->redirect($app['url_generator']->generate('article', array('id' => $article)));
        }
}
