<?php
/*
* Author 	: Team JoomlaXi @ Ready Bytes Software Labs Pvt. Ltd.
* Email 	: team@readybytes.in
* License 	: GNU-GPL V2
* (C) www.joomlaxi.com
*/


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin' );
//jimport('joomla.filesystem.file');
//jimport('joomla.filesystem.folder' );

	
class plgSystemJxi_reg_redirect extends JPlugin  
{
	/**
	 * return available Joomla Registration system
	 * retun : Array('option'=>array(req_vars))
	 */
	private static function available_registration_system() 
	{
		return 
				Array(
						'com_users' 		=> Array ('view' => 'registration'),
						'com_community'		=> Array ('view' => 'register'),
						'com_comprofiler' 	=> Array ('view' => 'register'),
//						'com_registration' 	=> Array ('view' => 'register'),
						'com_osemsc' 		=> Array ('view' => 'register'),
						'com_virtuemart'	=> Array ('page' => 'shop.registration')
					 );
	}
	
	function onAfterRoute()
	{
		$app = JFactory::getApplication();
		
		//Don't required on back-end
		if($app->isAdmin()) {
			return true;
		}
		
		//plugin params
		$redirect_to		= $this->params->get('redirectto');
		$need_lang_code 	= $this->params->get('needLangCode', 0);
		$lang_code			= $this->params->get('LangCode', 0);
		
		// no need to any redirection
		if(empty($redirect_to)) {
			return true;
		}
		
		$input = $app->input;
		// get url variables
		$option 	= $input->get('option');
//		$task 		= $input->get('task');
//		$view 		= $input->get('view');
//		$userid		= $input->get('userid','0','GET');
//		$page		=  $input->get('page');

		// if you are redircting from JXI redirector or already use default registration system then no need to redirection
		if ($option == $redirect_to) {
			return true;
		}
		

		$registration_system = self::available_registration_system();

		// check current req is registration or not
		$flag = false;
		foreach ($registration_system as $system => $request_var) {
			if ($system == $option) {
				foreach ($request_var as $key =>$value) {
					if($value != $input->get($key)) {
						$flag = false;
						break;
					}
					$flag = true;
				}
				break;
			}
		}

		// If any specified language required
		$lang_url =''; 
		if($need_lang_code && $lang_code ) {
			$lang_url .= "&lang=".$LangCode;
		}
		
		if($flag) {
			// if redirection required to custom url 
			if('custom' == $redirect_to ) {
				$url = $this->params->get('customUrl');
				if(empty($url)) {
					$url= 'index.php';
				}
				// Here a issue is in JRoute, so we need to put JURI
				$app->redirect(JRoute::_(JURI::root().$url.$lang_url, false));
			}
			
			
			// build redirect url
			$query_data = @$registration_system[$redirect_to];
			
			if(JDEBUG  && !$query_data) {
				$app->redirect('index.php', 'JoomlaXi Redirector :: Proper redirection does not available.');
			}
			
			$url = "index.php?option=$redirect_to&".http_build_query($query_data);
			$app->redirect($url);
		}
		return true;		
	}
}
