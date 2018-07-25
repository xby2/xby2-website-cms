<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/MindShareController.php';

Class MindShare extends Xby2BaseModel {

    public $id;
    public $title;
    public $authorName;
    public $authorTitle;
    public $authorImageUrl;
    public $shortDescription;
    public $isFeatured;
    public $industry;
    public $publishDate;
    public $readTimeInMinutes;
    public $publishName;
    public $publishUrl;
    public $content;
    public $tags;
    public $nextMindShareId;

    public function __construct($post) {

        $this->id = $post->ID;
        $this->title = $post->post_title;
        $this->content = apply_filters('the_content', $post->post_content);

        $tags = get_the_tags($this->id);
        if ($tags){
            foreach ($tags as $tag) {
                $this->tags[] = $tag->name;
            }
        }

        // Get the data we need from our custom fields
        foreach ($post->meta as $key=>$value) {
            if ( property_exists ( $this , $key ) ) {
                if ($key == "isFeatured") {
                    $this->$key = filter_var($value[0], FILTER_VALIDATE_BOOLEAN);
                } else {
                    $this->$key = $value[0];
                }
            }
        }

    }

    // Register this custom post type, and its associated custom REST endpoints
    public static function registerTypeAndRoutes () {

        $data = array(
            'plural_name'   => 'Mind Shares',
            'singular_name' => 'Mind Share',
            'dashicon'      => 'dashicons-format-chat',
            'supports'      => array( 'title', 'editor', 'custom-fields' ),
            'post_type'     => 'mindshare'
        );

        parent::registerType($data);

        register_taxonomy_for_object_type( 'post_tag', 'mindshare' );

        $controller = new MindShareController();
        $controller->register_routes();
    }

    // Add all custom post meta fields when creating a new post of this type
    public static function registerMeta ($post_id) {
        add_post_meta($post_id, 'authorName',        '', true);
        add_post_meta($post_id, 'authorTitle',       '', true);
        add_post_meta($post_id, 'authorImageUrl',    '', true);
        add_post_meta($post_id, 'shortDescription',  '', true);
        add_post_meta($post_id, 'isFeatured',        '', true);
        add_post_meta($post_id, 'industry',          '', true);
        add_post_meta($post_id, 'publishDate',       '', true);
        add_post_meta($post_id, 'readTimeInMinutes', '', true);
        add_post_meta($post_id, 'publishUrl',        '', true);
        add_post_meta($post_id, 'publishName',       '', true);
        add_post_meta($post_id, 'nextMindShareId',   '', true);
    }
}