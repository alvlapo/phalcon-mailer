<?php

namespace Rotoscoping\Phalcon\Mailer;

use Phalcon\Di;
use Phalcon\Mvc\View;
use PHPUnit\Framework\TestCase;

class ManagerTest extends TestCase
{
  protected $mailer;

  public function setUp()
  {
    $stub = $this->getMockBuilder(\Swift_Mailer::class)
      ->setConstructorArgs([new \Swift_SendmailTransport()])
      ->setMethods(['send'])
      ->getMock();

    $stub->method('send')->willReturn(1);

    $this->mailer = $stub;

    if (Di::getDefault()->has('view'))
    {
      Di::getDefault()->get('view')->setViewsDir(ROOT_PATH . '/_data/templates/');
    }
  }

  protected function tearDown()
  {

  }

  /**
   * @throws \Exception
   */
  public function testMessageParams()
  {
    $manager = new Manager($this->mailer);

    $this->assertNull($manager->getLastMessage());

    $manager->send(
      'notification',
      [
        'to' => ['test@example.com' => 'Alex'],
        'subject' => 'Example subject'
      ],
      [
        'username' => 'Alex',
        'password' => 'qwerty'
      ]
    );

    $this->assertSame(['test@example.com' => 'Alex'], $manager->getLastMessage()->getTo());
    $this->assertSame('Example subject', $manager->getLastMessage()->getSubject());
  }

  /**
   * @throws \Exception
   */
  public function testExceptionOnMissServiceInContainer()
  {
    $this->markTestSkipped('must be revisited.');
    $di = Di::getDefault();
    $di->remove('view');

    $this->expectException(\Exception::class);

    $manager = new Manager($this->mailer);
    $manager->send('notification', ['alex@example.com'], ['username' => 'Alex', 'password' => 'qwerty']);
  }

  /**
   * @throws \Exception
   */
  public function testExceptionOnEmptyViewsDirInService()
  {
    $this->markTestSkipped('must be revisited.');
    $di = Di::getDefault();
    /** @var View $view */
    $view = $di->get('view');
    $view->setViewsDir('');

    $this->expectException(\Exception::class);

    $manager = new Manager($this->mailer);
    $manager->send('notification', ['alex@example.com']);
  }

  /**
   * @throws \Exception
   */
  public function testTemplateVariablesMessage()
  {
    $manager = new Manager($this->mailer);

    $manager->send(
      'notification',
      [
        'to' => ['test@example.com' => 'Alex'],
        'subject' => 'Example subject'
      ],
      [
        'username' => 'Alex',
        'password' => 'qwerty'
      ]
    );

    self::assertRegExp('/Welcome Alex!/', $manager->getLastMessage()->getBody());
    self::assertRegExp('/Your password: "qwerty"/', $manager->getLastMessage()->getBody());
  }
  public function testEmbedImageToMessage()
  {
    $manager = new Manager($this->mailer);

    $manager->send(
      'notification',
      [
        'to' => ['test@example.com' => 'Alex'],
        'subject' => 'Example subject'
      ],
      [
        'username' => 'Alex',
        'password' => 'qwerty'
      ]
    );

    self::assertRegExp('/<img src="cid:/', $manager->getLastMessage()->getBody());
    self::assertNotRegExp('/<img src="img\/logo.png">/i', $manager->getLastMessage()->getBody());
  }

  public function testCallMagicMethod()
  {
    $manager = new Manager($this->mailer);

    $manager->sendEmailConfirmation(
      [
        'to' => ['test@example.com' => 'Alex'],
        'subject' => 'Example subject'
      ],
      [
        'username' => 'Alex',
        'token' => 'qwerty'
      ]
    );

    $this->assertRegExp('/Welcome Alex!/', $manager->getLastMessage()->getBody());
    $this->assertSame(['test@example.com' => 'Alex'], $manager->getLastMessage()->getTo());
    $this->assertSame('Example subject', $manager->getLastMessage()->getSubject());
  }

    /**
     * @throws \Exception
     */
    public function testCreateMailFromMailer()
    {
        $manager = new Manager($this->mailer);

        $message1 = $manager
          ->to('test1@test.com')
          ->subject('Test message 1')
          ->text('This is text message 1');

        $message2 = $manager
            ->subject('Test message 2')
            ->to('test2@test.com', 'Test user')
            ->text('This is text message 2');

        // Message 1
        $this->assertSame(['test1@test.com'], $message1->getTo());
        $this->assertSame('Test message 1', $message1->getSubject());
        $this->assertSame('This is text message 1', $message1->getText());

        // Message 2
        $this->assertSame(['test2@test.com' => 'Test user'], $message2->getTo());
        $this->assertSame('Test message 2', $message2->getSubject());
        $this->assertSame('This is text message 2', $message2->getText());
    }

    /**
     * @throws \Exception
     */
    function testDefaultMail()
    {
        $manager = new Manager($this->mailer);

        $defaultMail = new Mail();
        $defaultMail
            ->from('support@example.com','Support team');

        $manager->setDraftMail($defaultMail);

        $mail = $manager
            ->to('user@example.com')
            ->subject('Example subject')
            ->text('Example text body');

        $this->assertSame(['support@example.com' => 'Support team'], $mail->getFrom());
        $this->assertSame(['user@example.com'], $mail->getTo());
        $this->assertSame('Example subject', $mail->getSubject());
        $this->assertSame('Example text body', $mail->getText());
    }
}
