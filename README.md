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