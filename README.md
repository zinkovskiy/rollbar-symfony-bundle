# Introduction
Rollbar is one of the best bug tracking systems that supported a lot of programming languages, including PHP.

You can find their official php library [here](https://packagist.org/packages/rollbar/rollbar) and [symfony bundle](https://packagist.org/packages/rollbar/rollbar-php-symfony-bundle) that allow easily integrate rollbar to your application.
Unfortunately, they announced that the bundle will not be actively developed and will be archived in January 2025:

> As of May 2024, Rollbar will not be actively updating this repository and plans to archive it in January of 2025. We encourage our community to fork this repo if you wish to continue its development. While Rollbar will no longer be engaging in active development, we remain committed to reviewing and merging pull requests related to security updates. If an actively maintained fork emerges, please reach out to our support team and we will link to it from our documentation.

So, looks like it's time to create new rollbar symfony bundle that will support actual PHP and symfony versions

# Version support

This bundle support symfony version 6.4 | 7.*

It's compatible with php >= 8.1

# Set up instructions

1. To set up the bundle you need to [sign up](https://rollbar.com/signup) rollbar account
2. Add the bundle with composer:
```bash
composer require ant/rollbar-symfony-bundle
```
3. Create `rollbar_symfony.yaml` configuration file (if you use application structure recommended by symfony , then it should be placed to `config/packages` folder)
4. Configure your Rollbar setup in `config/packages/rollbar.yaml` or in any of the environment subdirectories:
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

### PersonProvider service<a id='person-provider'></a>
The service automatically collects information about authenticated users if your application uses [symfony/security-bundle](symfony/security-bundle). No extra configuration is needed.

If you use impersonation feature, the service will also collect information about the impersonator.

To collect user information, the service uses Symfony serializer. You can easily define which fields you want to report to Rollbar by configuring user entity serialization groups.

If you want to implement your own service to collect user information, simply implement [PersonProviderInterface.php](src/Service/PersonProvider/PersonProviderInterface.php)

You can define several person provider services if your application has a complex authentication flow.
All services that implement [PersonProviderInterface.php](src/Service/PersonProvider/PersonProviderInterface.php) are called in turn, until one of them returns user data.

PersonProvider that comes with this bundle has a priority of `-1` and is called last, in case previous services couldn't provide user data.

Exceptions thrown by person provider services don't stop the call queue. So, don't worry if your person provider service throws an exception - the original exception will still be delivered to Rollbar.

**(!) Note: If a person provider service returns an array without an `id` key, Rollbar [won't report](https://github.com/rollbar/rollbar-php/blob/master/src/DataBuilder.php#L933) user data at all**

### CheckIgnoreVoter<a id='check-ignore-voter'></a>
Sometimes we don't need to see noisy exceptions when a route is not found, a method is not allowed, access is denied, or similar issues occur. For these cases, Rollbar has a `check_ignore` option.

You can define your own service to ignore exceptions with custom rules. To do this, simply implement [CheckIgnoreVoterInterface](src/Service/CheckIgnore/CheckIgnoreVoterInterface.php).

The bundle allows you to create several voters, as the logic to ignore exceptions can be quite complex. 
All voters that implement the interface will be called one by one until one of them votes to ignore the occurred exception. 
If this doesn't happen, the exception will be reported to Rollbar.

For you convenience the bundle includes an example: [CheckIgnoreVoter](src/Service/CheckIgnore/CheckIgnoreVoter.php).
If you want to use it, you can simply define the class as a service in your configuration files.

### ExceptionExtraDataProvider<a id='exception-extra-data-provider'></a>
The bundle provides an interface for exceptions that allows easy passing of additional context to reported exceptions. 
Typically, developers include this context in exception messages, but this approach becomes inconvenient, especially when the context contains responses from other services.

To address this issue, the bundle includes [ExceptionExtraDataInterface](src/Service/Exception/ExceptionExtraDataInterface.php).
This interface defines only one method: `getExtraData`. 
When a thrown exception implements `ExceptionExtraDataInterface`, `getExtraData` method is called to retrieve the additional data previously passed by the developer. 
It's assumed that this data is passed through the constructor when the exception is created.