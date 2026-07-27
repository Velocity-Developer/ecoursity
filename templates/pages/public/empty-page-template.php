<?php

/**
 * Template Name: Ecoursity Empty Page Template
 *
 * @package Ecoursity/Template
 */
defined('ABSPATH') || exit;

get_header();

while (have_posts()) {
    the_post();
    the_content();
}

get_footer();
