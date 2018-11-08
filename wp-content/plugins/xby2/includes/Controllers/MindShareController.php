<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/MindShare.php';
require_once 'Xby2BaseController.php';

class MindShareController extends Xby2BaseController {

    public function __construct() {
        $this->base = "mind-shares";
        $this->post_type = "mindshare";
    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $this->registerBaseRoutes();

        // Register route to get item with specific Id
        register_rest_route($this->namespace, '/' . $this->base . '/(?P<slug>[a-zA-Z0-9-]+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_item_by_slug'),
                'permission_callback' => array( $this, 'get_permissions_check' ),
                'args' => array(
                    'slug'
                ),
            )
        ));
    }

    /**
     * Get one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_item_by_slug( $request ) {

        try {
            //get parameters from request
            $params = $request->get_params();
            $args = array(
                'meta_query' => array(
                    array(
                        'key'   => 'slug',
                        'value' => $params['slug'],
                    )
                ),
                'post_type' => 'mindshare',
                'posts_per_page' => '1'
            );
            $items = get_posts($args);

            // Ensure the post that we found is of the correct type
            if ($items[0]->post_type == $this->post_type) {
                $data = $this->prepare_item_for_response( $items[0], $request );
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
     * Prepare the item for the REST response
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return mixed
     */
    public function prepare_item_for_response( $item, $request ) {
        $meta = get_post_meta($item->ID);
        $item->meta = $meta;
        $MindShare = new MindShare($item);
        return $MindShare;
    }

}