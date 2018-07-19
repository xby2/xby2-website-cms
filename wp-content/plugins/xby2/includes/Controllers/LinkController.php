<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Link.php';
require_once 'Xby2BaseController.php';

class LinkController extends Xby2BaseController {

    public function __construct() {
        $this->base = "links";
        $this->post_type = "link";
    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $this->registerBaseRoutes();
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
        $Link = new Link($item);
        return $Link;
    }

    /**
     * Prepare the item collection for sending back as API response
     *
     * @param $items
     * @param $request
     * @return array
     */
    public function prepare_items_for_response( $items, $request ) {
        usort($items, array($this, 'sortLinksByPriority'));
        return $items;
    }

    /**
     * Sort links to ascending order based on their priority
     *
     * @param $x
     * @param $y
     * @return int
     */
    function sortLinksByPriority ($x, $y) {
        // Cast strings to floats for comparison
        $x = (float) $x->priority;
        $y = (float) $y->priority;

        if ($x == $y){
            return 0;
        }
        return $x < $y ? -1 : 1;
    }
}