<?php    defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Class that is used to redirect based on a page attribute
 * @package Page Redirect
 * @author Michael Krasnow <mnkras@gmail.com>
 * @category Packages
 * @copyright  Copyright (c) 2011 Michael Krasnow. (http://www.c5rockstars.com)
 */

class PageRedirect {
	/**
	 * Redirect to a page based on a page attribute
	 */
	public function checkRedirect() {
		//get the current page
		$page = Page::getCurrentPage();
		//get attribute
		$page_selector = $page->getCollectionAttributeValue('page_selector_redirect');
		//start checking if its a valid page
		if($page_selector > 0) {
			Loader::model('page');
			$npage = Page::getByID($page_selector);
			//more checking
			if(is_object($npage) && !$npage->isError()) {
				//get the page path
				$page_target = $npage->getcollectionpath();
				//load the controller just incase
				$pcontroller = Loader::controller($npage);
				//redirect
				header("HTTP/1.1 301 Moved Permanently"); 
				if(!$npage->isExternalLink()) {
					$pcontroller->redirect($page_target);
				} else {
					header('Location: '.$npage->getCollectionPointerExternalLink());
					exit;
				}
			}
		}	
	}
}

?>