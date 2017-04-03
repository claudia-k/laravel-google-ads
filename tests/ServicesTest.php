<?php


use Google\AdsApi\AdWords\v201609\cm\AdvertisingChannelType;
use Google\AdsApi\AdWords\v201609\cm\BiddingStrategyConfiguration;
use Google\AdsApi\AdWords\v201609\cm\BiddingStrategyType;
use Google\AdsApi\AdWords\v201609\cm\Budget;
use Google\AdsApi\AdWords\v201609\cm\BudgetBudgetDeliveryMethod;
use Google\AdsApi\AdWords\v201609\cm\BudgetOperation;
use Google\AdsApi\AdWords\v201609\cm\CampaignOperation;
use Google\AdsApi\AdWords\v201609\cm\CampaignService;
use Google\AdsApi\AdWords\v201609\cm\Money;
use Google\AdsApi\AdWords\v201609\cm\Operator;

class ServicesTest extends PHPUnit_Framework_TestCase {

    /** @test */
    public function add_service()
    {
        $service = (new \Edujugon\GoogleAds\Services\Service(CampaignService::class));
        $this->assertInstanceOf(\Google\AdsApi\AdWords\v201609\cm\CampaignService::class,$service->getService());
    }


    /** @test */
    public function campaing_all(){
        $campaign = new \Edujugon\GoogleAds\Services\Campaign();
        $this->assertInternalType('integer',$campaign->get(['Id'])[0]->getId());
        $this->assertInternalType('string',$campaign->orderBy('Name')->limit(2)->get()[0]->getName());
        //dd($campaign->all()->getEntries());
    }

    /** @test */
    public function adsGroup_all(){
        $ads = new \Edujugon\GoogleAds\Services\AdGroup();
        //dd($campaign->limit(1)->all());
        //dd($ads->all());

    }

    /** @test */
    public function ads_all(){
        $ads = new \Edujugon\GoogleAds\Services\AdGroupAd();
        $this->assertInternalType('integer',$ads->limit(1)->get(['Id'])[0]->getAd()->getId());
        //dd($ads->limit(1)->all()->getEntries());

    }

    /** @test */
    public function create_new_campaign()
    {
        $campaignService = (new \Edujugon\GoogleAds\Services\Service(CampaignService::class))->getService();

        //Create the campaign
        $campaign = new \Google\AdsApi\AdWords\v201609\cm\Campaign();
        $campaign->setName('My first campaign');
        $campaign->setStatus(\Google\AdsApi\AdWords\v201609\cm\CampaignStatus::PAUSED);

        $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
        $biddingStrategyConfiguration->setBiddingStrategyType(BiddingStrategyType::MANUAL_CPC);
        $campaign->setBiddingStrategyConfiguration($biddingStrategyConfiguration);

        //Budget
        $budgetService = (new \Edujugon\GoogleAds\Services\Service(\Google\AdsApi\AdWords\v201609\cm\BudgetService::class))->getService();
        $sharedBudget = new Budget();
        $budget = new Budget();

        $budgetAmount = new Money();

        $budgetAmount->setMicroAmount(50000000);
        $sharedBudget->setAmount($budgetAmount);
        $sharedBudget->setDeliveryMethod(BudgetBudgetDeliveryMethod::STANDARD);
        $sharedBudget->setName("My shared budget3");

        $budgetOperation = new BudgetOperation();
        $budgetOperation->setOperand($sharedBudget);
        $budgetOperation->setOperator(Operator::ADD);
        $budgetId = $budgetService->mutate([$budgetOperation])->getValue()[0]->getBudgetId();

        $budget->setBudgetId($budgetId);
        $campaign->setBudget($budget);

        $campaign->setAdvertisingChannelType(AdvertisingChannelType::SEARCH);

        $operation = new CampaignOperation();
        $operation->setOperand($campaign);
        $operation->setOperator(Operator::ADD);

        $result = $campaignService->mutate([$operation]);

        dd($result);


    }
}