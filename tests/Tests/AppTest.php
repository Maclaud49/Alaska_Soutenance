<?php

namespace Alaska\Test;

require_once __DIR__.'/../../vendor/autoload.php';

use Silex\WebTestCase;

class AppTest extends WebTestCase
{
    /** 
     * Basic, application-wide functional test inspired by Symfony best practices.
     * Simply checks that all application URLs load successfully.
     * During test execution, this method is called for each URL returned by the provideUrls method.
     *
     * @dataProvider provideUrls 
     */
    public function testPageIsSuccessful($url)
    {
        $client = $this->createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * {@inheritDoc}
     */
    public function createApplication()
    {
        $app = new \Silex\Application();

        require __DIR__.'/../../app/config/dev.php';
        require __DIR__.'/../../app/app.php';
        require __DIR__.'/../../app/routes.php';
        
        // Generate raw exceptions instead of HTML pages if errors occur
        unset($app['exception_handler']);
        // Simulate sessions for testing
        $app['session.test'] = true;
        // Enable anonymous access to admin zone
        $app['security.access_rules'] = array();

        return $app;
    }

    /**
     * Provides all valid application URLs.
     *
     * @return array The list of all valid application URLs.
     */
    public function provideUrls()
    {
        return array(
            array('/article/1'),
            array('/connexion'),
            array('/admin'),
            array('/enregistrement'),
            array('/admin/article/ajouter'),
            array('/admin/article/1/editer'),
            array('/admin/commentaire/5/editer'),
            array('/admin/utilisateur/ajouter'),
            array('/admin/utilisateur/1/editer'),
            array('/mentions-legales'),
            array('/page/1'),
            array('/connexion/oublie-mot-de-passe'),
            array('/connexion/changer-mot-de-passe'),
        );
    }

    //vendor\bin\phpunit.bat
}
