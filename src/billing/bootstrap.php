<?php

namespace UbersmithPlugin\Onapp\Billing;

// OnApp composer package load & file extensions
require_once __DIR__ . '/../vendor/onapp/onapp-php-wrapper-external/OnAppInit.php';
require_once 'model_extension/OnApp_User_BillingStatistics_Ubersmith.php';
require_once 'model_extension/OnApp_Ubersmith.php';
require_once 'model_extension/OnApp_Factory_Ubersmith.php';

// main files
require_once 'class.client.php';
require_once 'class.instance.php';
require_once 'class.resource_dictionary.php';
require_once 'class.usage_datasource.php';
require_once 'onapp_factory_wrapper.php';

// abstract classes
require_once 'method/usage.php';
require_once 'method/details.php';

require_once 'method/user/usage.php';
require_once 'method/user/details.php';
require_once 'method/vdcs/usage.php';
require_once 'method/vdcs/details.php';
require_once 'method/user_full/usage.php';
require_once 'method/user_full/details.php';
require_once 'method/cdn/usage.php';
require_once 'method/cdn/details.php';
