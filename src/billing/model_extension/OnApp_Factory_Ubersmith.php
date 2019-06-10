<?php

/**
 * Overridden class from onapp/onapp-php-wrapper-external composer package.
 *
 * Goal
 * - Enable timeout setting for curl ($this->_ch)
 */
class OnApp_Factory_Ubersmith extends OnApp_Ubersmith
{
	private $timeout = 300;

	public function __construct($hostname, $username, $password, $timeout = null)
	{
		parent::__construct();

		if ($timeout !== null) {
			$this->timeout = $timeout;
		}

		$this->auth($hostname, $username, $password);
	}

	public function _init_curl($user, $pass, $cookiedir = '')
	{
		$this->logger->debug("_init_curl: Init Curl (cookiedir => '$cookiedir').");
		$this->_ch = curl_init();

		if (strlen($this->options[ONAPP_OPTION_CURL_PROXY]) !== '') {
			curl_setopt(
				$this->_ch,
				CURLOPT_PROXY,
				$this->options[ONAPP_OPTION_CURL_PROXY]
			);
		}

		curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt(
			$this->_ch, CURLOPT_USERPWD,
			$user . ':' . $pass
		);

		curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
		curl_setopt($this->_ch, CURLOPT_TIMEOUT, $this->timeout);
	}

	public function factory($name, $debug = false)
	{
		$class_name = 'OnApp_' . $name;

		$result = new $class_name();
		$result->logger->setDebug($debug);

		$result->setOption(ONAPP_OPTION_DEBUG_MODE, $debug);
		$result->logger->setTimezone();
		$result->version = $this->getAPIVersion();
		$result->options = $this->options;
		$result->_ch = $this->_ch;
		$result->initFields($this->getAPIVersion());

		return $result;
	}
}
