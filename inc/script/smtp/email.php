<?php

//namespace Snipworks\Smtp;

/**
 * Send email class using SMTP Authentication
 *
 * @class Email
 * @package Snipworks\SMTP
 */
class Email
{
    const CRLF = "\r\n";
    const TLS = 'tcp';
    const SSL = 'ssl';
    const OK = 250;

    /** @var string $server */
    protected $server;

    /** @var string $hostname */
    protected $hostname;

    /** @var int $port */
    protected $port;

    /** @var resource $socket */
    protected $socket;

    /** @var string $username */
    protected $username;

    /** @var string $password */
    protected $password;

    /** @var int $connectionTimeout */
    protected $connectionTimeout;

    /** @var int $responseTimeout */
    protected $responseTimeout;

    /** @var string $subject */
    protected $subject;

    /** @var array $to */
    protected $to = array();

    /** @var array $cc */
    protected $cc = array();

    /** @var array $bcc */
    protected $bcc = array();

    /** @var array $from */
    protected $from = array();

    /** @var array $replyTo */
    protected $replyTo = array();

    /** @var array $attachments */
    protected $attachments = array();

    /** @var string|null $protocol */
    protected $protocol = null;

    /** @var string|null $textMessage */
    protected $textMessage = null;

    /** @var string|null $htmlMessage */
    protected $htmlMessage = null;

    /** @var bool $isHTML */
    protected $isHTML = false;

    /** @var bool $isTLS */
    protected $isTLS = false;

    /** @var array $logs */
    protected $logs = array();

    /** @var string $charset */
    protected $charset = 'utf-8';

    /** @var array $headers */
    protected $headers = array(
        'MIME-Version' => '1.0',
    );

    /**
     * Class constructor
     *  -- Set server name, port and timeout values
     *
     * @param string $server
     * @param int $port
     * @param int $connectionTimeout
     * @param int $responseTimeout
     * @param string|null $hostname
     */
    public function __construct($server, $port = 25, $connectionTimeout = 30, $responseTimeout = 8, $hostname = null)
    {
        $this->port = $port;
        $this->server = $server;
        $this->connectionTimeout = $connectionTimeout;
        $this->responseTimeout = $responseTimeout;
        $this->hostname = empty($hostname) ? gethostname() : $hostname;
    }

    /**
     * Add to recipient email address
     *
     * @param string $address
     * @param string|null $name
     * @return Email
     */
    public function addTo($address, $name = null)
    {
        $this->to[] = array($address, $name);

        return $this;
    }

    /**
     * Add carbon copy email address
     *
     * @param string $address
     * @param string|null $name
     * @return Email
     */
    public function addCc($address, $name = null)
    {
        $this->cc[] = array($address, $name);

        return $this;
    }

    /**
     * Add blind carbon copy email address
     *
     * @param string $address
     * @param string|null $name
     * @return Email
     */
    public function addBcc($address, $name = null)
    {
        $this->bcc[] = array($address, $name);

        return $this;
    }

    /**
     * Add email reply to address
     *
     * @param string $address
     * @param string|null $name
     * @return Email
     */
    public function addReplyTo($address, $name = null)
    {
        $this->replyTo[] = array($address, $name);

        return $this;
    }

    /**
     * Add file attachment
     *
     * @param string $attachment
     * @return Email
     */
    public function addAttachment($attachment)
    {
        if (file_exists($attachment)) {
            $this->attachments[] = $attachment;
        }

        return $this;
    }

