<?php
/**
 * Registration Redirector for Jom Social 1.5.x
   (C) JoomlaXI.com
 **/
defined('_JEXEC') or die('Restricted access to this plugin'); 
define('XI_RR_JOMSOCIAL', 2);
define('XI_CORE', 3);
define('XI_COMMBUILDER', 4); 
define('XI_VIRTUEMART', 5);
define('XI_OSE', 6);
define('XI_CUSTOM', 7);

$mainframe->registerEvent( 'onAfterRoute', 'redir' );

function redir()
{
	global $mainframe;
	$option = JRequest::getCmd('option');
	$task = JRequest::getCmd('task');
	$view = JRequest::getCmd('view');
	$userid= JRequest::getVar('userid','0','GET');
	$page=  JRequest::getCmd('page');
	// get plugin info
	$plugin =& JPluginHelper::getPlugin('system', 'js_regredirect');
 	$params = new JParameter($plugin->params);
	
 	//parameters to get where to go
	$redirectto		= $params->get('redirectto', 1);
	
	$needLangCode = $params->get('needLangCode', 0);
	$LangCode = $params->get('LangCode', 0);
	
	// if task exist then it must be register
	if($task && $task !='register')
		return;
		
	// View OR task should be register at least 
	if($view != 'register' && $task !='register' && $page !='shop.registration' )
		return;
		
	//index.php?option=com_registration&view=register
	if($option == 'com_community' || $option == 'com_user' || $option == 'com_comprofiler' || $option == 'com_registration' || $option == 'com_virtuemart'  ||$option == 'com_osemsc')
	{
		$Lang_URL =''; 
		if($needLangCode && $LangCode)
			$Lang_URL .= "&lang=".$LangCode;
			
		switch($redirectto)
		{
			case XI_RR_JOMSOCIAL :	
				if($option == 'com_community')
					return;
					
				$toURL = "index.php?option=com_community&view=register".$Lang_URL;
				require_once (JPATH_SITE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php');
				$mainframe->redirect(CRoute::_($toURL,false));
				return;

			case XI_CORE :
				if($option == 'com_user' || $option == 'com_registration')
					return;	
								
				$toURL = "index.php?option=com_user&view=register".$Lang_URL;
				$mainframe->redirect(JRoute::_($toURL,false));
				return;

			case XI_COMMBUILDER :
				if($option == 'com_comprofiler')
					return;	
								
				$toURL = "index.php?option=com_comprofiler&view=register".$Lang_URL;
				$mainframe->redirect(JRoute::_($toURL,false));
				return;
			
			case XI_VIRTUEMART :
				if($option == 'com_virtuemart')
					return;	
								
				$toURL = "index.php?option=com_virtuemart&page=shop.registration".$Lang_URL;
				$mainframe->redirect(JRoute::_($toURL,false));
				return;

			case XI_OSE :
					if ($option == 'com_osemsc')
						return;

					$toURL = "index.php?option=com_osemsc&view=register" . $Lang_URL;
					$mainframe->redirect(JRoute :: _($toURL, false));
					return;

			case XI_CUSTOM :	
				$custom = $params->get('customUrl');		
				if(empty($custom))
					$custom = 'index.php';

				$toURL = $custom.$Lang_URL;
				// Here a issue is in JRoute, so we need to put JURI
				$mainframe->redirect(JRoute::_(JURI::root().DS.$toURL,false));
				return;

			default :
				return;
		}
	}
	
	return;		
	
}
