<?php

namespace Rotoscoping\Phalcon\Mailer;


class Mailable
{
    /**
     * The "to" recipients of the message.
     *
     * @var array
     */
    private $to = [];

    /**
     * The "cc" recipients of the message.
     *
     * @var array
     */
    private $cc = [];

    /**
     * The "bcc" recipients of the message.
     *
     * @var array
     */
    private $bcc = [];

    /**
     * The "from" recipients of the message.
     *
     * @var array
     */
    private $from = [];

    /**
     * @var string
     */
    private $subject;

    /**
     * @var integer
     */
    private $priority;

    private $text;

    private $html;

    private $view;

    private $viewData = [];

    private $attachments = [];


    public function send()
    {
        if (method_exists($this, 'compose')) {

            $this->compose();
        }

        $mailer = $this->getMailerInstance();
    }

    /**
     * Add "to" recipients of the message.
     *
     * @param string $address
     * @param string $name
     * @return Mailable
     */
    public function to($address, $name = null)
    {
        return $this->setRecipient($address, $name, 'to');
    }

    /**
     * Add "cc" recipients of the message.
     *
     * @param string $address
     * @param string $name
     * @return Mailable
     */
    public function cc($address, $name = null)
    {
        return $this->setRecipient($address, $name, 'cc');
    }

    /**
     * Add "bcc" recipients of the message.
     *
     * @param string $address
     * @param string $name
     * @return Mailable
     */
    public function bcc($address, $name = null)
    {
        return $this->setRecipient($address, $name, 'bcc');
    }

    /**
     * Add "from" recipients of the message.
     *
     * @param string $address
     * @param string $name
     * @return Mailable
     */
    public function from($address, $name = null)
    {
        return $this->setRecipient($address, $name, 'from');
    }

    /**
     * Set the subject of the message.
     *
     * @param $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the priority of this message.
     *
     * @param int $level
     * @return $this
     */
    public function priority($level = 3)
    {
        $this->priority = 3;

        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function text($body)
    {
        $this->text = $body;

        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function html($body)
    {
        $this->html = $body;

        return $this;
    }

    /**
     * @param $view
     * @return $this
     */
    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function with($key, $value)
    {
        $this->viewData[$key] = $value;

        return $this;
    }

    public function attach($file)
    {
        $this->attachments[] = $file;

        return $this;
    }

    /**
     * Set the recipients of the message
     *
     * Store as list by mixing the use of associative and non-associative array syntax
     *
     * @param $address
     * @param null $name
     * @param string $property
     * @return Mailable
     */
    public function setRecipient($address, $name = null, $property = 'to')
    {
        if (!in_array($property, ['to', 'cc', 'bcc', 'from'])) {
           return $this;
        }

        if(empty($name)) {
            $this->{$property}[$address] = $name;
        } else {
            $this->{$property}[] = $address;
        }

        return $this;
    }

    /**
     * @return Manager
     */
    protected function getMailerInstance()
    {
        return \Phalcon\Di::getDefault()->getShared('mailer');
    }
}