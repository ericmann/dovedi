# Dovedi [![Build Status][travis-image]][travis-url] [![Coverage Status][coveralls-image]][coveralls-url]

Time-based One Time Password authentication for WordPress.

Description
-----------

Add support for [time-based one-time-password authentication](https://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm) to WordPress. Once enabled, you can use an application on your phone ([Google Authenticator](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en), [Windows Authenticator](https://www.microsoft.com/en-us/store/apps/authenticator/9wzdncrfj3rj), etc) to generate TOTP tokens and protect your account from prying eyes.

Installation
------------

### Manual Installation ###

1. Upload the entire `/dovedi` directory to the `/wp-content/plugins/` directory.
2. Activate Dovedi through the 'Plugins' menu in WordPress.
3. Enable TOTP support on your user profile

Frequently Asked Questions
--------------------------

### Does this work with Windows? ###

Yes! The [Windows Authenticator App](https://www.microsoft.com/en-us/store/apps/authenticator/9wzdncrfj3rj) works just great!

### Does this work with Android? ###

Yes! The [Google Authenticator App](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en) works perfectly!

### Does this work with iPhone? ###

Probably. But it hasn't been tested...

Screenshots
-----------

### 1. Enable two-step authentication for your WordPress login ###
![Enable two-step authentication for your WordPress login](https://s3-us-west-2.amazonaws.com/6675d06c-ea96-49d2-8788-c5bc5129fb4a/Enable-Two-Step.png)

### 2. Scan the QR code with your mobile app, or enter the code manually ###
![Scan the QR code with your mobile app, or enter the code manually](https://s3-us-west-2.amazonaws.com/6675d06c-ea96-49d2-8788-c5bc5129fb4a/Two-Step-QR.png)

### 3. Once enabled, you will be prompted for a second login factor whenever you log in ###
![Once enabled, you will be prompted for a second login factor whenever you log in](https://s3-us-west-2.amazonaws.com/6675d06c-ea96-49d2-8788-c5bc5129fb4a/Authentication-Required.png)

Changelog
----------

### 1.1.0 ###
* Nag users to set up 2FA
* List users' setup status in the Users table

### 1.0.0 ###
* Refactor for testability (97% coverage!)

### 0.1.0 ###
* First release

Upgrade Notice
--------------

### 0.1.0 ###
First Release

**Contributors:**      Eric Mann  
**Donate link:**       https://eamann.com  
**Tags:**              2fa, authentication, login  
**Requires at least:** 4.3.1  
**Tested up to:**      4.6.0
**Stable tag:**        1.1.0
**License:**           GPLv2 or later  
**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html  

[travis-image]: https://travis-ci.org/ericmann/dovedi.svg?branch=master
[travis-url]: https://travis-ci.org/ericmann/dovedi
[coveralls-image]: https://coveralls.io/repos/ericmann/dovedi/badge.svg?branch=master&service=github
[coveralls-url]: https://coveralls.io/github/ericmann/dovedi?branch=master