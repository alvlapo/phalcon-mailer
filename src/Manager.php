<?php

namespace Rotoscoping\Phalcon\Mailer;

use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\ViewBaseInterface;
use Swift_Image;

/**
 * Class Manager
 * @package Rotoscoping\Phalcon\Mailer
 *
 * @method Mail to($address, $name = null)
 * @method Mail cc($address, $name = null)
 * @method Mail bcc($address, $name = null)
 * @method Mail from($address, $name = null)
 * @method Mail subject($subject)
 * @method Mail priority($level = 3)
 * @method Mail text(string $text)
 * @method Mail html(string $html)
 * @method Mail view(string $view)
 * @method Mail with($key, $value)
 */
class Manager extends Plugin
{
    /**
    * @var \Swift_Mailer
    */
    private $mailer = null;

    /**
     * @var null
     */
    private $transport = null;

    /**
    * @var null|ViewBaseInterface
    */
    private $view = null;

    /**
     * @var null|Mail
     */
    private $draftMail = null;

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
     *
     * @param \Swift_Transport $transport
     * @param ViewBaseInterface $view
     */
    public function __construct(\Swift_Transport $transport, ViewBaseInterface $view)
    {
        $this->transport = $transport;
        $this->view = $view;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 4) == 'send')
        {
            $pieces = preg_split('/(?<=[a-z])(?=[A-Z])/', substr($name, 4));
            $path = strtolower(implode("_", $pieces));

            $this->send($path, $arguments[0], $arguments[1]);
        }

        if (method_exists($this->getDraftMail(), $name))
        {
            $newMail = clone $this->getDraftMail();

            return call_user_func_array([$newMail, $name], $arguments);
        }
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
    return $this->getSwiftMailer()->send($message);
  }

    /**
     * @return Mail|null
     */
    public function getDraftMail(): Mail
    {
        if (!$this->draftMail) {

            $mailClass = $this->getMailClass();
            $this->draftMail = new $mailClass();
        }

        return $this->draftMail;
    }

    /**
     * @param Mail|null $draftMail
     */
    public function setDraftMail(Mail $draftMail)
    {
        $this->draftMail = $draftMail;
    }

    /**
     * Get Swift mailer instance
     *
     * @return \Swift_Mailer
     */
    public function getSwiftMailer()
    {
        if (!$this->mailer) {

            $this->mailer = new \Swift_Mailer($this->transport);
        }

        return $this->mailer;
    }

  /**
   * @return mixed
   */
  public function getLastMessage()
  {
    return $this->lastMessage;
  }

    /**
     * Get class for default mail
     * Override this method if you extend standard Mail class
     *
     * @return string
     */
    protected function getMailClass(): string
    {
        return Mail::class;
    }
}