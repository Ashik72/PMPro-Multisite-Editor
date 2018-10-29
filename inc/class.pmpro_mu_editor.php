<?php

if(!defined('WPINC')) // MUST have WordPress.
    exit('Do NOT access this file directly: '.basename(__FILE__));

/**
 * pmpro_mu_editor
 */
class pmpro_mu_editor
{

    private static $instance;
    public static $titan_data;


    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    function __construct()  {
        add_action( 'wp_enqueue_scripts', array($this, 'load_custom_wp_frontend_style'), 50 );

        add_filter('editable_roles', [$this, 'editable_roles'], 10, 1);

        add_action( 'pmpro_after_checkout', [$this, 'pmproap_pmpro_after_checkout'], 1000 );

        add_shortcode('buy_editor_access', [$this, 'buy_editor_access'], 1000);

        remove_filter('pmpro_has_membership_access_filter', 'pmproap_pmpro_has_membership_access_filter', 10);

        add_filter('get_post_metadata', [$this, 'get_post_metadata'], 10, 4);

        add_filter('pmpro_international_addresses', [$this, 'add_hidden_quantity'], 1000, 1);

        ////

        add_action('template_redirect', function () {

            if (empty($_GET['d'])) return;
            $get_quantity = get_user_meta(get_current_user_id(), 'set_editor_quantity', true);

            var_dump($get_quantity);
            wp_die();
            return;
            Kint::dump(get_user_meta(get_current_user_id()));

           //update_user_meta(get_current_user_id(), '_pmproap_posts', []);


           $post_users = get_post_meta( 14, '_pmproap_users', true );

           update_post_meta( 14, '_pmproap_users', [] );


            Kint::dump($post_users);


            Kint::dump(get_user_meta(get_current_user_id(), '_pmproap_posts', true));



            wp_die();
        });
    }

    public function add_hidden_quantity($result) {
        echo '<input id="quantity" name="quantity" value="'.$_POST['quantity'].'" type="hidden">';
        return $result;
    }

    public function load_custom_wp_frontend_style() {
            wp_enqueue_style( 'pmpro_mu_editor-style-css', pmpro_mu_editor_PLUGIN_URL.'css/custom.css' );
        }


    public function get_post_metadata($null, $object_id, $meta_key, $single) {

        if ($meta_key != '_pmproap_price') return $null;

        global $wpdb;

        $_pmproap_price = $wpdb->get_var( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = {$object_id} AND meta_key = '_pmproap_price' LIMIT 1" );

        return doubleval($_pmproap_price) * intval($_POST['quantity']);
    }

    public function buy_editor_access($atts) {
        $atts = shortcode_atts( array(
            'price' => 0.00,
            'set_quantity' => TRUE
        ), $atts, 'buy_editor_access' );

        global $post;

        $page_id = get_the_ID();


        if (!function_exists('pmproap_getLevelIDForCheckoutLink')) die('Contact support!');
        $user_id = get_current_user_id();
        $text_level_id = pmproap_getLevelIDForCheckoutLink($page_id, $user_id);

        ob_start();



        ?>

        <form action="<?php _e(pmpro_url( 'checkout', '?level=' . $text_level_id . '&ap=' . $post->ID )); ?>" method="post">

            <input type="number" name="quantity" min="1" value="1">

            <input type="submit" value="<?php _e( 'Click here to checkout', 'pmproap' ) ?>">
        </form>

        <?php

        $output = ob_get_clean();

        return $output;

    }


    public static function pmproap_pmpro_after_checkout($user_id)
    {


        $get_quantity = get_user_meta($user_id, 'set_editor_quantity', true);
        $quantity_tot = intval($_REQUEST['quantity']) + intval($get_quantity);
        update_user_meta($user_id, 'set_editor_quantity', $quantity_tot);

        //file_put_contents(pmpro_mu_editor_PLUGIN_DIR."data-".time().".txt", maybe_serialize([$_SESSION, $user_id, $_REQUEST]), FILE_APPEND);

    }

    public function editable_roles($all_roles) {

     //   var_dump($all_roles);

        return $all_roles;

    }



}


?>
