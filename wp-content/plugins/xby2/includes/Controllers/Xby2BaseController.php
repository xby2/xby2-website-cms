<?php

Class Xby2BaseController {

    public $namespace = 'xby2/v1';
    public $base;
    public $post_type;

    // Register the simple "/get" and "/get/{id}" routes that are present on each type
    public function registerBaseRoutes() {

        // Register route to get all items of the specified type
        register_rest_route($this->namespace, '/' . $this->base, array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_items'),
                'permission_callback' => array( $this, 'get_permissions_check' ),
                'args' => array(),
            )
        ));

        // Register route to get item with specific Id
        register_rest_route($this->namespace, '/' . $this->base . '/(?P<id>[\d]+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_item'),
                'permission_callback' => array( $this, 'get_permissions_check' ),
                'args' => array(
                    'id'
                ),
            )
        ));
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items( $request ) {

        try {
            $data = array();

            // Try to get this response data from the cache
            if ( $data = get_transient($this->post_type . '-api-cache') ) {
                return new WP_REST_Response( $data, 200 );
            }

            // Get all posts of this post type
            $items = get_posts([
                'post_type' => $this->post_type,
                'numberposts' => -1
            ]);

            // Prepare each post for response and add it to the return array
            foreach( $items as $item ) {
                $itemdata = $this->prepare_item_for_response( $item, $request );
                $data[] = $itemdata;
            }

            $data = $this->prepare_items_for_response($data, $request);

            // Set cache, expire it in one week
            set_transient( $this->post_type . '-api-cache' , $data, WEEK_IN_SECONDS);

            return new WP_REST_Response( $data, 200 );
        }
        catch (Exception $exception) {
            $this->sendExceptionResponse($exception);
        }

    }

    /**
     * Get one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_item( $request ) {

        try {
            //get parameters from request
            $params = $request->get_params();
            $item = get_post($params['id']);

            // Ensure the post that we found is of the correct type
            if ($item->post_type == $this->post_type) {
                $data = $this->prepare_item_for_response( $item, $request );
                return new WP_REST_Response( $data, 200 );
            } else {
                return new WP_Error( 'item_not_found', __( 'Found no '.$this->post_type.' with the provided id'), array( 'status' => 404 ) );
            }
        }
        catch (Exception $exception){
            $this->sendExceptionResponse($exception);
        }

    }

    /**
     * Prepare the post item for sending back as API response
     * This method is meant to be overridden to load the proper post type
     *
     * @param $item
     * @param $request
     * @return array
     */
    public function prepare_item_for_response( $item, $request ) {
        return $item;
    }

    /**
     * Prepare the item collection for sending back as API response
     * This method is meant to be overridden to load the proper post type
     *
     * @param $items
     * @param $request
     * @return array
     */
    public function prepare_items_for_response( $items, $request ) {
        return $items;
    }

    /**
     * Permission check for all get requests
     *
     * @return bool|WP_Error
     */
    public function get_permissions_check() {

        if (false) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'This data is not available to view' ), array( 'status' => 401 ) );
        }
        return true;
    }

    /**
     * @param Exception $exception
     * @return WP_Error
     */
    public function sendExceptionResponse($exception){
        return new WP_Error( 'error_occurred', __( 'An unexpected error occurred: ' . $exception->getMessage()), array( 'status' => 500 ) );
    }
}
