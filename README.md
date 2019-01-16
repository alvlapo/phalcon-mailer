# phalcon-mailer
Mailer wrapper over SwiftMailer and View component for [Phalcon framework](https://phalconphp.com/).

## Usage

### Sending the Email

```php
// Initialize manager with mail and view services from DI
$mailer = new \Rotoscoping\Phalcon\Manager('mail', 'view');

// Compose mail
$mailer
  ->to('test@test.com')
  ->subject('Simple subject')
  ->text('Simple text part message')
  ->send();
  
// Fin
```
### Configuring The Sender

There are two ways to configure the sender. 

First, you may use the **from** method within your Mail class' build method:

```php
$mail = new Mail();
$mail
  ->from('support@example.com', 'Support team')
  ->to('test@test.com')
  ->subject('Simple subject')
  ->text('Simple text part message');
  
$mail->send();
```

Instead, you may specify a global "from" address throw draft mail in your Manager instance. 
This address will be used by default for all you mails:

```php
$defaultMail = new Mail();
$defaultMail
  ->from('support@example.com', 'Support team');
  
$mailer->setDraftMail($defaultMail);

$mail = new Mail();
$mail
  ->to('test@test.com')
  ->subject('Simple subject')
  ->text('Simple text part message');
  
$mail->send();
```


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
