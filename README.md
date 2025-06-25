# Introduction
Rollbar is one of the best bug tracking systems that supported a lot of programming languages, including PHP.

You can find their official php library [here](https://packagist.org/packages/rollbar/rollbar) and [symfony bundle](https://packagist.org/packages/rollbar/rollbar-php-symfony-bundle) that allow easily integrate rollbar to your application.
Unfortunately, they announced that the bundle will not be actively developed and will be archived in January 2025:

> As of May 2024, Rollbar will not be actively updating this repository and plans to archive it in January of 2025. We encourage our community to fork this repo if you wish to continue its development. While Rollbar will no longer be engaging in active development, we remain committed to reviewing and merging pull requests related to security updates. If an actively maintained fork emerges, please reach out to our support team and we will link to it from our documentation.

So, looks like it's time to create new rollbar symfony bundle that will support actual PHP and symfony versions.

# Set up instructions

1. To set up the bundle you need to [sign up](https://rollbar.com/signup) rollbar account
2. Add the bundle with composer:
```bash
composer require sfertrack/rollbar-symfony-bundle
```
3. Create `rollbar_symfony.yaml` configuration file (if you use application structure recommended by symfony, then it should be placed to `config/packages` folder)
4. Configure your Rollbar setup in `config/packages/rollbar_symfony.yaml` or in any of the environment subdirectories:
```yaml
rollbar_symfony:
   access_token: [rollbar access token]
```

That's it! Rollbar is integrated to your application.

For complete configuration reference take a look [official documentation](https://docs.rollbar.com/docs/php-configuration-reference)

# Additional configuration

This bundle provides you with several services and patterns that simplify developer life like: 
- [PersonProvider](#person-provider)
- [CheckIgnoreVoter](#check-ignore-voter)
- [ExceptionExtraDataProvider](#exception-extra-data-provider)
- [Scrubber](#scrubber)
- [RollbarReporter](#rollbar-reporter)
- [UserFriendlyExceptionInterface](#user-friendly-exception-interfacce)

## PersonProvider service <a id="person-provider"></a>
The service automatically collects information about authenticated users if your application uses [symfony/security-bundle](https://github.com/symfony/security-bundle). No extra configuration is needed.

If you use impersonation feature, the service will also collect information about the impersonator.

To collect user information, the service uses Symfony serializer. You can easily define which fields you want to report to Rollbar by configuring user entity serialization groups.

If you want to implement your own service to collect user information, simply implement [PersonProviderInterface.php](src/Service/PersonProvider/PersonProviderInterface.php)

You can define several person provider services if your application has a complex authentication flow.
All services that implement [PersonProviderInterface.php](src/Service/PersonProvider/PersonProviderInterface.php) are called in turn, until one of them returns user data.

PersonProvider that comes with this bundle has a priority of `-1` and is called last, in case previous services couldn't provide user data.

Exceptions thrown by person provider services don't stop the call queue. So, don't worry if your person provider service throws an exception - the original exception will still be delivered to Rollbar.

**(!) Note: If a person provider service returns an array without an `id` key, Rollbar [won't report](https://github.com/rollbar/rollbar-php/blob/master/src/DataBuilder.php#L933) user data at all**

## CheckIgnoreVoter <a id="check-ignore-voter"></a>
Sometimes we don't need to see noisy exceptions when a route is not found, a method is not allowed, access is denied, or similar issues occur. For these cases, Rollbar has a `check_ignore` option.

You can define your own service to ignore exceptions with custom rules. To do this, simply implement [CheckIgnoreVoterInterface](src/Service/CheckIgnore/CheckIgnoreVoterInterface.php).

The bundle allows you to create several voters, as the logic to ignore exceptions can be quite complex. 
All voters that implement the interface will be called one by one until one of them votes to ignore the occurred exception. 
If this doesn't happen, the exception will be reported to Rollbar.

For you convenience the bundle includes an example: [CheckIgnoreVoter](src/Service/CheckIgnore/CheckIgnoreVoter.php).
If you want to use it, you can simply define the class as a service in your configuration files.

## ExceptionExtraDataProvider <a id="exception-extra-data-provider"></a>
The bundle provides an interface for exceptions that allows easy passing of additional context to reported exceptions. 
Typically, developers include this context in exception messages, but this approach becomes inconvenient, especially when the context contains responses from other services.

To address this issue, the bundle includes [ExceptionExtraDataInterface](src/Service/Exception/ExceptionExtraDataInterface.php).
This interface defines only one method: `getExtraData`. 
When a thrown exception implements `ExceptionExtraDataInterface`, `getExtraData` method is called to retrieve the additional data previously passed by the developer. 
It's assumed that this data is passed through the constructor when the exception is created.

## Scrubber <a id="scrubber"></a>
By default, Rollbar provides a scrubber that allows you to scrub specific values by key. Take a look on `scrub_fields` option

Sometimes, you need to scrub cookie values, such as user sessions, but still want to see other cookies in the occurrence details.

To resolve this problem, the bundle includes [CookieScrubber](https://github.com/zinkovskiy/rollbar-symfony-bundle/blob/a2b80d6d6f26479466423e9efa784f1e89a6e677/src/Service/Scrubber/CookieScrubber.php)

This scrubber is enabled by default. List of keys to be scrubbed can be configured using the `scrub_cookie_fields` option

You can also easily add your own scrubber: simply create a service that implements [ScrubberInterface](https://github.com/zinkovskiy/rollbar-symfony-bundle/blob/a2b80d6d6f26479466423e9efa784f1e89a6e677/src/Service/Scrubber/ScrubberInterface.php)

## RollbarReporter <a id="rollbar-reporter"></a>
You might occasionally need to catch an exception, handle it, and log it to Rollbar, especially to track down tricky issues.

[RollbarReporter](https://github.com/zinkovskiy/rollbar-symfony-bundle/blob/05bd9efaaca62c3de1165feb06aa62dd292f1927/src/Service/RollbarReporter.php#L11) can assist you with this.

## UserFriendlyExceptionInterface <a id="user-friendly-exception-interfacce"></a>
Sometimes, exception messages may contain technical information; displaying these messages to users is not good practice.

[UserFriendlyExceptionInterface](src/Service/UserFriendlyExceptionInterface.php) is designed to distinguish exceptions whose messages are user-friendly and can be displayed to the user. 
Typically, these types of exceptions do not require a fix from developers; instead, users should perform certain actions themselves.

Imagine your developed application depends on an external service. 
Occasionally, this service might be broken and return a 5xx error. 
In such a case, you can throw your own exception that implements UserFriendlyExceptionInterface. 
If you define CheckIgnoreVoter from the package in config, this type of exception will not be logged to Rollbar. 
Furthermore, you can rely on these exceptions and simply display the exception message to the user.

For example:
```php
use SFErTrack\RollbarSymfonyBundle\Service\UserFriendlyExceptionInterface;

final FailedDependencyException extends Exception implements UserFriendlyExceptionInterface {
    public function __construct()
    {
        parent::__construct('Unfortunately zoom service is down, please try again later');
    }
}
```
