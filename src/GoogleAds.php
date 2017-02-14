<?php
namespace Edujugon\GoogleAds;


use Edujugon\GoogleAds\Auth\OAuth2;
use Edujugon\GoogleAds\Reports\Fields;
use Edujugon\GoogleAds\Reports\Format;
use Edujugon\GoogleAds\Reports\Report;
use Edujugon\GoogleAds\Reports\ReportTypes;
use Edujugon\GoogleAds\Services\AdGroup;
use Edujugon\GoogleAds\Services\AdGroupAd;
use Edujugon\GoogleAds\Services\Campaign;
use Edujugon\GoogleAds\Services\Service;
use Edujugon\GoogleAds\Session\AdwordsSession;

class GoogleAds
{

    /**
     * Full config data.
     *
     * @var mixed
     */
    protected $config;

    /**
     * Environment => test/production
     * @var
     */
    protected $env;

    /**
     * oAuth UserRefreshCredentials
     * @var
     */
    protected $userCredentials;

    /**
     * Session.
     * @var
     */
    protected $session;

    /**
     * Service
     * @var
     */
    protected $service;

    /**
     * GoogleAds constructor.
     * @param null $env
     */
    public function __construct($env = null)
    {
        $this->config = eConfig();

        $this->env = $env ? $env : $this->config['env'] ;
    }

    /**
     * Generate User Credentials.
     *
     * @param array $data
     * @return $this
     */
    public function oAuth(array $data = [])
    {
        $this->userCredentials = (new OAuth2())->userCredentials($data);

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function session(array $data = [])
    {
        $this->session = new AdwordsSession();

        if(!$this->userCredentials)
            $this->session->oAuth($this->env);

        $this->session = $this->session->build($data);

        return $this;
    }

    /**
     * Get an instance of Fields
     *
     * @return Fields
     */
    public function fields()
    {
      return new Fields($this->session);
    }

    //////////////////////////////////
    //SERVICES
    /////////////////////////////////

    /**
     * Set the google adwords service.
     *
     * @param \Google\AdsApi\AdWords\v201609\cm\AdGroupService | \Google\AdsApi\AdWords\v201609\cm\AdGroupAdService | \Google\AdsApi\AdWords\v201609\cm\CampaignService  $service
     * @return \Edujugon\GoogleAds\Services\Service
     */
    public function service($service)
    {
        $this->service = (new Service($this->session))->service($service);

        return $this->service;
    }

    /**
     * Set the google adwords service.
     *
     * @param $name
     * @return Service
     */
    public function serviceByName($name)
    {
        $this->service = (new Service($this->session))->serviceByName($name);

        return $this->service;
    }

    /**
     * AdGroupService
     * @return AdGroup
     */
    public function adGroupService()
    {
        return new AdGroup($this->session);
    }

    /**
     * AdGroupAdService
     * @return AdGroupAd
     */
    public function adGroupAdService()
    {
        return new AdGroupAd($this->session);
    }

    /**
     * CampaignService
     * @return Campaign
     */
    public function campaignService()
    {
        return new Campaign($this->session);
    }


    /////////////////////////////////
    //REPORTS
    /////////////////////////////////

    /**
     * Get an instance of Report
     * @return Report
     */
    public function report()
    {
        return new Report($this->session);
    }

    /**
     * Show a list of report types
     *
     * @return array
     */
    public function showReportTypes()
    {
        return ReportTypes::list();
    }

    /**
     * Show a list of report formats.
     * @return array
     */
    public function showReportFormats()
    {
        return Format::list();
    }

    /////////////////////////////////
    //GETTERS AND SETTERS
    /////////////////////////////////

    /**
     * Get the service.
     *
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Get the session.
     *
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Get UserRefreshCredentials
     *
     * @return \Google\Auth\Credentials\UserRefreshCredentials
     */
    public function getUserCredentials()
    {
        return $this->userCredentials;
    }

    /**
     * Set the environment to work with.
     *
     * @param $env
     * @return $this
     */
    public function env($env){
        $this->env = $env;

        return $this;
    }

    /**
     * Get the environment.
     * @return null
     */
    public function getEnv()
    {
        return $this->env;
    }

}