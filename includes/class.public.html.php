<?php

class UETK_UrlEmailerPublicHtml
{
    function confirmBoxHtml($content, $sendmailValue)
    {
        if ($sendmailValue) { 
            $html = '<div class="success">'. esc_html__('The email has been send successfully', 'UrlEmailer') . '</div>';
            return $html . $content;
        }
        return '<div class="failed">'. esc_html__('Sorry something went wrong. Could not send email', 'UrlEmailer') . '</div>' . $content;
    }
}