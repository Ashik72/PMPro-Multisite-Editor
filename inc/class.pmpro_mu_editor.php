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
        add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_frontend_style'), 50 );

        add_filter('editable_roles', [$this, 'editable_roles'], 10, 1);

        add_action( 'pmpro_after_checkout', [$this, 'pmproap_pmpro_after_checkout'], 1000 );

        add_shortcode('buy_editor_access', [$this, 'buy_editor_access'], 1000);

        remove_filter('pmpro_has_membership_access_filter', 'pmproap_pmpro_has_membership_access_filter', 10);

        add_filter('get_post_metadata', [$this, 'get_post_metadata'], 10, 4);

        add_filter('pmpro_international_addresses', [$this, 'add_hidden_quantity'], 1000, 1);

        add_action('wpmu_new_user', [$this, 'after_signup_user'], 1000, 1);

        add_action('template_redirect', [$this, 'check_and_assign_membership'], 1000);

        // add_filter( 'manage_users_columns', [$this, 'add_credit_col'], 10, 1 );

        ////
        ///
        add_action('wpmu_activate_blog', [$this, 'after_activate_blog'], 1000, 5);

        add_action('template_redirect', function () {
            if (empty($_GET['d'])) return;
            //global $current_user;
            $current_user = new WP_User(get_current_user_id(), '',1);


//            Kint::dump($user->get_role_caps());
//            var_dump(in_array("administrator", $user->roles));

            delete_user_meta(get_current_user_id(), 'set_editor_quantity');

            $get_quantity = get_user_meta(get_current_user_id(), 'set_editor_quantity', true);
            var_dump($get_quantity);


            wp_die();
            return;
            $get_quantity = get_user_meta(get_current_user_id(), 'set_editor_quantity', true);

            Kint::dump($get_quantity);

            wp_die();

            return;

            $titan = TitanFramework::getInstance( 'pmpro_mu_editor' );

            global $wpdb;

            $pmpro_mu_editor_options = $wpdb->get_var("SELECT option_value FROM {$wpdb->base_prefix}options WHERE option_name = 'pmpro_mu_editor_options'");
            $pmpro_mu_editor_options = maybe_unserialize($pmpro_mu_editor_options);
            $pmpro_mu_editor_options = maybe_unserialize($pmpro_mu_editor_options);
            $protected_user_roles = $pmpro_mu_editor_options['protected_user_roles'];
            $protected_user_roles = explode("\n", $protected_user_roles);

            Kint::dump($protected_user_roles);

            wp_die();
            return;
            Kint::dump(get_user_meta(get_current_user_id()));

            //update_user_meta(get_current_user_id(), '_pmproap_posts', []);


            $post_users = get_post_meta( 14, '_pmproap_users', true );

            update_post_meta( 14, '_pmproap_users', [] );



            wp_die();
        });

        add_action('in_admin_header', function () {

            if (is_super_admin()) return;

            $current_screen = get_current_screen();

            if ($current_screen->base != "user") return;

            $get_quantity = get_user_meta(get_current_user_id(), 'set_editor_quantity', true);
            $get_quantity = intval($get_quantity);

            if ($get_quantity > 0) return;

            $pmpro_mu_editor_options = $this->getOptions();
            ?>

            <script>
                window.location.replace("<?php _e(get_admin_url().'admin.php?page=subscription&tab=purchase-access'); ?>");
            </script>

            <?php

            wp_redirect( get_admin_url().'admin.php?page=subscription&tab=purchase-access' );
            exit;

        });
        add_shortcode( 'pmpro_editor_balance', [$this, 'pmpro_editor_balance'] );

        add_action('template_redirect', function () {
            global $wpdb;
            pmpro_mu_editor::changePMProTables();

        });

        add_action('wp_footer', [$this, 'update_page_design']);

        add_action('pmpro_email_field_type', [$this, 'update_price'], 10, 1);

    }


    public function update_price($data) {
        global $pmpro_level;
        $pmpro_level->billing_amount = $pmpro_level->billing_amount * intval($_GET['quantity']);


        return $data;
    }

    public function update_page_design() {

        $options = self::getEditorOptions();
        if (is_page($options['checkout_page_id']) || is_page('membership-confirmation'))        {

            global $pmpro_level;
            $quantity = intval($_GET['quantity']);
            global $pmpro_currency;
            $txt = $pmpro_level->billing_amount." ".$pmpro_currency." for {$quantity} member(s).";
            ?>

            <script>

                jQuery(document).ready(function ($) {

                    $("#wpadminbar").css("display", "none");
                    $("#masthead").css("display", "none");
                    $("#colophon").css("display", "none");

                    $("#pmpro_level_cost p").html("Please pay "+"<?php _e($txt); ?>");

                })

            </script>

            <?php



        }
    }

    public function pmpro_editor_balance($atts) {


        if (!is_user_logged_in()) return;

        $get_quantity = get_user_meta(get_current_user_id(), 'set_editor_quantity', true);

            return $get_quantity;
    }


    public function after_activate_blog($blog_id, $user_id, $password, $signup_title, $meta) {

        global $wpdb;

        $cap = $wpdb->get_var("SELECT meta_value FROM {$wpdb->base_prefix}usermeta WHERE user_id = {$user_id} AND meta_key LIKE 'wp_{$blog_id}_capabilities'");
        $cap = maybe_unserialize($cap);

        if (empty($cap['administrator'])) return;


        $pmpro_mu_editor_options = $this->getOptions();
        $free_credits = isset($pmpro_mu_editor_options['free_credits']) ? $pmpro_mu_editor_options['free_credits'] : 0;
        $free_credits = intval($free_credits);
        update_user_meta($user_id, 'set_editor_quantity', $free_credits);

        $data = [
            $cap, $blog_id, $user_id
        ];

        file_put_contents(pmpro_mu_editor_PLUGIN_DIR."aadata-".time().".txt", maybe_serialize($data), FILE_APPEND);

        return;

    }

    public function add_credit_col($column) {
        if (!is_super_admin()) return $column;

        $column['contributor_balance'] = 'Contributor Balance';
        return $column;

    }

    public function check_and_assign_membership() {

        if (!is_user_logged_in()) return;
        if (is_super_admin()) return;

        $user_id = get_current_user_id();

        if (!function_exists('pmpro_getMembershipLevelForUser')) return;

        if (!empty(pmpro_getMembershipLevelForUser($user_id))) return;

        $pmpro_mu_editor_options = $this->getOptions();
        $level_to_assign = $pmpro_mu_editor_options['free_level_id'];

        pmpro_changeMembershipLevel($level_to_assign, $user_id);

    }

    public function add_hidden_quantity($result) {
        echo '<input id="quantity" name="quantity" value="'.$_GET['quantity'].'" type="hidden">';
        return $result;
    }

    public function load_custom_wp_admin_frontend_style() {


        wp_register_script('pmpro_mu_editor_main-users-custom', pmpro_mu_editor_PLUGIN_URL . 'js/users.js', array('jquery'), '', false);

        $adminurl = admin_url();

        wp_localize_script('pmpro_mu_editor_main-users-custom', 'mu_custom', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'is_super_admin' => is_super_admin(),
            'admin_dir' => $adminurl,
            'is_main_site' => is_main_site()
        ));
        wp_enqueue_script('pmpro_mu_editor_main-users-custom');

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

        $pmpro_mu_editor_options = $this->getOptions();

        $text_level_id = intval($pmpro_mu_editor_options['free_level_id']);
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

        file_put_contents(pmpro_mu_editor_PLUGIN_DIR."acdata-".time().".txt", maybe_serialize([$_SESSION, $user_id, $_REQUEST]), FILE_APPEND);

    }

    public function editable_roles($all_roles) {

        if (is_super_admin()) return $all_roles;

        //Kint::dump($all_roles);
        $user_id = get_current_user_id();
        $get_quantity = get_user_meta($user_id, 'set_editor_quantity', true);
        $get_quantity = intval($get_quantity);
        // $get_quantity = 0;

        if ($get_quantity <= 0 )
            unset($all_roles['editor']);

        $pmpro_mu_editor_options = $this->getOptions();
        $protected_user_roles = isset($pmpro_mu_editor_options['protected_user_roles']) ? $pmpro_mu_editor_options['protected_user_roles'] : [];

        if (empty($protected_user_roles)) return $all_roles;


        $protected_user_roles = explode("\n", $protected_user_roles);

        foreach ($protected_user_roles as $role)
            unset($all_roles[preg_replace('/\s/', '', strtolower($role))]);


        return $all_roles;

    }

    public function getOptions() {

        global $wpdb;

        $pmpro_mu_editor_options = $wpdb->get_var("SELECT option_value FROM {$wpdb->base_prefix}options WHERE option_name = 'pmpro_mu_editor_options'");
        $pmpro_mu_editor_options = maybe_unserialize($pmpro_mu_editor_options);
        $pmpro_mu_editor_options = maybe_unserialize($pmpro_mu_editor_options);

        return $pmpro_mu_editor_options;
    }

    public static function getEditorOptions() {
        return (new self)->getOptions();
    }


    public function after_signup_user($user_id) {

        $created_uid = $user_id;

        global $current_user;
        $user_id = $current_user->ID;

        $credit_applied = get_user_meta($created_uid, 'credit_applied', true);

        if (!empty($credit_applied)) return;

        if (is_super_admin()) {
            if ( !isset($_POST['blog']) ) return;

            $pmpro_mu_editor_options = $this->getOptions();
            $free_credits = isset($pmpro_mu_editor_options['free_credits']) ? $pmpro_mu_editor_options['free_credits'] : 0;
            $free_credits = intval($free_credits);
            update_user_meta($user_id, 'set_editor_quantity', $free_credits);
            update_user_meta($created_uid, 'credit_applied', true);

            return;
        }

        if ($_POST['role'] != 'editor') return;


        update_user_meta($created_uid, 'credit_applied', true);

        global $current_user;
        $user_id = $current_user->ID;
        $get_quantity = get_user_meta($user_id, 'set_editor_quantity', true);
        $get_quantity = intval($get_quantity);

        file_put_contents(pmpro_mu_editor_PLUGIN_DIR."sdata-".time().".txt", maybe_serialize($get_quantity), FILE_APPEND);

        $get_quantity = $get_quantity - 1;
        update_user_meta($user_id, 'set_editor_quantity', $get_quantity);
        return;
    }


    public static function changePMProTables() {
        global $wpdb;
        foreach($wpdb as $key => $value) {
            if (is_array($wpdb->$key)) continue;
            if (empty(strpos($wpdb->$key, 'pmpro'))) continue;
            $wpdb->$key = str_replace($wpdb->prefix, $wpdb->base_prefix, $value);
        }
    }

        public static function pmpro_admin_checkout() {
            ob_start();
            global $current_user;
            $plugin_dir = ABSPATH . 'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR;

            $options = self::getEditorOptions();

            $pmpro_dir = $plugin_dir."paid-memberships-pro";

            //include( $pmpro_dir . "/preheaders/checkout.php");

            //include( $pmpro_dir . "/pages/checkout.php");
            $text_level_id = 1;

            $free_level_id = $options['free_level_id'];



            if (isset($_POST['admin_add_editor_submit']))
                include_once pmpro_mu_editor_PLUGIN_DIR."/template/pay_frame.php";
            else
                include_once pmpro_mu_editor_PLUGIN_DIR."/template/form.php";

            ?>



            <?php

            $output = ob_get_clean();
            return $output;
        }




}


?>
