<?php

class EmailSender
{
    private $mailer;

    public function __construct($smtpSettings = null)
    {
        include_once Helpers . 'mail/PHPMailerAutoload.php';
        $this->mailer = new PHPMailer(true); // true parametresi hata raporlamasını etkinleştirir

        if ($smtpSettings === null) {
            $smtpSettings = $this->getDefaultSMTPSettings();
        }

        $this->initialize($smtpSettings);
    }

    private function initialize($smtpSettings)
    {
        $this->mailer->isSMTP();
        $this->mailer->SetLanguage("tr", "language");
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->SMTPAuth = true;
        $this->mailer->IsHTML(true);

        $this->mailer->Host = $smtpSettings['Host'];
        $this->mailer->Username = $smtpSettings['Username'];
        $this->mailer->Password = $smtpSettings['Password'];
        $this->mailer->SMTPSecure = $smtpSettings['SMTPSecure'];
        $this->mailer->Port = $smtpSettings['Port'];

        $this->mailer->setFrom($smtpSettings['FromEmail'], $smtpSettings['FromName']);
    }

    private function getDefaultSMTPSettings()
    {
        $configPath = CONF . '/SMTPSettings.json';
        $config = file_get_contents($configPath);
        return json_decode($config, true);
    }

    public function sendEmail($recipientEmail, $recipientName, $subject, $message)
    {
        try {
            $this->mailer->addAddress($recipientEmail, $recipientName);
            $this->mailer->Subject = $subject;
            $this->mailer->MsgHTML($message);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            Log::write($e->getMessage(), "error");
            return false;
        } finally {
            $this->mailer->clearAddresses();
        }
    }
}