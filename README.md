# phalcon-mailer
Mailer wrapper over SwiftMailer and View component for Phalcon.

## Example

### Sending a message from the template
```php
// Initialize manager with mail and view services from DI
$mailer = new \Rotoscoping\Phalcon\Manager('mail', 'view');

// template email_confirmation.phtml in your viewDir
$mailer->send(
  // view path
  'email_confirmation',
  // Swift params
  [
    'subject' => 'Example email confirmation',
    'from' => 'no-reply@example.com',
    'to' => 'user@mail.com'
  ],
  // View params
  [ 
    'username' => 'User Name',
    'token' => 'aq1sw2de3'
  ]
);
```
### Sending a message from the template via a magic method call
```php
// word of the send from the beginning and the template filename in camelCase notation
$mailer->sendEmailConfirmation(
  [
    'subject' => 'Example email confirmation',
    'from' => 'no-reply@example.com',
    'to' => 'user@mail.com'
  ],
  [
    'username' => 'User Name',
    'token' => 'aq1sw2de3'
  ]
);
```

## Sending with a Mailable

Using mailable classes are a lot more elegant than the basic usage example above.
Building up the mail in a mailable class cleans up controllers and routes, making things look a more 
tidy and less cluttered as well as making things so much more manageable.

All of a mailable class' configuration is done in the **compose** method. Within this method, 
you may call various methods such as **from**, **subject**, **view**, and **attach** to configure the email's 
presentation and delivery.

Mailable classes are required to extend the base Rotoscoping\Phalcon\Mailer\Mailable class;

## Inspired by
* [mailer-library](https://github.com/2amigos/mailer-library)
* [slim3-mailer](https://github.com/andrewdyer/slim3-mailer)
* [mailer-library](https://github.com/2amigos/mailer-library)
