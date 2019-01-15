<?php

namespace Rotoscoping\Phalcon\Mailer;


class Mail
{
    const
        FIELD_TO             = 'to',
        FIELD_FROM           = 'from',
        FIELD_CC             = 'cc',
        FIELD_BCC            = 'bcc',
        FIELD_SUBJECT        = 'subject',
        FIELD_TEXT           = 'text',
        FIELD_HTML           = 'html',
        FIELD_PRIORITY       = 'priority',
        FIELD_VIEW           = 'view',
        FIELD_VIEW_DATA      = 'viewData',
        FIELD_ATTACHMENTS    = 'attachments';

    const MAIL_FIELDS = [
        self::FIELD_TO,
        self::FIELD_FROM,
        self::FIELD_CC,
        self::FIELD_BCC,
        self::FIELD_SUBJECT,
        self::FIELD_TEXT,
        self::FIELD_HTML,
        self::FIELD_PRIORITY,
        self::FIELD_VIEW,
        self::FIELD_VIEW_DATA,
        self::FIELD_ATTACHMENTS
    ];

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
     * @param bool $override
     * @return Mail
     */
    public function to($address, $name = null, $override = false)
    {
        if (!is_array($address) && isset($name)) {

            $address = [$address => $name];
        }

        if ($override)
        {
            return $this->setRecipient((array)$address, self::FIELD_TO);
        }

        return $this->addRecipient((array)$address, self::FIELD_TO);
    }

    /**
     * Add "cc" recipients of the message.
     *
     * @param string $address
     * @param string $name
     * @param bool $override
     * @return Mail
     */
    public function cc($address, $name = null, $override = false)
    {
        if (!is_array($address) && isset($name)) {

            $address = [$address => $name];
        }

        if ($override) {

            return $this->setRecipient((array)$address,self::FIELD_CC);
        }

        return $this->addRecipient((array)$address, self::FIELD_CC);
    }

    /**
     * Add "bcc" recipients of the message.
     *
     * @param string $address
     * @param string $name
     * @param bool $override
     * @return Mail
     */
    public function bcc($address, $name = null, $override = false)
    {
        if (!is_array($address) && isset($name)) {

            $address = [$address => $name];
        }

        if ($override) {

            return $this->setRecipient((array)$address, self::FIELD_BCC);
        }

        return $this->addRecipient((array)$address, self::FIELD_BCC);
    }

    /**
     * Add "from" recipients of the message.
     *
     * @param string $address
     * @param string $name
     * @param bool $override
     * @return Mail
     */
    public function from($address, $name = null, $override = false)
    {
        if (!is_array($address) && isset($name)) {

            $address = [$address => $name];
        }

        if ($override) {

            return $this->setRecipient((array)$address, self::FIELD_FROM);
        }

        return $this->addRecipient((array)$address, self::FIELD_FROM);
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
        $this->priority = $level;

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
     * Get "to" recipients of the message
     *
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Get "cc" recipients of the message
     *
     * @return array
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Get "bcc" recipients of the message
     *
     * @return array
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * Get "from" recipients of the message
     *
     * @return array
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @return mixed
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return array
     */
    public function getViewData(): array
    {
        return $this->viewData;
    }

    /**
     * @return array
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * Get "subject" recipients of the message
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get "text" recipients of the message
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    public function hasTo(): bool
    {
        return !empty($this->to);
    }

    public function hasCc(): bool
    {
        return !empty($this->cc);
    }

    public function hasBcc(): bool
    {
        return !empty($this->bcc);
    }

    public function hasFrom(): bool
    {
        return !empty($this->from);
    }

    public function hasPriority(): bool
    {
        return !empty($this->priority);
    }

    public function hasSubject(): bool
    {
        return !empty($this->subject);
    }

    public function hasText(): bool
    {
        return !empty($this->text);
    }

    public function hasHtml(): bool
    {
        return !empty($this->html);
    }

    public function hasView(): bool
    {
        return !empty($this->view);
    }

    public function hasViewData(): bool
    {
        return !empty($this->viewData);
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    /**
     * Merges two mails together
     *
     * @param Mail $mail
     * @param bool $overrideRecipients
     * @return Mail
     */
    public function merge(Mail $mail, $overrideRecipients = true)
    {
        foreach (self::MAIL_FIELDS as $property) {

            $hasMethod = 'has' . ucfirst($property);
            $getMethod = 'get' . ucfirst($property);
            $setMethod = $property;

            if ($mail->$hasMethod()) {

                $recipientsField = in_array($property, [self::FIELD_TO, self::FIELD_CC, self::FIELD_BCC, self::FIELD_FROM]);

                if (!$this->$hasMethod()) {

                    if ($recipientsField) {

                        $this->setRecipient($mail->$getMethod(), $property);

                    } else {

                        $this->$setMethod($mail->$getMethod());

                    }

                } else {

                    if ($recipientsField && !$overrideRecipients) {

                        $this->addRecipient($mail->$getMethod(), $property);

                    }

                }

            }

        }

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
     * @return Mail
     */
    protected function setRecipient(array $address, $property = self::FIELD_TO): Mail
    {
        // Erase previous recipients
        $this->{$property} = [];

        $this->setAddress($address, $property);

        return $this;
    }

    /**
     * @param $address
     * @param null $name
     * @param string $property
     * @return Mail
     */
    protected function addRecipient(array $address, $property = 'to'): Mail
    {
        if ($this->hasRecipient($address, $property)) {

            return $this;
        }

        $this->setAddress($address, $property);

        return $this;
    }

    /**
     * @param $address
     * @param null $name
     * @param string $property
     * @return bool
     */
    protected function hasRecipient($address, $property = self::FIELD_TO): bool
    {
        $key = array_keys($address)[0];

        $keys = array_keys($this->{$property});
        $values = array_values($this->{$property});

        if (is_string($key)) {

            $address = $key;

        } else {

            $address = $address[0];
        }

        return (in_array($address, $keys) || in_array($address, $values));
    }

    /**
     * @param array $addresses
     * @param string $property
     */
    protected function setAddress(array $addresses, $property = 'to')
    {
        foreach ($addresses as $key => $value) {

            if (is_string($key)) {

                $this->{$property}[$key] = $value;

            } else {

                $this->{$property}[] = $value;

            }
        }
    }

    /**
     * @return Manager
     */
    protected function getMailerInstance()
    {
        return \Phalcon\Di::getDefault()->getShared('mailer');
    }
}