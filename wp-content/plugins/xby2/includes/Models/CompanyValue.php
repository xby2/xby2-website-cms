<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/CompanyValueController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class CompanyValue extends Xby2BaseModel {

    public $id;
    public $iconUrl;
    public $title;
    public $description;

    public function __construct($post)
    {
        // Get the data we need from the post
        $this->id = $post->ID;
        $this->title = $post->post_title;
        $this->description = strip_tags($post->post_content);

        // Get the data we need from our custom fields
        foreach ($post->meta as $key=>$value) {
            if ( property_exists ( $this , $key ) ) {
                $this->$key = $value[0];
            }
        }

    }

    // Register this custom post type, and its associated custom REST endpoints
    public static function registerTypeAndRoutes() {

        $data = array(
            'plural_name'   => 'Company Values',
            'singular_name' => 'Company Value',
            'dashicon'      => 'dashicons-slides',
            'supports'      => array( 'title', 'editor' ),
            'post_type'     => 'companyvalue'
        );

        parent::registerType($data);

        $controller = new CompanyValueController();
        $controller->register_routes();
    }

    // Register the meta box to add to the Post edit page, and define the html callback
    public static function add_meta_box() {
        add_meta_box(
            'company_value_meta',        // Unique ID
            'Properties',                // Box title
            [self::class, 'meta_html'],  // Content callback, must be of type callable
            "companyvalue"               // Post type
        );
    }

    // Callback to display the HTML for this meta box
    function meta_html($post)
    {
        $view = new Xby2BaseView(get_post_meta($post->ID));
        $view->addInput('imageSelect', 'company-value-icon-url', 'Icon Url: ', 'iconUrl', 'large-text');
        $view->displayForm();
    }

    // Callback function to save the metadata when saving/updating the post
    public static function save_meta_data($post_id) {
        $standardMetaValues = array("iconUrl");
        parent::save_standard_meta_values($standardMetaValues, $post_id);
    }
}