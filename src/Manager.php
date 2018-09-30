<?php

namespace Rotoscoping\Phalcon\Mailer;

use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\View;
use Swift_Image;

class Manager extends Plugin
{
  /**
   * @var \Swift_Mailer
   */
  private $mailer = null;

  /**
   * @var null|View\Simple
   */
  private $view = null;

  /**
   * @var array
   */
  private $attachedFiles = [];

  /**
   * @var \Swift_Message|null
   */
  private $lastMessage;

  /**
   * Manager constructor.
   * @param $mail
   * @param $view
   * @throws \Exception
   */
  public function __construct($mail = 'mail', $view = 'view')
  {
    if (is_string($mail))
    {
      $mail = $this->getMailFromContainer($mail);
    }

    if (is_string($view))
    {
      $view = $this->getViewFromContainer($view);
    }

    if ( !($mail instanceof \Swift_Mailer) ) {

      throw new \Exception('Mail service must be instance of \Swift_Mailer');
    }

    if ( !($view instanceof View\Simple) ) {

      throw new \Exception('Render service must be instance of \Phalcon\Mvc\View\Simple');
    }

    $this->mailer = $mail;
    $this->view = $view;
  }

  /**
   * @param $path
   * @param $messageParams
   * @param null $templateVariables
   * @return int
   */
  public function send($path, $messageParams, $templateVariables = null)
  {
    $message = new \Swift_Message();

    //ToDo: Add work with the message's HeaderSet
    if (isset($messageParams['subject'])) $message->setSubject($messageParams['subject']);
    if (isset($messageParams['to']))      $message->setTo($messageParams['to']);
    if (isset($messageParams['from']))    $message->setFrom($messageParams['from']);

    $body = $this->view->render($path, $templateVariables);

    $body = preg_replace_callback('/(\w+\/)*\w+\.(jpg|png|gif)/i',
      function ($matches) use ($message) {

        $path = $this->view->getViewsDir() . $matches[0];

        if (isset($this->attachedFiles[$path]))
        {
          return $this->attachedFiles[$path];
        }

        $this->attachedFiles[$path] = $message->embed(Swift_Image::fromPath($path));

        return $this->attachedFiles[$path];
      },
      $body
    );

    $message->setBody($body);

    $this->lastMessage = $message;

    // ToDo:
    // Add list of addresses that were rejected by the Transport
    // by using a by-reference parameter to send()
    return $this->mailer->send($message);
  }

  /**
   * @return mixed
   */
  public function getLastMessage()
  {
    return $this->lastMessage;
  }

  protected function getMailFromContainer($serviceName)
  {
    return $this->getDI()->getShared($serviceName);
  }

  /**
   * @param $serviceName
   * @return View
   * @throws \Exception
   */
  protected function getViewFromContainer($serviceName)
  {
    /** @var View $view */
    $view = $this->getDI()->getShared($serviceName);

    if (empty($view->getViewsDir()))
    {
      throw new \Exception(
        'You must configure ViewsDir in rendering service (Phalcon\Mvc\View\Simple)'
      );
    }

    return $view;
  }

  public function getDI()
  {
    if ($this->_dependencyInjector == null)
    {
      $this->_dependencyInjector = \Phalcon\Di::getDefault();
    }

    return $this->_dependencyInjector;
  }
}