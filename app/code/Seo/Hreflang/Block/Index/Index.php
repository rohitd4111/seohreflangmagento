<?php
/* Author: Rohit Dhiman
 * Purpose: Provide Specific Conditions for Hreflang Tag in CMS Pages for Content Duplication Issue
 * */
namespace Seo\Hreflang\Block\Index;

use Magento\Store\Model\ScopeInterface;

class Index extends \Magento\Framework\View\Element\Template {

    const XML_PATH_HREFLANG = 'seohreflangsection/general/lang_code';
    protected $_page;
    protected $_storeManager;
    protected $request;
    protected $_scopeConfig;
    public function __construct(
        \Magento\Cms\Model\Page $page,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlInterface,
        array $data = []
    )
    {
        $this->_page = $page;
        $this->_storeManager = $storeManager;
        $this->request = $request;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }


    /**
     * Add Hrelang Tag if Content is Duplicate for current CMS Page
     *
     * @return string|int|null
     */
    public function getHrefLang()
    {
        if($this->request->getModuleName() == 'cms')
        {
            $languagecode=  $this->_scopeConfig->getValue(self::XML_PATH_HREFLANG , ScopeInterface::SCOPE_WEBSITES); //Get Language Code from System Configuration for Particular Website.
            if($this->getCmsPages() > 1)
            {
				return '<link rel="alternate" href="' . $this->getUrlInterfaceData() . '" hreflang="' . $languagecode . '"/>'; //Final Hreflang Tag.
            }
        }
    }

	// Get CMS Page Information //
    public function getCmsPages()
    {
        $filters[] = $this->filterBuilder
            ->setField('identifier')
            ->setConditionType('eq')
            ->setValue($this->_page->getIdentifier())
            ->create();
        $this->searchCriteriaBuilder->addFilters($filters);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $pages = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();
        return count($pages); // Returning Count of current CMS Page, If returns more than 1 then Duplication issue is there.
    }

	// GET current URL to be added in hreflang meta tag.
    public function getUrlInterfaceData()
    {
        return $this->_urlInterface->getCurrentUrl(); 
    }
}
