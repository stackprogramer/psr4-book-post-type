<?php

namespace Helper;

class Core
{
    /**
     * @return mixed
     */
    public static function getRandomEmoji()
    {
        $items = Array(':)', ':(', ':|', ':D');

        return $items[array_rand($items)];
    }
	
	 public static function isbn_number_meta_box_callback( $post ) {

			// Add a meta box field so we can check for it later.
			wp_nonce_field( 'isbn_number_nonce', 'isbn_number_nonce' );
			$value = get_post_meta( $post->ID, '_isbn_number', true );
			echo '<textarea style="width:100%" id="isbn_number" name="isbn_number">' . esc_attr( $value ) . '</textarea>';
			printf( __( 'The post type is: %s', 'book-post-type' ), get_post_type( get_the_ID() ) );
			//get_id_book();
    }

}