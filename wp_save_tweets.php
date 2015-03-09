<?php

/*
  Plugin Name: WP Save Tweets
  Plugin URI: https://github.com/ambercouch/wp_save_tweets
  Description: IFTTT can create a post in WordPress every time you tweet. <a href="https://ifttt.com/recipes/267523-wp-save-tweets">Use this IFTTT recipe</a>. This Plugin automatically adds a custom post type called 'tweets' and converts IFTTT posts to custom posts so tweets don't show in your main feed.
  Version: 0.1
  Author: Richie Ambercouch
  Author URI: http://ambercouch.co.uk
 */

//register the tweets custom post type
function add_cpt_tweets() {
  $labels = array(
      'name' => _x('Tweets', 'post type general name'),
      'singular_name' => _x('Tweet', 'post type singular name'),
      'add_new' => _x('Add New', 'Tweet'),
      'add_new_item' => __('Add New Tweet'),
      'edit_item' => __('Edit Tweet'),
      'new_item' => __('New Tweet'),
      'all_items' => __('All Tweets'),
      'view_item' => __('View Tweets'),
      'search_items' => __('Search Tweets'),
      'not_found' => __('No Tweets found'),
      'not_found_in_trash' => __('No Tweets found in the Trash'),
      'parent_item_colon' => '',
      'menu_name' => 'Tweets'
  );
  $args = array(
      'labels' => $labels,
      'description' => 'Ambercouch Testimonials',
      'public' => true,
      'menu_position' => 6,
      'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'page-attributes'),
      'has_archive' => true,
  );
  register_post_type('tweets', $args);
}

add_action('init', 'add_cpt_tweets');

//save all posts with catgory tweet as custom post tweets
function save_tweets($post_id) {

  //if the post has the category 'tweet'
  if (!has_term('tweet', 'category', $post_id)) {
    return;
  }

  //save as tweets
  set_post_type($post_id, 'tweets');
}

add_action('save_post', 'save_tweets');

//Convert urls and hashtags to html links
function format_tweets($post_id) {

  //prevent infinite loops
  remove_action('save_post', 'format_tweets');

  //get the post by id
  $content_post = get_post($post_id);

  //convert url to html links
  $content = preg_replace(
          "/(?<!a href=\")(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i", "<a href=\"\\0\" target=\"blank\">\\0</a>", $content_post->post_content
  );

  //convert #hashtags to html links
  $content = preg_replace('/#([0-9a-zA-Z]+)/i', '<a href="http://twitter.com/hashtag/$1">#$1</a>', $content);


  //update the post with the formatted content
  wp_update_post(
          array(
              'ID' => $post_id,
              'post_content' => $content
          )
  );

  //reset the action
  add_action('save_post', 'format_tweets');
}

add_action('save_post', 'format_tweets');

