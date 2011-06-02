# Ciko Email Notifier

This is a module for the Ciko continuous integration server. It sends emails using swiftmailer when builds fail.

## Use

To use, add a notifier to your config like so:

	notifiers(
		array(
			'email' => new Notifier_Email(
				array(
					'contractfrombelow@gmail.com',
					'foo@bar.com' => 'Testing Name',
				)
			),
		)
	)

Also remember to specify the mailer config in config/email.php. If you already have one in your application folder (from shadowhand's email module), it will "just work".