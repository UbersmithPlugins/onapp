<?php

namespace UbersmithPlugin\Onapp\Billing;

use UbersmithSDK\Error\SDKException;
use TypeError;
use function UbersmithSDK\Util\I18n;

/**
 * Class OnAppFactoryWrapper
 *
 * Wraps \OnApp_Factory to automate error catching in usage classes.
 *
 * @package UbersmithPlugin\Onapp\Billing
 */
class OnAppFactoryWrapper
{
	private $onapp;

	public function __construct($hostname, $username, $password, $timeout)
	{
		$this->onapp = new \OnApp_Factory_Ubersmith($hostname, $username, $password, $timeout);
	}

	public function factory($name, $debug = false)
	{
		if ($this->onapp->isAuthenticate() === false) {
			throw new SDKException(I18n('Unable to connect to OnApp instance'));
		}

		$onappClass = $this->onapp->factory($name, $debug);

		return new class($onappClass)
		{
			private $onappObject;

			public function __construct($onappClass)
			{
				$this->onappObject = $onappClass;
			}

			public function __call($name, $arguments)
			{
				$result = [];

				try {
					$result = call_user_func_array([$this->onappObject, $name], $arguments);
				} catch (TypeError $e) {
					// timeout
					$curl_error_num = curl_errno($this->onappObject->_ch);
					if ($curl_error_num === 28) {
						throw new SDKException(I18n('Request timed out'));
					}
				}

				if (!empty($this->onappObject->getErrorsAsArray())) {
					throw new SDKException($this->onappObject->getErrorsAsString());
				}

				return $result;
			}
		};
	}
}
