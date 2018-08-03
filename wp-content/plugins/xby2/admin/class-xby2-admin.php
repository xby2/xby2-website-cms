<?php

// Include custom classes
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/ClientStory.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/Industry.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/RecruitingValue.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/Service.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/FrequentlyAskedQuestion.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/Link.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/Perk.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/CompanyValue.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/Location.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/MindShare.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Models/Author.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.elevenwinds.com/
 * @since      1.0.0
 *
 * @package    Xby2
 * @subpackage Xby2/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Xby2
 * @subpackage Xby2/admin
 * @author     Colin <clouzon@elevenwinds.com>
 */
class Xby2_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Xby2_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Xby2_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/xby2-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Xby2_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Xby2_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/xby2-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * 'init' hook callback
     * Call the class methods to register the custom post types and their routes for the api
     */
	public function init_classes(){

	    // Register custom types
	    Author                  ::registerTypeAndRoutes();
		ClientStory             ::registerTypeAndRoutes();
		Industry                ::registerTypeAndRoutes();
		RecruitingValue         ::registerTypeAndRoutes();
		Service                 ::registerTypeAndRoutes();
		FrequentlyAskedQuestion ::registerTypeAndRoutes();
		Link                    ::registerTypeAndRoutes();
		Perk                    ::registerTypeAndRoutes();
		CompanyValue            ::registerTypeAndRoutes();
		Location                ::registerTypeAndRoutes();
		MindShare               ::registerTypeAndRoutes();
	}

    /**
     * 'wp_insert_post' hook callback
     * Register the custom meta fields
     *
     * @param $post_id
     */
	public function set_custom_fields($post_id){

	    $post = get_post($post_id);

	    // When creating a new Post, register the meta fields
	    switch ($post->post_type) {
            case 'clientstory'     :ClientStory::registerMeta($post_id);             break;
            case 'service'         :Service::registerMeta($post_id);                 break;
            case 'faq'             :FrequentlyAskedQuestion::registerMeta($post_id); break;
            case 'link'            :Link::registerMeta($post_id);                    break;
            case 'perk'            :Perk::registerMeta($post_id);                    break;
            case 'companyvalue'    :CompanyValue::registerMeta($post_id);            break;
            case 'location'        :Location::registerMeta($post_id);                break;
            case 'recruitingvalue' :RecruitingValue::registerMeta($post_id);         break;
            case 'industry'        :Industry::registerMeta($post_id);                break;
            case 'mindshare'       :MindShare::registerMeta($post_id);               break;
            case 'author'		   :Author::registerMeta($post_id);					 break;
        }
	}

    /**
     * 'admin_menu' hook callback
     * Remove menu items
     */
	public function remove_admin_menu_items(){
		remove_menu_page( 'edit.php' );                //Posts
		remove_menu_page( 'edit.php?post_type=page' ); //Pages
		remove_menu_page( 'edit-comments.php' );       //Comments
	}

    /**
     * 'upload_mimes' hook callback
     * Allow SVG uploads
     *
     * @param $allowed_mimes
     * @return array
     */
	public function allow_svg_uploads( $allowed_mimes ){
        $allowed_mimes['svg'] = 'image/svg+xml';
        $allowed_mimes['svgz'] = 'image/svg+xml';
        return $allowed_mimes;
    }

}
