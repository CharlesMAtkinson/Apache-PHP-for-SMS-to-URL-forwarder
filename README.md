# Apache-PHP-for-SMS-to-URL-forwarder

Works with Android app "SMS to URL Forwarder" (https://github.com/bogkonstantin/android_income_sms_gateway_webhook) to email SMS messages.

Requires Apache2 and a local SMTP server accepting local connections for the PHP mail function, https://www.php.net/manual/en/function.mail.php.

Tested with Apache2 2.4.62 and postfix 3.7.11.

There are too many possible ways to set up DNS records to describe them here.  Do whatever floats your boat.  In the example Apache .conf file, the hostname is sms-to-email.\<redacted\>.org

Copy the example Apache .conf file sms-to-email.\<redacted\>.org.conf to /etc/apache2/sites-available.  Change \<redacted\> in its name and content to your own.

There are too many possible ways to set up TLS certificates to describe them here so float your own boat again.  The example Apache .conf file uses letsencrypt certificates.

Install sms-to-email.php in the directory listed in your Apache conffile.  In the example Apache .conf file it is /var/www/sms-to-email/

Install conf.php in the same directory and edit the to address.

Enable the new Apache site using `a2ensite` or otherwise and effect it using `systemctl reload apache2` or otherwise.

On the phone, in the "SMS to URL Forwarder" app, add a configuration for your Apache server.  For testing, set Sender to *.  Set the webhook URL to your variant of ht<span>tps://sms-to-email.\<redacted\>.org/sms-to-email.php

If you don't want to wait for SMS messages to test, adb can be used
```
adb shell
service call isms 5 i32 0 s16 "com.android.mms.service" s16 "null" s16 "<your phone number>" s16 "null" s16 "test message text" s16 "null" s16 "null" i32 0 i64 0
```

## Debugging

Apache logs may help, especially when debug is set TRUE in conf.php.

## Compatibility

Apache-PHP-for-SMS-to-URL-forwarder is not compatible with
[this fork of SMS to URL Forwarder](https://github.com/scottmconway/android_income_sms_gateway_webhook)
which also forwards the sender's name.  If wanted, please request by creating an issue with label "enhancement".

## Alternatives

A pub/sub service like [ntfy](https://ntfy.sh/) is more flexible.

https://code.philo.ydns.eu/philorg/sms-handler supports implementation by an unprivileged user.
