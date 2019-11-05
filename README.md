# MonologStackdriverHandler
This package allows you to store Monolog log data to Google Cloud Platform (Google Stackdriver Logging service).

This package also allows you enabling batching option to batch multiple logs into one single RPC call.

### Installation
To begin, install the preferred dependency manager for PHP, Composer.

Now to install just this component:
```
$ composer require netflie/monolog-stackdriver-handler
```
### Configuration
```php
require 'vendor/autoload.php';

use Netflie\MonologStackdriverHandler\MonologStackdriverHandler;
use Monolog\Logger;

// See Google\Cloud\Logging\LoggingClient::__construct
$loggingClientOptions = [
    'keyFilePath' => '/path/to/service-account-key-file.json'
];

$monologStackdriverHandler = new MonologStackdriverHandler('my_log', $loggingClientOptions);

$monologLogger = new Logger('name');
$monologLogger->pushHandler($monologStackdriverHandler);

// Send records to Google Stackdriver Logging
$monologLogger->notice('Foo');
$monologLogger->error('Bar');
```

### Enabling batching option
The handler sends the logs synchronously. This means that whenever you emit a log, it will add RPC latency to the user request. Especially if you emit multiple logs in a single request, the added latency will be significant. You probably want to avoid that.

The following code creates a PSR-3 handler logger which will batch multiple logs into one single RPC calls:

```php
$loggerOptions = [
    'batchEnabled' => true,
];

$monologStackdriverHandler = new MonologStackdriverHandler(
    'my_log',
    $loggingClientOptions,
    $loggerOptions
);
```


### Version

This component will not introduce backwards-incompatible changes in any minor or patch releases. We will address issues and requests with the highest priority.