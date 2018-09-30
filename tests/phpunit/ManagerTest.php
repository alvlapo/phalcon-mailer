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
}