    /**
     * Set SMTP Login authentication
     *
     * @param string $username
     * @param string $password
     * @return Email
     */
    public function setLogin($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * Get message character set
     *
     * @param string $charset
     * @return Email
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Set SMTP Server protocol
     * -- default value is null (no secure protocol)
     *
     * @param string $protocol
     * @return Email
     */
    public function setProtocol($protocol = null)
    {
        if ($protocol === self::TLS) {
            $this->isTLS = true;
        }

        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Set from email address and/or name
     *
     * @param string $address
     * @param string|null $name
     * @return Email
     */
    public function setFrom($address, $name = null)
    {
        $this->from = array($address, $name);

        return $this;
    }

    /**
     * Set email subject string
     *
     * @param string $subject
     * @return Email
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set plain text message body
     *
     * @param string $message
     * @return Email
     */
    public function setTextMessage($message)
    {
        $this->textMessage = $message;

        return $this;
    }

    /**
     * Set html message body
     *
     * @param string $message
     * @return Email
     */
    public function setHtmlMessage($message)
    {
        $this->htmlMessage = $message;

        return $this;
    }

    /**
     * Get log array
     * -- contains commands and responses from SMTP server
     *
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Send email to recipient via mail server
     *
     * @return bool
     */
    public function send()
    {
        $this->socket = fsockopen($this->getServer(), $this->port, $error_number, $error_string,
            $this->connectionTimeout);
        if (empty($this->socket)) {
            return false;
        }

        $this->logs['CONNECTION'] = $this->getResponse();
        $this->logs['HELLO'][1] = $this->sendCommand('EHLO ' . $this->hostname);

        if ($this->isTLS) {
            $this->logs['STARTTLS'] = $this->sendCommand('STARTTLS');
            stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->logs['HELLO'][2] = $this->sendCommand('EHLO ' . $this->hostname);
        }

        $this->logs['AUTH'] = $this->sendCommand('AUTH LOGIN');
        $this->logs['USERNAME'] = $this->sendCommand(base64_encode($this->username));
        $this->logs['PASSWORD'] = $this->sendCommand(base64_encode($this->password));
        $this->logs['MAIL_FROM'] = $this->sendCommand('MAIL FROM: <' . $this->from[0] . '>');

        $recipients = array_merge($this->to, $this->cc);
        foreach ($recipients as $address) {
            $this->logs['RECIPIENTS'][] = $this->sendCommand('RCPT TO: <' . $address[0] . '>');
        }

        $this->headers['Date'] = date('r');
        $this->headers['Subject'] = $this->subject;
        $this->headers['From'] = $this->formatAddress($this->from);
        $this->headers['To'] = $this->formatAddressList($this->to);
        if (!empty($this->cc)) {
            $this->headers['Cc'] = $this->formatAddressList($this->cc);
        }

        if (!empty($this->bcc)) {
            $this->headers['Bcc'] = $this->formatAddressList($this->bcc);
        }

        if (!empty($this->replyTo)) {
            $this->headers['Reply-To'] = $this->formatAddressList($this->replyTo);
        }

        $this->logs['DATA'][1] = $this->sendCommand('DATA');

        $boundary = md5(uniqid(microtime(true), true));
        $this->headers['Content-Type'] = 'multipart/mixed; boundary="mixed-' . $boundary . '"';

        $message = '--mixed-' . $boundary . self::CRLF;
        $message .= 'Content-Type: multipart/alternative; boundary="alt-' . $boundary . '"' . self::CRLF . self::CRLF;

        if (!empty($this->textMessage)) {
            $message .= '--alt-' . $boundary . self::CRLF;
            $message .= 'Content-Type: text/plain; charset=' . $this->charset . self::CRLF;
            $message .= 'Content-Transfer-Encoding: base64' . self::CRLF . self::CRLF;
            $message .= chunk_split(base64_encode($this->textMessage)) . self::CRLF;
        }

        if (!empty($this->htmlMessage)) {
            $message .= '--alt-' . $boundary . self::CRLF;
            $message .= 'Content-Type: text/html; charset=' . $this->charset . self::CRLF;
            $message .= 'Content-Transfer-Encoding: base64' . self::CRLF . self::CRLF;
            $message .= chunk_split(base64_encode($this->htmlMessage)) . self::CRLF;
        }

        $message .= '--alt-' . $boundary . '--' . self::CRLF . self::CRLF;

        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $filename = pathinfo($attachment, PATHINFO_BASENAME);
                $contents = file_get_contents($attachment);

                $message .= '--mixed-' . $boundary . self::CRLF;
                $message .= 'Content-Type: application/octet-stream; name="' . $filename . '"' . self::CRLF;
                $message .= 'Content-Disposition: attachment; filename="' . $filename . '"' . self::CRLF;
                $message .= 'Content-Transfer-Encoding: base64' . self::CRLF . self::CRLF;
                $message .= chunk_split(base64_encode($contents)) . self::CRLF;
            }
        }

        $message .= '--mixed-' . $boundary . '--';

        $headers = '';
        foreach ($this->headers as $k => $v) {
            $headers .= $k . ': ' . $v . self::CRLF;
        }

        $this->logs['MESSAGE'] = $message;
        $this->logs['HEADERS'] = $headers;
        $this->logs['DATA'][2] = $this->sendCommand($headers . self::CRLF . $message . self::CRLF . '.');
        $this->logs['QUIT'] = $this->sendCommand('QUIT');
        fclose($this->socket);

        return substr($this->logs['DATA'][2], 0, 3) == self::OK;
    }

    /**
     * Get server url
     * -- if set SMTP protocol then prepend it to server
     *
     * @return string
     */
    protected function getServer()
    {
        return ($this->protocol) ? $this->protocol . '://' . $this->server : $this->server;
    }

    /**
     * Get Mail Server response
     * @return string
     */
    protected function getResponse()
    {
        $response = '';
        stream_set_timeout($this->socket, $this->responseTimeout);
        while (($line = fgets($this->socket, 515)) !== false) {
            $response .= trim($line) . "\n";
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }

        return trim($response);
    }

    /**
     * Send command to mail server
     *
     * @param string $command
     * @return string
     */
    protected function sendCommand($command)
    {
        fputs($this->socket, $command . self::CRLF);

        return $this->getResponse();
    }

    /**
     * Format email address (with name)
     *
     * @param array $address
     * @return string
     */
    protected function formatAddress($address)
    {
        return (empty($address[1])) ? $address[0] : '"' . $address[1] . '" <' . $address[0] . '>';
    }

    /**
     * Format email address to list
     *
     * @param array $addresses
     * @return string
     */
    protected function formatAddressList(array $addresses)
    {
        $data = array();
        $delimiter = ', ' . self::CRLF;

        foreach ($addresses as $address) {
            $data[] = $this->formatAddress($address);
        }

        return implode($delimiter, $data);
    }
}
