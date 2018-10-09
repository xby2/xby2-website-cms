<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/AuthorController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Views/Xby2BaseView.php';

class Author extends Xby2BaseModel {

    public $id;
    public $name;
    public $imageUrl;
    public $title;

    public function __construct($post)
    {
        // Get the data we need from the post
        $this->id = $post->ID;
        $this->name = $post->post_title;

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
            'plural_name'   => 'Authors',
            'singular_name' => 'Author',
            'dashicon'      => 'dashicons-businessman',
            'supports'      => array( 'title' ),
            'post_type'     => 'author'
        );

        parent::registerType($data);

		$controller = new AuthorController();
		$controller->register_routes();
	}

    // Register the meta box to add to the Post edit page, and define the html callback
	public static function add_meta_box() {
        add_meta_box(
            'author_meta',              // Unique ID
            'Properties',               // Box title
            [self::class, 'meta_html'], // Content callback, must be of type callable
            "author"                    // Post type
        );
    }

    // Callback to display the HTML for this meta box
    function meta_html($post)
    {
        $view = new Xby2BaseView(get_post_meta($post->ID));
        $view->addInput('text', 'author-image-url', 'Author Image Url: ', 'imageUrl', 'large-text');
        $view->addInput('text', 'author-title',     'Author Title: ',     'title',    'regular-text');
        $view->displayForm();
    }

    // Callback function to save the metadata when saving/updating the post
    public static function save_meta_data($post_id)
    {
        $standardMetaValues  = array("imageUrl", "title");
        parent::save_standard_meta_values($standardMetaValues, $post_id);
    }
}