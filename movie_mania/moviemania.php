<?php 

/**
 * Movie Mania
 *
 * Plugin Name: Movie Mania
 * Plugin URI:  https://magnigeeks.com/
 * Description: This plugin helps to upload and show movies on your website, allowing you to create your cinematic universe.
 * Version:     1.0.0
 * Author:      Sulaiman hasan Evan
 * Text Domain: Movie_mania
 * Requires PHP: 5.2.4
 */



 // Function to add a custom meta box for Movie Name
function movie_name_meta_box() {
    add_meta_box(
        'movie_name_meta_box',
        'Movie Name',
        'movie_name_meta_box_callback',
        'movie',
        'normal',
        'default'
    );
}

// Callback function to display the Movie Name field
function movie_name_meta_box_callback($post) {
    // Retrieve the current Movie Name value
    $movie_name = get_post_meta($post->ID, 'movie_name', true);
    ?>
    <input type="text" name="movie_name" value="<?php echo esc_attr($movie_name); ?>" style="width: 100%;" />
    <?php
}

// Function to save the Movie Name field
function save_movie_name($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['movie_name'])) {
        update_post_meta($post_id, 'movie_name', sanitize_text_field($_POST['movie_name']));
    }
}

add_action('add_meta_boxes', 'movie_name_meta_box');
add_action('save_post', 'save_movie_name');


// Add support for featured image (Movie Poster)
function add_movie_poster_support() {
    add_post_type_support('movie', 'thumbnail');
}

add_action('init', 'add_movie_poster_support');

function movie_release_year_meta_box() {
    add_meta_box(
        'movie_release_year_meta_box',
        'Movie Release Year',
        'movie_release_year_meta_box_callback',
        'movie',
        'normal',
        'default'
    );
}

// Callback function to display the Movie Release Year field
function movie_release_year_meta_box_callback($post) {
    // Retrieve the current Movie Release Year value
    $movie_release_year = get_post_meta($post->ID, 'movie_release_year', true);
    ?>
    <input type="text" name="movie_release_year" value="<?php echo esc_attr($movie_release_year); ?>" style="width: 100%;" />
    <?php
}

// Function to save the Movie Release Year field
function save_movie_release_year($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['movie_release_year'])) {
        update_post_meta($post_id, 'movie_release_year', sanitize_text_field($_POST['movie_release_year']));
    }
}

add_action('add_meta_boxes', 'movie_release_year_meta_box');
add_action('save_post', 'save_movie_release_year');


// Function to add a custom meta box for Movie Length
function movie_length_meta_box() {
    add_meta_box(
        'movie_length_meta_box',
        'Movie Length',
        'movie_length_meta_box_callback',
        'movie',
        'normal',
        'default'
    );
}

// Callback function to display the Movie Length field
function movie_length_meta_box_callback($post) {
    // Retrieve the current Movie Length value
    $movie_length = get_post_meta($post->ID, 'movie_length', true);
    ?>
    <input type="text" name="movie_length" value="<?php echo esc_attr($movie_length); ?>" style="width: 100%;" />
    <?php
}

// Function to save the Movie Length field
function save_movie_length($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['movie_length'])) {
        update_post_meta($post_id, 'movie_length', sanitize_text_field($_POST['movie_length']));
    }
}

add_action('add_meta_boxes', 'movie_length_meta_box');
add_action('save_post', 'save_movie_length');


// Function to add a custom meta box for Movie URL and Movie Upload
function movie_url_upload_meta_box() {
    add_meta_box(
        'movie_url_upload_meta_box',
        'Movie URL & Upload',
        'movie_url_upload_meta_box_callback',
        'movie',
        'normal',
        'default'
    );
}

// Callback function to display the Movie URL and Movie Upload fields
function movie_url_upload_meta_box_callback($post) {
    // Retrieve the current Movie URL and Movie Upload values
    $movie_url = get_post_meta($post->ID, 'movie_url', true);
    $movie_upload = get_post_meta($post->ID, 'movie_upload', true);
    ?>
    <p>Movie URL (YouTube/Vimeo):</p>
    <input type="text" name="movie_url" value="<?php echo esc_url($movie_url); ?>" style="width: 100%;" />

    <p>Movie Upload (MP4, AVI, MKV):</p>
    <input type="file" name="movie_upload" accept=".mp4,.avi,.mkv" />

    <?php
}

