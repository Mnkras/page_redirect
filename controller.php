<?php

namespace Concrete\Package\PageRedirect;

defined('C5_EXECUTE') or die("Access Denied.");

use Loader;
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\CollectionKey as CollectionAttributeKey;
use Events;
/**
 * Class that is used to redirect based on a page attribute
 * @package Page Redirect
 * @author Michael Krasnow <mnkras@gmail.com>
 * @category Packages
 * @copyright  Copyright (c) 2014 Michael Krasnow. (http://www.mnkras.com)
 */
class Controller extends \Concrete\Core\Package\Package {

	protected $pkgHandle = 'page_redirect';
	protected $appVersionRequired = '5.7.1';
	protected $pkgVersion = '2.0';
	
	public function getPackageDescription() {
		return t("Adds a page attribute that allows you to specify a page to redirect to.");
	}
	
	public function getPackageName() {
		return t("Page Redirect");
	}
	
	public function install() {
		$PageAttrType = AttributeType::getByHandle('page_selector');
		if(!is_object($PageAttrType) || !intval($PageAttrType->getAttributeTypeID())){ 
			throw new \Exception(t('Please install %s before installing this addon.', '<a href="http://www.concrete5.org/marketplace/addons/page-selector-attribute/">Page Selector Attribute</a>'));
			exit;
		}
		$pkg = parent::install();
		CollectionAttributeKey::add('page_selector', array('akHandle' => 'page_selector_redirect', 'akName' => t('Page Redirect'), 'akIsSearchable' => true), $pkg);
	}
	
	public function on_start() {
		Events::addListener('on_start', function($event) {
            $page = \Page::getCurrentPage();
            if(!is_object($page)) {
                return;
            }
            //get attribute
            $page_selector = $page->getAttribute('page_selector_redirect');
            //start checking if its a valid page
            if($page_selector > 0) {
                $npage = \Page::getByID($page_selector);
                //more checking
                if(is_object($npage) && !$npage->isError()) {
                    //redirect
                    header("HTTP/1.1 301 Moved Permanently");
                    if(!$npage->isExternalLink()) {
                        $nh = Loader::Helper('navigation');
                        header('Location: '.$nh->getLinkToCollection($npage, true));
                        exit;
                    } else {
                        header('Location: '.$npage->getCollectionPointerExternalLink());
                        exit;
                    }
                }
            }
        });
	}
}