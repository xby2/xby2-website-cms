<?php

/**
 * Class Xby2BaseModel
 * Base class for all custom Xby2 Post Types
 */
Class Xby2BaseModel{

    /**
     * Register the custom post type using the provided data
     *
     * @param $data
     */
    public static function registerType($data) {

        // Set the labels, this variable is used in the $args array
        $labels = array(
            'name'               => __( $data['plural_name'] ),
            'singular_name'      => __( $data['singular_name'] ),
            'add_new'            => __( 'Add New ' . $data['singular_name']  ),
            'add_new_item'       => __( 'Add New ' . $data['singular_name'] ),
            'edit_item'          => __( 'Edit ' . $data['singular_name'] ),
            'new_item'           => __( 'New ' . $data['singular_name'] ),
            'all_items'          => __( 'All ' . $data['plural_name'] ),
            'view_item'          => __( 'View ' . $data['singular_name'] ),
            'search_items'       => __( 'Search ' . $data['plural_name'])
        );

        // The arguments for our post type, to be entered as parameter 2 of register_post_type()
        $args = array(
            'labels'            => $labels,
            'description'       => 'Holds our ' . strtolower($data['plural_name']),
            'public'            => true,
            'menu_position'     => 100,
            'menu_icon'         => $data['dashicon'],
            'supports'          => $data['supports'],
            'has_archive'       => true,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'query_var'         => $data['post_type']
        );

        // Register the type
        register_post_type( $data['post_type'], $args );

    }

    /**
     * Explode into an array on newline characters so that it may be iterated easily
     *
     * @param $string
     * @return array
     */
    public function getPointsArray($string) {
        $pointsArray = explode("\r\n", $string);
        if ($pointsArray[0] == "") {
            array_shift($pointsArray);
        }
        return $pointsArray;
    }
}