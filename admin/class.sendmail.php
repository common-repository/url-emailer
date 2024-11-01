<?php

class UETK_TKSendEmail
{
    private $fromName;
    private $fromEmail;
    private $toEmail;
    private $subject;
    private $bcc;
    private $message;

    public function sendEmail()
    {
        $eol = PHP_EOL;

        // main header
        $headers  = "From: $this->fromName <$this->fromEmail>".$eol;
        $headers .= "Bcc: ".$this->bcc.$eol;
        $headers .= "Content-Type: text/html; charset=\"utf-8\"".$eol;

        // send message
        return wp_mail($this->toEmail, $this->subject, $this->message, $headers);
    }

    public function setMailData($maildata)
    {
        $this->toEmail = isset($_GET['mailto']) ? sanitize_text_field($_GET['mailto']) : $maildata['to'];
        $this->subject = isset($_GET['subject']) ? sanitize_text_field($_GET['subject']) : $maildata['subject'];
        $this->bcc = $maildata['bcc'];
        $this->message = $maildata['message'];
        $this->fromName = $maildata['from_name'];
        $this->fromEmail = $maildata['from_email'];
        
        $this->subject = $this->replaceKeywords($this->subject);
        $this->fromEmail = $this->replaceKeywords($this->fromEmail);
        $this->message = $this->replaceKeywords($this->message);
    }

    public function verifyEmailSettings($body)
    {
        $sendemail = false;
        if (isset($_GET['mail']) AND sanitize_text_field($_GET['mail']) == '1')
            $sendemail = true;

        $pwd_match = true;
        if (isset($_GET['pwd']))
        {
            if (sanitize_text_field($_GET['pwd']) != get_option('uep_pwd'))
                $pwd_match = false;
        }

        if ($sendemail AND $pwd_match AND is_main_query())
        {
            $mailsettings = array(
                'to' => get_option( 'uep_to_email', '%mailto%' ),
                'subject' => get_option( 'uep_subject', 'Email subject' ),
                'bcc' => get_option( 'uep_bcc_email' ),
                'message' => get_option( 'uep_body', $body),
                'from_name' => get_option( 'uep_from_name' ),
                'from_email' => get_option( 'uep_from_email',  '%site_admin_email%'),
            );

            $this->setMailData($mailsettings);
            return $this->sendEmail();
        }
    }

    private function replaceKeywords($content)
    {
        $content = str_replace("%site_admin_email%", get_option('admin_email'), $content);
        $content = str_replace("%subject%", sanitize_text_field($_GET['subject']), $content);
        $content = str_replace("%firstname%", sanitize_text_field($_GET['firstname']), $content);
        $content = str_replace("%lastname%", sanitize_text_field($_GET['lastname']), $content);
        $content = str_replace("%mailto%", sanitize_email($_GET['mailto']), $content);
        $content = str_replace("%fromemail%", $this->fromEmail, $content);
        $content = str_replace("%fromname%", $this->fromName, $content);
        return $content;
    }    
}
