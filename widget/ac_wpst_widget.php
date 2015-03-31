<?php

class Ac_WPST_widget extends WP_Widget {

  public function __construct() {

    parent::__construct(
            'ac_wpst_widget', // Base ID
            'Show Tweets', // Name
            array('description' => __('Show your Tweets in a sidbar', 'ac_wpst'),) // Args
    );
  }

  public function form($instance) {
    if (isset($instance['title'])) {
      $title = $instance['title'];
    } else {
      $title = __('New title', 'ac_wpst');
    }
    if (isset($instance['post_type'])) {
      $post_type = $instance['post_type'];
    } else {
      $post_type = __('', 'ac_wpst');
    }
    if (isset($instance['show'])) {
      $show = $instance['show'];
    } else {
      $show = __('1', 'ac_wpst');
    }
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('show'); ?>"><?php _e('Number of tweets to show:') ?></label>
      <input id="<?php echo $this->get_field_id('show'); ?>" type="text" size="3" value="<?php echo esc_attr($show); ?>" name="<?php echo $this->get_field_name('show'); ?>">
    </p>
    <?php
  }

  public function update($new_instance, $old_instance) {
    $instance = array();
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['post_type'] = strip_tags($new_instance['post_type']);
    $instance['show'] = strip_tags($new_instance['show']);

    return $instance;
  }

  public function widget($args, $instance) {

    extract($args);
    global $wp_query;
    $temp_q = $wp_query;
    $wp_query = null;
    $wp_query = new WP_Query();
    $wp_query->query(array($tax => $cat,
        'post_type' => 'tweets',
        'showposts' => $instance['show'],
        'order' => 'desc')
    );
    echo $before_title . $instance['title'] . $after_title;
    echo $before_widget;

    if (have_posts()) :
      ?>
      <div class="<?php echo $instance['post_type'] ?>-list">
        <?php while (have_posts()): the_post(); ?>
          <article class="tweet">
            <div class="tweet__content">
              <?php the_content(); ?>
            </div>
          </article >
          <?php //get_template_part('content', $instance['post_type']) ?>

        <?php endwhile; ?>
      </div>
      <?php
    endif;
    echo $after_widget;
    $wp_query = $temp_q;
  }

}

add_action('widgets_init', create_function('', 'return register_widget("Ac_WPST_widget");'));

