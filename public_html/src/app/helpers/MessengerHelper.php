<?php

namespace helpers;

class MessengerHelper {

    private \Tina4\MessengerSettings $messengerSettings;

    /**
     * Constructor for the helper
     */
    public function __construct() {
        //Get the mail setting from the environment
        $this->messengerSettings = new \Tina4\MessengerSettings(true); //Use PHPMailer
        $this->messengerSettings->smtpServer = $_ENV["MAILSERVER_HOST"];
        $this->messengerSettings->smtpUsername = $_ENV["MAILSERVER_USERNAME"];
        $this->messengerSettings->smtpPassword = $_ENV["MAILSERVER_PASSWORD"];
        $this->messengerSettings->smtpPort = $_ENV["MAILSERVER_PORT"];
    }

    /**
     * Sends an email. Refer to the Messenger.php class for formatting of email addresses
     */
    public function sendMail($toAddresses, $subject, $message) {
        try {
            $mail = new \Tina4\Messenger($this->messengerSettings);

            $result = $mail->sendEmail([$toAddresses], $subject, $message, $_ENV["MAIL_FROM_NAME"], $_ENV["MAIL_FROM_ADDRESS"]);

            return $result;
        }
        catch (\Exception $exception) {
            \Tina4\Debug::message("Failed to send email: " . $exception->getMessage());
            return false;
        }
    }
}