<?php
/**
 * Class for sending status emails for failures
 *
 * @package    Ciko
 * @author     Jeremy Bush
 * @copyright  (c) 2011 Jeremy Bush
 * @license    http://github.com/zombor/Ciko/raw/develop/LICENSE
 */
class Post_Runner_Email implements Notifier
{
	protected $_emails = array();

	/**
	 * Constructor to assign emails for sending
	 *
	 * @return null
	 */
	public function __construct(array $emails)
	{
		parent::__construct();

		$this->_emails = $emails;

		if ( ! class_exists('Swift_Message'))
		{
			require Kohana::find_file(
				'vendor', 'swiftmailer/lib/swift_required'
			);
		}
	}

	/**
	 * Sends an email if the project build has failed
	 * 
	 * @param Model_Ciko_Project $project the project to notify with
	 * @param bool               $status  if the job ran successfully
	 *
	 * @return bool
	 */
	public function execute(Model_Ciko_Project $project, $status)
	{
		// Send emails on failure
		if ( ! $status)
		{
			$mailer = $this->_swift();

			$message = Swift_Message::newInstance()->setCharset(
				Kohana::$charset
			);

			foreach ($this->_emails as $key => $value)
			{
				if (ctype_digit((string) $key))
				{
					$message->addTo($value);
				}
				else
				{
					$message->addTo($key, $value);
				}
			}

			$view = new View_Ciko_Email_Falure;
			$view->project = $project;

			$message->setBody($view);

			$mailer->send($message);
		}
	}

	/**
	 * Method to get a swiftmailer instance
	 * 
	 * @see https://github.com/shadowhand/email/tree/32ca8af13a2558ae5c535002e4280efcd726d4c9
	 *
	 * @return object swiftmailer object
	 */
	protected function _swift()
	{
		// Load email configuration, make sure minimum defaults are set
		$config = Kohana::config('email')->as_array() + array(
			'driver'  => 'native',
			'options' => array(),
		);

		// Extract configured options
		extract($config, EXTR_SKIP);

		if ($driver === 'smtp')
		{
			// Create SMTP transport
			$transport = Swift_SmtpTransport::newInstance($options['hostname']);

			if (isset($options['port']))
			{
				// Set custom port number
				$transport->setPort($options['port']);
			}

			if (isset($options['encryption']))
			{
				// Set encryption
				$transport->setEncryption($options['encryption']);
			}

			if (isset($options['username']))
			{
				// Require authentication, username
				$transport->setUsername($options['username']);
			}

			if (isset($options['password']))
			{
				// Require authentication, password
				$transport->setPassword($options['password']);
			}

			if (isset($options['timeout']))
			{
				// Use custom timeout setting
				$transport->setTimeout($options['timeout']);
			}
		}
		elseif ($driver === 'sendmail')
		{
			// Create sendmail transport
			$transport = Swift_SendmailTransport::newInstance();

			if (isset($options['command']))
			{
				// Use custom sendmail command
				$transport->setCommand($options['command']);
			}
		}
		else
		{
			// Create native transport
			$transport = Swift_MailTransport::newInstance();

			if (isset($options['params']))
			{
				// Set extra parameters for mail()
				$transport->setExtraParams($options['params']);
			}
		}

		// Create the SwiftMailer instance
		return Swift_Mailer::newInstance($transport);
	}
}