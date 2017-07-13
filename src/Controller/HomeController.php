<?php

namespace Alaska\Controller;

use Alaska\Form\Type\UserRegistrationType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Alaska\Domain\Comment;
use Alaska\Form\Type\CommentType;
use Alaska\Form\Type\ForgotPasswordType;
use Alaska\Domain\User;

class HomeController
{

    /**
     * Home page with pages control controller.
     *
     * @param page number
     * @param Application $app Silex application
     */
    public function indexPageAction($pageId, Application $app)
    {
        //Number of articles displayed per page
        $articlesPerPage = 3;
        $articlesVisible_total = $app['manager.article']->articlesVisibleCount();
        $pageNb = ceil($articlesVisible_total / $articlesPerPage);
        //If page nb does not exist send to error page
        if ($pageId > $pageNb) {
            return $app['twig']->render('error.html.twig', array(
                'message' => 'La page n\'existe pas'));
        }
        //If connected user, refresh last connected date
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['user'];
            $user->setLastConnectedDate(date("Y-m-d H:i:s"));
            $app['manager.user']->save($user);
        }
        //If only 1 page, redirect to normal index
        if (!$pageNb > 1) {
            return $app->redirect($app['url_generator']->generate('index'));
        }
        //display index with pages system
        else {
            $articlesVisibleDesc = $app['manager.article']->findVisibleArticlesByPage($pageId, $articlesPerPage);
            $articlesVisible = $app['manager.article']->findAllVisible();

            return $app['twig']->render('index.html.twig', array(
                'articlesVisibleDesc' => $articlesVisibleDesc,
                'articlesVisible' => $articlesVisible,
                'pageId' => $pageId,
                'pageNb' => $pageNb,
            ));
        }
    }

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app)
    {
        //Number of articles displayed
        $articlesPerPage = 3;
        $articlesVisible_total = $app['manager.article']->articlesVisibleCount();
        $pageNb = ceil($articlesVisible_total / $articlesPerPage);
        //If connected user, refresh last connected date
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['user'];
            $user->setLastConnectedDate(date("Y-m-d H:i:s"));
            $app['manager.user']->save($user);
        }
        //If less than 1 page, display index
        if (!$pageNb > 1) {
            $articlesVisibleDesc = $app['manager.article']->findAllVisibleDesc($articlesPerPage);
            $articlesVisible = $app['manager.article']->findAllVisible();
            return $app['twig']->render('index.html.twig', array('articlesVisibleDesc' => $articlesVisibleDesc,
                'articlesVisible' => $articlesVisible));
        }
        //If more than 1 page, redirect to pages system index
        else{
            return $app->redirect($app['url_generator']->generate('index_page', array('pageId' => 1)));
        }
    }

    /**
     * Article details controller.
     *
     * @param integer $artChap Article chapter
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function articleAction($artChap, Request $request, Application $app)
    {
        //Display article depending on authorization
        if ($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            $article = $app['manager.article']->find($artChap);
        }
        else {
            $article = $app['manager.article']->findVisible($artChap);
        }
        $articlesVisible = $app['manager.article']->findAllVisible();
        $chapterMax = $app['manager.article']->findChapterMaxVisible();

        //Add 1 to the article view counter if not admin
        if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            $article->setViewsNb($article->getViewsNb() + 1);
            $app['manager.article']->save($article);
        }
        $commentFormView = null;
        // if a user is fully authenticated : he can add comments
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $comment = new Comment();
            $comment->setArticle($article);
            $user = $app['user'];
            //Register the viewed art id so we can welcome with the last read chapter
            $chapter = $article->getChapter();
            $user->setLastViewArt($chapter);
            $app['manager.user']->save($user);
            $comment->setAuthor($user);
            $comment->setCommentReportedNb(0);
            $commentForm = $app['form.factory']->create(CommentType::class, $comment);
            $commentForm->handleRequest($request);
            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                $app['manager.comment']->save($comment);
                $app['session']->getFlashBag()->add('success', 'Votre commentaire a été mis en ligne.');
            }
            $commentFormView = $commentForm->createView();
        }
        $comments = $app['manager.comment']->findAllByArticle($artChap);

        return $app['twig']->render('article.html.twig', array(
            'article' => $article,
            'comments' => $comments,
            'commentForm' => $commentFormView,
            'articlesVisible' => $articlesVisible,
            'chapterMax' => $chapterMax));
    }

    /**
     * Article details controller.
     *
     * @param integer $id Article id
     * @param Application $app Silex application
     */
    public function nextArticleAction($artChap, Application $app)
    {
        $nextArticleVisible = $app['manager.article']->findNextVisible($artChap);
        //If no more article to display
        if ($nextArticleVisible == "No next") {
            $app['session']->getFlashBag()->add('warning', 'Il n\'y a pas de prochain article pour le moment.');
            return $app->redirect($app['url_generator']->generate('article', array('artChap' => $artChap - 1)));
        }
        //Else display the next article
        else {
            return $app->redirect($app['url_generator']->generate('article', array('artChap' => $nextArticleVisible->getChapter())));
        }

    }

    /**
     * User login controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app)
    {
        $articlesVisible = $app['manager.article']->findAllVisible();

        return $app['twig']->render('login.html.twig', array(
            'error' => $app['security.last_error']($request),
            'articlesVisible' => $articlesVisible,
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }


    /**
     * User register controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function registerAction(Request $request, Application $app)
    {
        $user = new User();
        $user->setLastViewArt(1);
        $articlesVisible = $app['manager.article']->findAllVisible();
        $userForm = $app['form.factory']->create(UserRegistrationType::class, $user);
        $userForm->handleRequest($request);
        //if form is submitted and accepted
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user1 = $app['manager.user']->loadUserByUsername($user->getUsername());
            $user2 = $app['manager.user']->loadUserByEmail($user->getEmail());
            //if username is already used
            if ($user1 != "No user") {
                $app['session']->getFlashBag()->add('warning', 'Ce pseudo est déjà utilisé. Merci d\'en choisir un autre.');
            }
            //if email address is already used
            else if($user2 !="No user"){
                $app['session']->getFlashBag()->add('warning', 'Cette adresse email est déjà utilisée. Merci d\'en choisir une autre.');
            }
            //else create new user
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
            $user->setRole('ROLE_USER');
            $app['manager.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'Vous êtes bien enregistré. Tapez votre pseudo et mot de passe pour vous loguer');

            return $app->redirect($app['url_generator']->generate('login'));
        }}
        return $app['twig']->render('user_registration_form.html.twig', array(
            'title' => 'Inscription',
            'userForm' => $userForm->createView(),
            'articlesVisible' => $articlesVisible));
    }


    /**
     * Comment report controller.
     *
     * @param integer $id comment id
     * @param Application $app Silex application
     */
    public function commentReportAction($idComment, Application $app)
    {
        $comment = $app['manager.comment']->find($idComment);
        $articleId = $app['manager.comment']->findArticleIdByComId($idComment);
        $article= $app['manager.article']->find($articleId);
        $articleChapter = $article->getChapter();
        $comment->setCommentReportedNb($comment->getCommentReportedNb() + 1);
        $app['manager.comment']->save($comment);
        $app['session']->getFlashBag()->add('warning', 'Le commentaire a été signalé.');

        return $app->redirect($app['url_generator']->generate('article', array('artChap' => $articleChapter)));
    }

    /**
     * User login controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function mentionsLegalesAction(Application $app)
    {
        $articlesVisible = $app['manager.article']->findAllVisible();

        return $app['twig']->render('mentions_legales.html.twig', array(
            'articlesVisible' => $articlesVisible
        ));
    }

    /**
     * Forgot password controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function forgotPasswordAction(Request $request, Application $app)
    {
        $articlesVisible = $app['manager.article']->findAllVisible();
        $user = new User();
        $forgotPasswordForm = $app['form.factory']->create(ForgotPasswordType::class, $user);
        $forgotPasswordForm->handleRequest($request);
        //if form is submitted and accepted
        if ($forgotPasswordForm->isSubmitted() && $forgotPasswordForm->isValid()) {
            // generate a user with the input email
            $user = $app['manager.user']->loadUserByEmail($user->getEmail());
            //If email address is not known
            if ($user == false) {
                $app['session']->getFlashBag()->add('warning', 'Cette adresse n\'est pas connue dans nos registres. Merci de vérifier votre adresse email.');
            }
            //send email with new password
            else {

                    $username = $user->getUsername();
                    $salt = substr(md5(time()), 0, 23);
                    $user->setSalt($salt);
                    $random_password = chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)). chr(rand(65,90));
                    $encoder = $app['security.encoder.bcrypt'];
                    $password = $encoder->encodePassword($random_password, $user->getSalt());
                    $user->setPassword($password);
                    $app['manager.user']->save($user);
                    $email=$user->getEmail();

                    //Does not work online
                     $message = \Swift_Message::newInstance()
                        ->setSubject('Nouveau mot de passe')
                        ->setFrom(array('billet.simple.alaska@gmail.com'))
                        ->setTo(array($email))
                        ->setBody($app['twig']->render(
                            'new_password_message.html.twig', array('name' => $username, 'password' =>$random_password)),'text/html')
                        ;
                $app['mailer']->send($message);

                //Does not work offline
                /*
                $to      = $email;
                $subject = 'Nouveau mot de passe';
                $message = $app['twig']->render(
                    'new_password_message.html.twig', array('name' => $username, 'password' =>$random_password));
                $headers = "From: no-reply@alaska.com\r\n";
                $headers .= "Content-Type: text/html; charset=utf-8";
                mail($to, $subject, $message, $headers);
                */

                $app['session']->getFlashBag()->add('success', 'Un email avec votre nouveau mot de passe vous a été envoyé');

            return $app['twig']->render('forgot_password_form.html.twig', array(
                'title' => 'Vous avez oublié votre mot de passe?',
                'forgotPasswordForm' => $forgotPasswordForm->createView(),
                'articlesVisible' => $articlesVisible,
            ));
        }}
        return $app['twig']->render('forgot_password_form.html.twig', array(
            'title' => 'Vous avez oublié votre mot de passe?',
            'forgotPasswordForm' => $forgotPasswordForm->createView(),
            'articlesVisible' => $articlesVisible,
        ));

    }

}

