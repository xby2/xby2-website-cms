<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/LinkController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class Link extends Xby2BaseModel {

    public $id;
    public $label;
    public $link;
    public $priority;

    public function __construct($post)
    {
        // Get the data we need from the post
        $this->id = $post->ID;

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
            'plural_name'   => 'Links',
            'singular_name' => 'Link',
            'dashicon'      => 'dashicons-paperclip',
            'supports'      => array( 'title' ),
            'post_type'     => 'link'
        );

        parent::registerType($data);

		$controller = new LinkController();
		$controller->register_routes();
	}

    // Register the meta box to add to the Post edit page, and define the html callback
    public static function add_meta_box() {
        add_meta_box(
            'link_meta',                 // Unique ID
            'Properties',                // Box title
            [self::class, 'meta_html'],  // Content callback, must be of type callable
            "link"                       // Post type
        );
    }

    // Callback to display the HTML for this meta box
    function meta_html($post)
    {
        $view = new Xby2BaseView(get_post_meta($post->ID));
        $view->addInput('text', 'link-label',    'Label: ',    'label',    'regular-text');
        $view->addInput('text', 'link-link',     'Link: ',     'link',     'large-text');
        $view->addInput('text', 'link-priority', 'Priority: ', 'priority', 'regular-text');
        $view->displayForm();
    }

    // Callback function to save the metadata when saving/updating the post
    public static function save_meta_data($post_id)
    {
        $standardMetaValues = array("label", "link", "priority");
        parent::save_standard_meta_values($standardMetaValues, $post_id);
    }
}