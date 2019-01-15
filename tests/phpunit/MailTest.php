<?php

namespace Rotoscoping\Phalcon\Mailer;

use Rotoscoping\Phalcon\Mailer\Mail;
use PHPUnit\Framework\TestCase;

class MailTest extends TestCase
{
    public function testMergeMail()
    {
        $defaultMail = new Mail();
        $defaultMail
            ->from('no-reply@example.com', 'webmaster')
            ->cc('support@example.com')
            ->priority(4);

        $orderMail = new Mail();
        $orderMail
            ->to('user@example.com', 'User')
            ->subject('Ordering Information')
            ->cc('support@example.com', 'Support')
            ->text('Your order #2341 has been shipped')
            ->priority(2);

        /** @var Mail $mergedMail */
        $mergedMail = $defaultMail->merge($orderMail, false);

        $this->assertSame(['no-reply@example.com' => 'webmaster'], $mergedMail->getFrom());
        $this->assertSame(['user@example.com' => 'User'], $mergedMail->getTo());
        $this->assertSame('Ordering Information', $mergedMail->getSubject());
        $this->assertSame('Your order #2341 has been shipped', $mergedMail->getText());
        $this->assertSame(4, $mergedMail->getPriority());
        $this->assertSame(['support@example.com'], $mergedMail->getCc());
    }
}
