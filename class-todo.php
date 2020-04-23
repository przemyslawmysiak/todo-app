<?php

class Todo {

    private $type               = 'tasks';
    private $slug               = 'task';
    private $name               = 'Tasks';
    private $singular_name      = 'Task';
    private $results            = array();

    public function __construct() {
      add_action('init',                          array($this, 'register_tasks_type') );
      add_action('wp_ajax_nopriv_ajax_data',      array($this, 'ajax_data'));
      add_action('wp_ajax_ajax_data',             array($this, 'ajax_data'));
      add_action('wp_enqueue_scripts',            array($this, 'load_js'));

      add_action('wp_enqueue_scripts',            array($this, 'load_css'));

      add_shortcode('todo',                       array($this, 'shortcode') );


    }

    public function init() {

    }

    public function load_css() {
      wp_register_style( 'todo-template',    PLUGIN_URL . '/css/template.min.css' );
      wp_enqueue_style(  'todo-template',    PLUGIN_URL . '/css/template.min.css' );
    }
    public function load_js() {
      wp_register_script('task-ajax', PLUGIN_URL . '/js/js.js', [], null, true);
      wp_enqueue_script( 'task-ajax', PLUGIN_URL . '/js/js.js', array( 'jquery' ) );
      wp_localize_script('task-ajax', 'ajax_nonce', array(
        'url'   => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('task-ajax-nonce')
    ));
    }


    public function shortcode() {
      require('tpl-listing.php');
    }

    public function get_todo_list() {
      $args = array(
      'post_type'              => array( 'tasks' ),
      'post_status'            => array( 'publish' ),
      'orderby'                => array( 'DESC' )
      );

      $query = new WP_Query($args);
      $i=0;
      while ( $query->have_posts() ) {
      $query->the_post();
      $task_status = !empty(get_post_meta(get_the_ID(), 'status', true)) && get_post_meta(get_the_ID(), 'status', true) == '1' ? 1 : 0;
      $task_id = get_the_ID();
      $task_title = htmlspecialchars(get_the_title());

      $this->results[$i]['task_id'] = $task_id;
      $this->results[$i]['task_title'] = $task_title;
      $this->results[$i]['task_status'] = $task_status;
      $i++;
      }
      return $this->results;
    }

    public function check_nonce($nonce) {
      if (wp_verify_nonce( $nonce, 'task-ajax-nonce' )) {
      return true;
      }
      else return false;
    }

    public function ajax_data() {

      if(isset($_POST['req'])) $req = $_POST['req'];
      if(isset($_POST['pid'])) $pid = $_POST['pid'];
      if(isset($_POST['task_name'])) $task_name = $_POST['task_name'];
      if(isset($_POST['status'])) $status = $_POST['status'];

      $nonce = $_POST['security'];
      $checkPost = get_post($pid);

      if(!$this->check_nonce($nonce)) exit(wp_send_json(false));
      if($pid > 0 && $checkPost->post_type != 'tasks') exit(wp_send_json(false));

      switch($req) {
        case 'add':

            if (!empty($task_name)) {
            $task_data = array(
                            'post_title'    => $task_name,
                            'post_status'   => 'publish',
                            'post_type'     => 'tasks',
                            'post_content'  => '');

            $post_id = wp_insert_post($task_data);
            wp_send_json($post_id);
          }
        break;

        case 'update':
            $task_data = array(
                            'post_title'    => $task_name,
                            'post_status'   => 'publish',
                            'post_type'     => 'tasks',
                            'ID'            => $pid,
                            'post_content'  => '');

        $update_task = wp_update_post($task_data);

        break;

        case 'status':

        if($checkPost) update_post_meta($pid, 'status', $status);

        break;

        case 'delete':
            if($checkPost) wp_delete_post($pid);

        break;

      }
}
    // register Custom Post Type (TASKS)
    public function register_tasks_type() {

  			$labels = array(
  					'name'                  => $this->name,
  					'singular_name'         => $this->singular_name,
  					'add_new'               => 'Add New',
  					'add_new_item'          => 'Add New '   . $this->singular_name,
  					'edit_item'             => 'Edit '      . $this->singular_name,
  					'new_item'              => 'New '       . $this->singular_name,
  					'all_items'             => 'All '       . $this->name,
  					'view_item'             => 'View '      . $this->name,
  					'search_items'          => 'Search '    . $this->name,
  					'not_found'             => 'No '        . strtolower($this->name) . ' found',
  					'not_found_in_trash'    => 'No '        . strtolower($this->name) . ' found in Trash',
  					'parent_item_colon'     => '',
  					'menu_name'             => $this->name
  			);

  			$args = array(
  					'labels'                => $labels,
  					'public'                => true,
  					'publicly_queryable'    => true,
  					'show_ui'               => false,
  					'show_in_menu'          => false,
  					'query_var'             => true,
  					'rewrite'               => array( 'slug' => $this->slug ),
  					'capability_type'       => 'post',
  					'has_archive'           => true,
  					'hierarchical'          => true,
            'taxonomies'            => array( '' ),
  					'menu_position'         => 8,
  					'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail'),
  					'yarpp_support'         => true
  			);
        register_post_type( $this->type, $args );
  	}
}
