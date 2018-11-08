<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/MindShareController.php';

Class MindShare extends Xby2BaseModel {

    public $id;
    public $title;
    public $authorId;
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
    public $nextMindShareTitle;

    public function __construct($post) {

        $this->id = (isset($post->meta['slug'][0])) ? $post->meta['slug'][0] : $post->ID;
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
                // Populate the "nextMindShare______" fields based on the given post ID
                } else if ($key == "authorId") {
                    $authorPost = get_post($value[0]);
                    if ($authorPost) {
                        $meta = get_post_meta($authorPost->ID);
                        $authorPost->meta = $meta;
                        $Author = new Author($authorPost);
                        $this->authorId = $authorPost->ID;
                        $this->authorImageUrl = $Author->imageUrl;
                        $this->authorName = $Author->name;
                        $this->authorTitle = $Author->title;
                    }
                } else if ($key == "nextMindShareId") {
                    $mindSharePost = get_post($value[0]);
                    if ($mindSharePost) {
                        $meta = get_post_meta($mindSharePost->ID);
                        $this->nextMindShareId = (isset($meta['slug'][0])) ? $meta['slug'][0] : $post->ID;
                        $this->nextMindShareTitle = $mindSharePost->post_title;
                    }
                } else if ($key == "industry") {
                    $industryPost = get_post($value[0]);
                    if ($industryPost) {
                        $this->industry = $industryPost->post_title;
                    }
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
            'supports'      => array( 'title', 'editor'),
            'post_type'     => 'mindshare'
        );

        parent::registerType($data);

        register_taxonomy_for_object_type( 'post_tag', 'mindshare' );

        $controller = new MindShareController();
        $controller->register_routes();
    }

    // Register the meta box to add to the Post edit page, and define the html callback
    public static function add_meta_box() {
        add_meta_box(
            'mindshare_meta',           // Unique ID
            'Properties',               // Box title
            [self::class, 'meta_html'], // Content callback, must be of type callable
            "mindshare"                 // Post type
        );
    }

    // Callback to display the HTML for this meta box
    function meta_html($post)
    {
        $view = new Xby2BaseView(get_post_meta($post->ID));

        $authors    = get_posts(['post_type' => 'author', 'numberposts' => -1]);
        $industries = get_posts(['post_type' => 'industry', 'numberposts' => -1]);
        $mindShares = get_posts(['post_type' => 'mindshare', 'numberposts' => -1]);

        // Options for the Read Time dropdown menu
        $readTimes  = [ 5, 10, 15, 20, 25, 30];

        $view->addInput('dropdown', 'mind-share-author',       'Author: ',                  'authorId',          'regular-text', $authors);
        $view->addInput('dropdown', 'mind-share-industry',     'Industry: ',                'industry',          'regular-text', $industries);
        $view->addInput('dropdown', 'mind-share-mindshare',    'Next Mind Share: ',         'nextMindShareId',   'regular-text', $mindShares);
        $view->addInput('checkbox', 'mind-share-featured',     'Featured (Y/N): ',          'isFeatured',        '');
        $view->addInput('text',     'mind-share-publish-date', 'Publish Date (MM/DD/YYYY)', 'publishDate',       'regular-text');
        $view->addInput('text',     'mind-share-publish-name', 'Publish Name',              'publishName',       'regular-text');
        $view->addInput('text',     'mind-share-publish-url',  'Publish Url',               'publishUrl',        'regular-text');
        $view->addInput('dropdown', 'mind-share-read-time',    'Read Time (Minutes)',       'readTimeInMinutes', 'regular-text', $readTimes);
        $view->addInput('text',     'mind-share-description',  'Short Description',         'shortDescription',  'large-text', [], true);
        $view->addInput('text',     'client-story-slug',       'URL Slug: ',                'slug',              'regular-text');

        $view->displayForm();
    }

    // Callback function to save the metadata when saving/updating the post
    public static function save_meta_data($post_id) {
        $standardMetaValues = array("authorId", "industry", "nextMindShareId", "publishDate", "publishName", "publishUrl", "readTimeInMinutes", "shortDescription", "slug");
        parent::save_standard_meta_values($standardMetaValues, $post_id);

        // Update Checkbox value
        update_post_meta(
            $post_id,
            'isFeatured',
            (array_key_exists('isFeatured', $_POST)) ? TRUE : FALSE
        );
    }
}