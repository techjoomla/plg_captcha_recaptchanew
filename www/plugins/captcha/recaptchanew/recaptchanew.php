<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 *
 * @copyright  	Techjoomla. All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class PlgCaptchaRecaptchaNew extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Initialise the captcha
	 *
	 * @return  Boolean	True on success, false otherwise
	 *
	 * @since  2.5
	 */
	public function onInit($id = '')
	{
		//Include javascript
		JHtml::_('script', 'https://www.google.com/recaptcha/api.js');
	}
	
	/**
	 * Gets the challenge HTML
	 *	 	 
	 * @return  string  The HTML to be embedded in the form.
	 *
	 * @since  2.5
	 */
	public function onDisplay($name, $id = '', $class = '')
	{
		//Compose captcha
		$sitekey = $this->params->get('site_key', '');
		$type  = $this->params->get('type', 'image');
		$theme  = $this->params->get('theme', 'light');	
	
		//Show captcha
		$captcha_html = '<div class="g-recaptcha" data-sitekey="'.$sitekey.'" data-theme="'.$theme.'" data-type="'.$type.'"></div>';
		
		return $captcha_html;
	}
	
	/**
	 * Calls an HTTP POST function to verify if the user's guess was correct
	 *
	 * @param  string  $code  Answer provided by user.
	 *
	 * @return  True if success, false otherwise
	 *
	 * @since  2.5
	 */
	public function onCheckAnswer()
	{
		jimport('joomla.http');

		// Required objects 
		$input = JFactory::getApplication()->input;

		//Data array
		$data = array('secret' => $this->params->get('secret_key'), 'response' => $input->get('g-recaptcha-response')); 
		$moduleParams = new JRegistry();
		$http = new JHttp($moduleParams, $transport = null);
		
		//Post data to url and get response
		$response = $http->post($this->params->get('post_url'), $data);
		$response = json_decode($response->body);

		if($response->success)
			return true;
		else
		{			
			$this->_subject->setError(JText::_('PLG_CAPTCHA_RECAPTCHANEW_ERROR'));
			return false;
		}
	}
}
