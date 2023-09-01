<?php

/**
    Plugin Name: Clickskeks
    description: Integrates the Clickskeks DSGVO solution into WordPress
    Version: 1.3.5
    Author: Papoo Software &amp; Media GmbH
    Author URI: https://papoo-media.de
    License: GPLv2 or later
    Text Domain: ccm19-integration

    Copyright (C) 2020 Papoo Software & Media GmbH

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

class CKeksScriptInserter {
    
    protected $CKeksScriptKey = '';

    public function __construct() {
        
        $this->CKeksScriptKey = get_option('ckeks_script_key');


	    if( $this->CKeksScriptKey && ( !is_admin() && !$this->ckeks_is_login_page() ) )
        {
            if( $this -> ckeks_check_snippet( $this -> CKeksScriptKey ) ) {
	            add_action( 'init', [ $this, 'ckeks_enqueue_my_scripts' ], - 999 );
            }else
            {
                add_action('wp_head', [$this , 'ckeks_print_ccm_script'], -10);
            }
        }
        add_action( 'admin_menu', [ $this, 'ckeks_create_plugin_settings_page' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'ckeks_enqueue_my_admin_scripts' ] );
        
        add_shortcode('clickskeks', [ $this, 'ckeks_shortcode_cookietable' ]);
    }
    
    public function ckeks_is_login_page() {
        return in_array( $GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php') );
    }

    public function ckeks_enqueue_my_scripts() {
        wp_enqueue_script('keks', 'https://static.clickskeks.at/'.$this->CKeksScriptKey.'/bundle.js' );
    }

	/**
	 * @return void
     *
     * prints the ccm19 script into the header of the website
	 */
    public function ckeks_print_ccm_script() {

        $ccmTag = $this->get_integration_url($this->CKeksScriptKey);
        if ( $ccmTag ) {

		    wp_print_script_tag( [
			    'src'            => $ccmTag,
			    'referrerpolicy' => 'origin'
		    ] );

	    }else{
	        ?>
            <div class="error" style="margin-left: 0">
                <p><?php __('Der eingegebene CCM19 Code-Schnipsel war leider falsch. Bitte versuchen Sie es erneut oder wenden sich an den Support.', 'clickskeks'); ?></p>
            </div> <?php
        }
    }

    public function ckeks_enqueue_my_admin_scripts() {
        wp_enqueue_script('keks_admin', plugins_url( 'js/ckeks_admin.js', __FILE__ ) );
        wp_enqueue_style('keks', plugins_url( 'keks.css', __FILE__ ) );
    }

    public function ckeks_shortcode_cookietable() {
        return '<script id="clickskeks-disclaimer-script" src="https://static.clickskeks.at/'.$this->CKeksScriptKey.'/disclaimer.js" type="application/javascript"></script>';
    }
    
    public function ckeks_create_plugin_settings_page() {
        // Add the menu item and page
        $page_title = 'Clickskeks';
        $menu_title = 'Clickskeks';
        $capability = 'manage_options';
        $slug = 'clickskeks';
        $callback = array( $this, 'ckeks_plugin_settings_page_content' );
        $icon = 'dashicons-keks';
        $position = 100;
        
        add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    }
    
    public function ckeks_plugin_settings_page_content() {
        
        if( (isset($_POST['updated'])) && ($_POST['updated'] === 'true') ){
            $this->ckeks_handle_form();
        }
        
        ?>
        <div class="wrap clickskeks" style="background: white; padding: 1rem">
            <img src="<?php echo plugins_url( 'img/logo.png', __FILE__ ); ?>" style="width: 220px;">
            
            <form method="POST" id="ckeksform" name="ckeksform">
                <input type="hidden" name="updated" value="true" />
                <input type="hidden" name="submit_type"/>
                <?php wp_nonce_field( 'script_update', 'script_add_form' ); ?>
                <br/>
                <strong style="font-size: 1rem"><?php _e('clickskeks - gemeinsam gegen DSGVO Strafen', 'clickskeks'); ?></strong>
                <p><?php _e('Das clickskeks Cookie-Management-Plugin aus Österreich gibt dir die volle Kontrolle über Cookies und Tracker auf deiner Website. Nach einem Erst-scan deiner Website werden Cookies identifiziert und dein DSGVO-konformer Cookie-Banner erstellt, welcher deinen Usern die gesetzlich vorgeschriebene aktive Einwilligung zu Cookies ermöglicht.', 'clickskeks'); ?></p>
                <p><?php _e('Clickskeks überprüft daraufhin regelmäßig und automatisch deine Seite auf neue oder veränderte Cookies und informiert dich, wenn Anpassungen notwendig sind. Du kannst clickskeks 30 Tage kostenlos testen und erhältst anschließend dein Cookie-Tool ab nur 9,90 EUR im Monat.', 'clickskeks'); ?></p>
                <p><?php _e('Deinen 30 Tage Test kannst du auf <a href="https://www.clickskeks.at/">clickskeks.at</a> bekommen!', 'clickskeks'); ?></p>
                <p><?php _e('Bei Fragen wende dich bitte an <a href="mailto:hallo@clickskeks.at">hallo@clickskeks.at</a>', 'clickskeks'); ?></p>
                
                <h2 style="margin-top:40px;font-size: 1rem"><?php _e('Cookie-Einbindung in deine Datenschutzerklärung', 'clickskeks'); ?></h2>
                <p><?php _e('Damit deine Website DSGVO-konform ist, musst du die Cookies auch in deiner Datenschutzerklärung anführen. <br/>Gehe dazu auf deine Datenschutz-Seite zu dem Abschnitt "Cookies" und füge hier den Shortcode <b style="color:#000;">[clickskeks]</b> ein. Speichere die Änderungen und schon werden deine gesetzten Cookies als Tabelle angezeigt.', 'clickskeks'); ?></p>
                <img src="<?php echo plugins_url( 'img/screenshot.png', __FILE__ ); ?>" style="display:block;max-width:100%;height:auto;margin:0 0 40px;padding:10px;border:1px solid #7e8993;" width="634" height="141" />
    
                <label for="ckeks_script_key"><strong><?php _e('Bitte geben Sie hier Ihren clickskeks oder CCM19 Code ein!', 'clickskeks'); ?></strong></label>
                <br/>
                <textarea name="ckeks_script_key" id="ckeks_script_key" cols="100" rows="2" placeholder='<script src="http://Beispiel/ccm19.js?apiKey=1234&domain=1234"
          referrerpolicy="origin"></script> oder 5f97f8ca-704f-45a2-9627-a85ca89e3ff4'><?php echo get_option('ckeks_script_key'); ?></textarea>
                <input type="submit" name="submit_code" id="submit_code" class="keks-btn" value="Code speichern">
                <input type="submit" name="reset" id="reset" class="keks-btn" value="Zurücksetzen">
                <br/>
                <br/>
            </form>
        </div> <?php
    }

    public function ckeks_handle_form() {
        if( ! isset( $_POST['script_add_form'] ) || ! wp_verify_nonce( $_POST['script_add_form'], 'script_update' )
        ){ ?>
            <div class="error" style="margin-left: 0">
                <p><?php __('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.', 'clickskeks'); ?></p>
            </div> <?php
            exit;
        } else {

            $responseText = '';

            if( isset( $_POST['submit_type']) && $_POST['submit_type'] == 'reset' )
            {
                $scriptKey = '';
                $responseText = 'Erfolgreich zurückgesetzt.';
            } elseif( isset( $_POST['submit_type'] ) && $_POST['submit_type'] == 'submit' )
            {
                //stripslashes_deep is crucial because of legacy wp core magic quotes adding slashes
                $scriptKey = stripslashes_deep( $_POST['ckeks_script_key'] );
                if(!empty($scriptKey)){
	                $responseText = 'Der Code wurde erfolgreich gespeichert.';
                }else{
                    $responseText = 'Bitte Code-Schnipsel eingeben.';
                }
            }

            update_option( 'ckeks_script_key', $scriptKey );
            ?>
            <div class="updated" style="margin-left: 0">
                <p> <?php echo _e($responseText); ?> </p>
            </div> <?php
        }
    }

    /** decides if input is ccm or ckekks */
    public function ckeks_check_snippet($scriptKey) {
	    return (bool)preg_match('~^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$~', $scriptKey);
    }

    /** checks if ccm19 snippet is correct */
    private function get_integration_url($scriptKey)
	{

		if ( ! empty( $scriptKey ) ) {
			$match = [];
			preg_match( '/\bsrc=([\'"])((?>[^"\'?#]|(?!\1)["\'])*\/(ccm19|app)\.js\?(?>[^"\']|(?!\1).)*)\1/i', $scriptKey, $match );
			if ( $match and $match[2] ) {
				return html_entity_decode( $match[2], ENT_HTML401 | ENT_QUOTES, 'UTF-8' );
			}
		}

		return null;
	}
}
new CKeksScriptInserter();
