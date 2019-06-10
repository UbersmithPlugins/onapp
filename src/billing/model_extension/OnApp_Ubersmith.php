<?php

/**
 * Overridden class from onapp/onapp-php-wrapper-external composer package.
 *
 * Goal
 * - Enable timeout setting for curl ($this->_ch)
 *
 */
class OnApp_Ubersmith extends OnApp
{
	function auth($url, $user, $pass, $proxy = '')
	{
		$this->logger->setDebug($this->options[ONAPP_OPTION_DEBUG_MODE]);
		$this->logger->setTimezone();
		$this->logger->debug('auth: Authorization(url => ' . $url . ', user => ' . $user . ', pass => ********).');

		$this->setOption(ONAPP_OPTION_CURL_URL, $url);
		$this->setOption(ONAPP_OPTION_CURL_PROXY, $proxy);
		$this->_init_curl($user, $pass);
		$this->setAPIResource(ONAPP_GETRESOURCE_VERSION);

		$response = $this->sendRequest(ONAPP_REQUEST_METHOD_GET);
		if ($response['info']['http_code'] == '200') {
			$this->setAPIVersion($response['response_body']);

			if (!in_array($this->getClassName(), [OnApp::class, OnApp_Ubersmith::class])) {
				$this->initFields($this->version);
			}

			$this->setErrors();
			$this->_is_auth = true;
		} else {
			switch ($this->options[ONAPP_OPTION_API_TYPE]) {
				case 'xml':
				case 'json':
					$this->version = null;
					$objCast = new OnApp_Helper_Caster($this);
					$error = $objCast->unserialize($this->getClassName(), $response['response_body'], null, 'errors');
					break;
				default:
					echo 'FATAL ERROR: Caster for "' . $this->options[ONAPP_OPTION_API_TYPE] . '" is not defined'
						. ' in FILE ' . __FILE__ . ' LINE ' . __LINE__ . PHP_EOL . PHP_EOL;
					exit($this->logger->logs());
			}

			$this->setErrors($error);
			$this->_is_auth = false;
		}
	}

	public function initFields($version = null, $className = '')
	{
		if ($version !== null) {
			$this->version = $version;
		}

		if ($this->fields === null && ($this->getClassName() !== 'OnApp')) {
			$this->logger->debug('No fields defined for current API version [ ' . $version . ' ]');
		} elseif ($version !== null) {
			if ($version === $this->version) {
				if ($this->defaultOptions[ONAPP_OPTION_DEBUG_MODE]) {
					$this->logger->debug($className . '::initFields, version ' . $version . PHP_EOL . print_r($this->fields, true));
				} else {
					$this->logger->add($className . '::initFields, version ' . $version);
				}
			}
		}

		if ($this->fields === null && !in_array(static::class, [
				'OnApp', 'OnApp_Factory', 'OnApp_Factory_Ubersmith', 'OnApp_Ubersmith'
			])) {
			throw new Exception(sprintf(
				"The wrapper class '%s' does not support OnApp version '%s'",
				static::class,
				$version
			));
		}
	}
}
