<?php
namespace Relevantly\Rest;

class RestCore {
    private $restSettings;
    private $restPhrases;

    private $version = 'v1';
    private $settingsRoute = '/settings';
    private $phrasesRoute = '/phrases';


    function __construct( RestSettings $restSettings, RestPhrases $restPhrases )
    {
        $this->restSettings = $restSettings;
        $this->restPhrases  = $restPhrases;
    }

    function init()
    {
        $this->restSettings->setVersion( $this->version );
        $this->restSettings->setEndpoint( $this->settingsRoute );

        $this->restSettings->init();

        $this->restPhrases->setVersion( $this->version );
        $this->restPhrases->setEndpoint( $this->phrasesRoute );

        $this->restPhrases->init();
    }
}