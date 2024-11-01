<?php
/**
 * @package url-emailer
 * @version 1.0.0
 */
/*
Plugin Name: URL Emailer
Plugin URI: https://wordpress.org/plugins/url-emailer
Description: With URL Emailer you can easily and quickly send emails when a web page is accessed or loaded. With the help of various URL parameters such as recipient, subject, etc., the email delivery can be fully customized.
Author: Timo Kosiol
Version: 1.0.0
Author URI:  https://trader-tools.net/url-emailer-documentation/
Text Domain: UrlEmailer
Domain Path: /languages
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.txt
*/

defined( 'ABSPATH' ) or die; 

require_once(__DIR__ . '/admin/class.admin.html.php');
require_once(__DIR__ . '/admin/class.sendmail.php');
require_once(__DIR__ . '/includes/class.public.html.php');

if ( !class_exists( 'UETK_UrlEmailer' ) )
{
    class UETK_UrlEmailer
    {
        public $adminHtml;
        public $publicHtml;
        private $sendmailValue;


        function __construct() {
            $this->adminHtml = new UETK_UrlEmailerAdminHTML();
            $this->publicHtml = new UETK_UrlEmailerPublicHtml();

            add_action('admin_menu', array($this, 'adminPage'));
            add_action( 'admin_init', array($this,'settings' ));
            add_shortcode( 'url-emailer', array($this, 'sendEmail') );
            add_action( 'admin_enqueue_scripts', array($this, 'load_admin_style') );
            add_action( 'wp_enqueue_scripts', array($this, 'load_frontend_style') );
            add_filter( 'the_content', array($this, 'showConfirmation') );
            add_action('init', array($this, 'languages'));

        }

        function languages() {
            load_plugin_textdomain('UrlEmailer', false, dirname(plugin_basename(__FILE__)) . '/languages');
        }

        function settings() {
            add_settings_section( 'uep_first_section', esc_html__('Basic settings', 'UrlEmailer'), array($this->adminHtml, 'firstSectionDescription'), 'url-emailer-settings-page');
            
            add_settings_field( 'uep_from_name', esc_html__('From name', 'UrlEmailer'), array($this->adminHtml, 'textBoxHtml'), 'url-emailer-settings-page', 'uep_first_section', array('name' => 'uep_from_name', 'type' => 'text'));
            register_setting( 'urlemailerplugin', 'uep_from_name', array('sanatize_callback' => 'sanatize_text_field', 'default' => '') );

            add_settings_field( 'uep_from_email', esc_html__('From email', 'UrlEmailer'), array($this->adminHtml, 'textBoxHtml'), 'url-emailer-settings-page', 'uep_first_section', array('name' => 'uep_from_email', 'type' => 'text'));
            register_setting( 'urlemailerplugin', 'uep_from_email', array('sanatize_callback' => 'sanatize_text_field', 'default' => '%site_admin_email%') );

            add_settings_field( 'uep_to_email', esc_html__('To email', 'UrlEmailer'), array($this->adminHtml, 'textBoxHtml'), 'url-emailer-settings-page', 'uep_first_section', array('name' => 'uep_to_email', 'type' => 'text'));
            register_setting( 'urlemailerplugin', 'uep_to_email', array('sanatize_callback' => 'sanatize_text_field', 'default' => '%mailto%') );

            add_settings_field( 'uep_bcc_email', esc_html__('BCC email', 'UrlEmailer'), array($this->adminHtml, 'textBoxHtml'), 'url-emailer-settings-page', 'uep_first_section', array('name' => 'uep_bcc_email', 'type' => 'text'));
            register_setting( 'urlemailerplugin', 'uep_bcc_email', array('sanatize_callback' => 'sanatize_text_field', 'default' => '') );

            add_settings_field( 'uep_subject', esc_html__('Subject', 'UrlEmailer'), array($this->adminHtml, 'textBoxHtml'), 'url-emailer-settings-page', 'uep_first_section', array('name' => 'uep_subject', 'type' => 'text'));
            register_setting( 'urlemailerplugin', 'uep_subject', array('sanatize_callback' => 'sanatize_text_field', 'default' => 'Email subject') );

            add_settings_field( 'uep_body', esc_html__('Message', 'UrlEmailer'), array($this->adminHtml, 'textAreaHtml'), 'url-emailer-settings-page', 'uep_first_section', array('name' => 'uep_body'));
            register_setting( 'urlemailerplugin', 'uep_body', array('sanatize_callback' => 'sanatize_text_field', 'default' => '') );

            add_settings_field( 'uep_pwd', esc_html__('Password', 'UrlEmailer'), array($this->adminHtml, 'textBoxHtml'), 'url-emailer-settings-page', 'uep_first_section', array('name' => 'uep_pwd', 'type' => 'text'));
            register_setting( 'urlemailerplugin', 'uep_pwd', array('sanatize_callback' => 'sanatize_text_field', 'default' => '') );

            add_settings_field( 'uep_confirm_box', esc_html__('Confirmation box', 'UrlEmailer'), array($this->adminHtml, 'checkBoxHtml'), 'url-emailer-settings-page', 'uep_first_section', array('name' => 'uep_confirm_box', 'type' => 'checkbox'));
            register_setting( 'urlemailerplugin', 'uep_confirm_box', array('sanatize_callback' => 'sanatize_text_field', 'default' => $this->adminHtml->defaultBodyText()) );
        }

        function adminPage() {
            add_options_page( esc_html__('URL Emailer Settings', 'UrlEmailer'), 'URL Emailer', 'manage_options', 'url-emailer-settings-page', array($this->adminHtml, 'adminPageHTML'));
        }

        function load_admin_style($hook) {
            $css_file = plugins_url('/admin/css/toggle.css', __FILE__);
            $admin_css = plugins_url('/admin/css/uep_admin_styles.css', __FILE__);
            if ( 'settings_page_url-emailer-settings-page' != $hook )
                return;
            wp_enqueue_style( 'toggle_css', $css_file, false, '1.0.0', 'all' );
            wp_enqueue_style( 'admin_css', $admin_css, false, '1.0.0', 'all' );
        }

        function load_frontend_style() {
            $css_file = plugins_url('/public/css/style.css', __FILE__);
            wp_enqueue_style( 'uep_css', $css_file, false, '1.0.0', 'all' );
        }

        public function sendEmail() {
            if (isset($_GET['mail']) AND sanitize_text_field($_GET['mail']) == '1')
            {
                $tkmailer = new UETK_TKSendEmail();
                $sendmailValue = $tkmailer->verifyEmailSettings($this->adminHtml->defaultBodyText());            
                $permalink = get_permalink( $post->ID ) . '?uep_success=' .  ($sendmailValue ? '1' : '0');
                wp_redirect( $permalink );
                exit;
            }
        }

        function showConfirmation($content) {
            
            if (is_main_query() AND (is_single() OR is_page()) AND
                get_option( 'uep_confirm_box', '0' ) AND
                isset($_GET['uep_success'])) {
                    return $this->publicHtml->confirmBoxHtml($content, $_GET['uep_success'] == '1' ? true : false); 
            }
            return $content;
        }

        
    }        
}

$urlEmailer = new UETK_UrlEmailer();