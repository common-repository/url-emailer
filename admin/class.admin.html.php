<?php

class UETK_UrlEmailerAdminHTML
{
    public function adminPageHTML() { ?>
        <div class="wrap">
            <h1>URL Emailer</h1>
            <form action="options.php" method="POST"> <?php
                settings_fields( 'urlemailerplugin' );
                do_settings_sections( 'url-emailer-settings-page' );
                submit_button();
            ?>

            </form>
        </div>
    <?php }

    function textBoxHtml($args) { ?>
         <input type="<?php echo esc_attr($args['type']) ?>"
                size="40" 
                name="<?php echo esc_attr($args['name']) ?>" 
                id="<?php echo esc_attr($args['name']) ?>" 
                value="<?php echo esc_attr( get_option($args['name']) )?>">
                <p><em><?php echo $this->settingsFieldDescription($args['name']) ?></em></p></input>
    <?php }  

    function checkBoxHtml($args) { ?>
        <input type="checkbox"
               class="uep-ui-toggle" 
               id="<?php echo esc_attr($args['name']) ?>"
               name="<?php echo esc_attr($args['name']) ?>"
               value="1" <?php echo ((int) get_option($args['name']) === 1) ? 'checked' : '' ?>>
               <p><em><?php echo $this->settingsFieldDescription($args['name']) ?></em></p></input> 
    <?php }

    function settingsFieldDescription($name) {
        switch ($name) {
            case 'uep_from_name':
                return esc_html__('Email sender name. You can override this by using the following URL parameter', 'UrlEmailer') . ': <strong>&amp;fromname=</strong>';
            
            case 'uep_from_email':
                return esc_html__('Sender email address. You can use %site_admin_email% keyword and override this by using the following URL parameter', 'UrlEmailer') . ': <strong>&amp;fromemail=</strong>';

            case 'uep_bcc_email':
                return esc_html__('BCC recipient email address. You can use multiple addresses separated by comma', 'UrlEmailer');

            case 'uep_subject':
                return esc_html__('Email subject. You can use keywords and override this by using the following URL parameter', 'UrlEmailer') . ': <strong>&amp;subject=</strong>';

            case 'uep_pwd':
                return esc_html__('You can use a password to add extra security. In this case add the following URL parameter', 'UrlEmailer') . ': <strong>&amp;pwd=' . urlencode(get_option('uep_pwd')) . '</strong>';

            case 'uep_to_email':
                return esc_html__('Add recipient email address. You can use multiple addresses separated by comma and override this by using the following URL parameter', 'UrlEmailer') . ': <strong>&amp;mailto=</strong>';

            case 'uep_confirm_box':
                return esc_html__('Show email success or error message on yourpage', 'UrlEmailer');
    
            default:
                return '';
        }
    }

    function textAreaHtml($args) { ?>
        <textarea
               name="<?php echo esc_attr($args['name']) ?>" 
               id="<?php echo esc_attr($args['name']) ?>" 
               rows="10" class="uep_inline_textarea" 
               placeholder="<?php echo esc_attr($this->defaultBodyText()) ?>"
               ><?php echo esc_attr( get_option($args['name']) ) ?></textarea>
               <p><em>Email body text. You can use HTML and keywords</em></p>
   <?php }  

    function firstSectionDescription() { ?>
        <div id="poststuff" class="postbox">
            <div class="inside">
                <p><?php esc_html_e('With URL Emailer you can easily and quickly send emails when a web page is accessed. With the help of various URL parameters such as recipient, subject, etc., the email delivery can be customized. For a detailed description', 'UrlEmailer') . " "?> <a href="https://trader-tools.net/url-emailer-documentation/"> <?php esc_html_e('read the documentation', 'URLEmailer') ?></a></p>
                <p><?php esc_html_e('Available keywords', 'UrlEmailer') ?>: <strong>%mailto%, %site_admin_email%, %fromname%, %fromemail%, %subject%, %firstname%, %lastname%</strong></p>
                <p><?php esc_html_e('An example URL could be like', 'UrlEmailer') ?>: <em>https://yourdomain.com/yourpage/?mail=1&mailto=heisenberg%40mail.com&firstname=Walter&lastname=White&subject=New%20Deal</em></p>
                <p><?php esc_html_e('Do not forget to add the following shortcode to yourpage', 'UrlEmailer') ?>: [url-emailer]</p>
                <p><?php esc_html_e('If you like this plugin', 'UrlEmailer') . " " ?> <a href="https://wordpress.org/support/plugin/url-emailer/reviews"> <?php esc_html_e('please rate us', 'URLEmailer') ?></a> <img src="<?php echo plugins_url('/images/five-stars.png', __FILE__) ?>" alt="Five star rating" width="90" height="18" /></p>
                <?php do_meta_boxes('', 'normal', ''); ?>
            </div>
        </div>
    <?php }

    function defaultBodyText() {
        return 
            '<h2><strong>Hi there,</strong></h2>
            <p>the following details were submitted:</p>
            <p>Sender\'s email: <strong>%fromemail%</strong></p>
            <p>Sender\'s name: <strong>%fromname%</strong></p>
            <p>To email: <strong>%mailto%</strong></p>
            <p>To first name: <strong>%firstname%</strong></p>
            <p>To last name: <strong>%lastname%</strong></p>
            <p>Subject: <strong>%subject%</strong></p>';
}

}
