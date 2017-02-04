<?php

/*
   Plugin Name: Destinos
   Plugin URI: https://github.com/cabelotaina/destiny
   Description: Plugin for manager Travel Destiny
   Author: Maurilio Atila
   Version: 0.3
   Author URI: https://github.com/cabelotaina/
 */
defined('ABSPATH') or die('No script kiddies please!');

add_action('init', 'create_destiny');

function create_destiny()
{
 $labels = array(
    'name'               => esc_html__( 'Destino', 'destiny' ),
    'singular_name'      => esc_html__( 'Destino', 'destiny' ),
    'add_new'            => esc_html__( 'Adicionar Novo', 'destiny' ),
    'add_new_item'       => esc_html__( 'Adicionar Novo Destino', 'destiny' ),
    'edit_item'          => esc_html__( 'Editar Destino', 'destiny' ),
    'new_item'           => esc_html__( 'Novo Destino', 'destiny' ),
    'all_items'          => esc_html__( 'Todos os Destinos', 'destiny' ),
    'view_item'          => esc_html__( 'Visualizar Destino', 'destiny' ),
    'search_items'       => esc_html__( 'Buscar Destino', 'destiny' ),
    'not_found'          => esc_html__( 'Nada Encontrado', 'destiny' ),
    'not_found_in_trash' => esc_html__( 'Nada encontrado na Lixeira', 'destiny' ),
    'parent_item_colon'  => '',
  );
  $args = array(
    'labels'             => $labels,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'can_export'         => true,
    'show_in_nav_menus'  => true,
    'query_var'          => true,
    'has_archive'        => true,
    'capability_type'    => 'post',
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' ),
    'menu_icon'          =>  'dashicons-businessman',
  );
  register_post_type( 'destiny', $args );
  $labels = array(
          'name'              => esc_html__( 'Categorias de Destinos', 'destiny' ),
          'singular_name'     => esc_html__( 'Categoria de Destino', 'destiny' ),
          'search_items'      => esc_html__( 'Buscar Categorias de Destinos', 'destiny' ),
          'all_items'         => esc_html__( 'Todas os Categorias de Destinos', 'destiny' ),
          'parent_item'       => esc_html__( 'Categoria de Destino Pai', 'destiny' ),
          'parent_item_colon' => esc_html__( 'Catetegoria de Destino Pai:', 'destiny' ),
          'edit_item'         => esc_html__( 'Editar Categoria de Destino', 'destiny' ),
          'update_item'       => esc_html__( 'Atualizar Categoria de Destino', 'destiny' ),
          'add_new_item'      => esc_html__( 'Adicionar Novo Categoria de Destino', 'destiny' ),
          'new_item_name'     => esc_html__( 'Novo nome do Categoria de Destino', 'destiny' ),
          'menu_name'         => esc_html__( 'Categorias de Destinos', 'destiny' ),
  );

  register_taxonomy( 'destiny_category', array( 'destiny' ), array(
          'hierarchical'      => true,
          'labels'            => $labels,
          'show_ui'           => true,
          'show_admin_column' => true,
          'query_var'         => true,
    )
  );
}

function list_all_destinations($attr){

  $cat_args = array(
      'parent'       => 0,
      'number'        => 10,
      'hide_empty'    => false,
  );

  $taxonomies = 'destiny_category';

  $main_categories = get_terms($taxonomies,$cat_args);
  $content = "";
  foreach($main_categories as $cat){
    $content .= $cat->name . "<br>";
    $childrens = get_term_children( $cat->term_id, $taxonomies);
      foreach($childrens as $child){
        $city = get_term_by('id',$child,$taxonomies);
        $content .=  $city->name. "<br>";
        $the_query = new WP_Query(array(
          'post_type' => 'destiny',
          'numberposts' => -1,
          'tax_query' => array(
            array(
              'taxonomy' => $taxonomies,
              'field' => 'id',
              'terms' => $city->term_id, // Where term_id of Term 1 is "1".
            )
          )
        ));

     if ( $the_query->have_posts() ) {
       $content .=  '<ul>';
       while ( $the_query->have_posts() ) {
         $the_query->the_post();
         $content .= '<li><a href="'.get_permalink().'" >' . get_the_title() . '</a></li>';
       }
       $content .= '</ul>';
       /* Restore original Post Data */
       wp_reset_postdata();
     } else {
      	$content .= __("no posts found","destiny");
     }

   }
  }
  return $content;

}

add_shortcode( 'destinations', 'list_all_destinations' );