// Function to save the Movie URL and Movie Upload fields
function save_movie_url_upload($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    // Save Movie URL
    if (isset($_POST['movie_url'])) {
        update_post_meta($post_id, 'movie_url', esc_url($_POST['movie_url']));
    }

    // Handle Movie Upload
    if (isset($_FILES['movie_upload']) && !empty($_FILES['movie_upload']['name'])) {
        $file = $_FILES['movie_upload'];
        $upload_dir = wp_upload_dir();
        $file_name = sanitize_file_name($file['name']);
        $file_path = $upload_dir['path'] . '/' . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            update_post_meta($post_id, 'movie_upload', $file_path);
        }
    }
}

add_action('add_meta_boxes', 'movie_url_upload_meta_box');
add_action('save_post', 'save_movie_url_upload');


// Function to create custom taxonomies for Genres and Category
function create_movie_taxonomies() {
    // Custom taxonomy for Genres
    $genre_labels = array(
        'name' => 'Genres',
        'singular_name' => 'Genre',
    );

    $genre_args = array(
        'labels' => $genre_labels,
        'hierarchical' => true,
    );

    register_taxonomy('movie_genre', 'movie', $genre_args);

    // Custom taxonomy for Category
    $category_labels = array(
        'name' => 'Category',
        'singular_name' => 'Category',
    );

    $category_args = array(
        'labels' => $category_labels,
        'hierarchical' => true,
    );

    register_taxonomy('movie_category', 'movie', $category_args);
}

add_action('init', 'create_movie_taxonomies');


// Shortcode to Display Movie List
function movie_list_shortcode($atts) {
    ob_start();

    // Retrieve the filter values
    $filter_movie_name = isset($_POST['movie_name']) ? sanitize_text_field($_POST['movie_name']) : '';
    $filter_release_year = isset($_POST['release_year']) ? sanitize_text_field($_POST['release_year']) : '';
    $filter_genre = isset($_POST['genre']) ? sanitize_text_field($_POST['genre']) : '';

    // WP_Query arguments for retrieving movies with filters
    $args = array(
        'post_type' => 'movie',
        'posts_per_page' => -1,
        's' => $filter_movie_name, // Search by movie name
        'meta_query' => array(
            array(
                'key' => 'movie_release_year',
                'value' => $filter_release_year,
                'compare' => '='
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'movie_genre',
                'field' => 'id',
                'terms' => $filter_genre
            )
        )
    );

    $query = new WP_Query($args);

    // Output the filter form
    ?>
    <form method="post" action="">
        <input type="text" name="movie_name" placeholder="Movie Name" value="<?php echo $filter_movie_name; ?>" />
        <input type="text" name="release_year" placeholder="Release Year" value="<?php echo $filter_release_year; ?>" />
        <?php
        // Output a dropdown for genres
        $genres = get_terms(array('taxonomy' => 'movie_genre', 'hide_empty' => false));
        ?>
        <select name="genre">
            <option value="">All Genres</option>
            <?php foreach ($genres as $genre) : ?>
                <option value="<?php echo $genre->term_id; ?>" <?php selected($filter_genre, $genre->term_id); ?>>
                    <?php echo $genre->name; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Filter" />
    </form>
    <?php

    // Output the movie list
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            ?>
            <div class="movie-item">
                <h2><?php the_title(); ?></h2>
                <p>Release Year: <?php echo get_post_meta(get_the_ID(), 'movie_release_year', true); ?></p>
                <p>Genre: <?php echo get_the_term_list(get_the_ID(), 'movie_genre', '', ', '); ?></p>
                <!-- You can display other movie details here -->
            </div>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo 'No movies found.';
    }

    return ob_get_clean();
}

add_shortcode('movie_list', 'movie_list_shortcode');

// Register the custom post type 'movie'
function register_movie_post_type() {
    $labels = array(
        'name' => 'Movies',
        'singular_name' => 'Movie',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-video-alt2', 
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
    );

    register_post_type('movie', $args);
}

add_action('init', 'register_movie_post_type');








 ?>