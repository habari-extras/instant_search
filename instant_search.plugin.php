<?php

class InstantSearch extends Plugin
{
	/**
	 * Register the template.
	 **/
	function action_init()
	{
		$this->add_template( "block.instant_search", dirname( __FILE__ ) . "/block.instant_search.php" );
	}

	/**
	 * Add block to the list of selectable blocks
	 **/
	public function filter_block_list( $block_list )
	{
		$block_list[ 'instant_search' ] = _t( 'Instant Search', 'instant_search' );
		return $block_list;
	}

	/**
	 * Add required Javascript
	 */
	public function theme_header($theme)
	{
		// Add the jQuery library
		Stack::add( 'template_header_javascript', Site::get_url('scripts') . '/jquery.js', 'jquery' );
		Stack::add( 'template_header_javascript', $this->get_url() . '/instant_search.js', 'instant_search', array( 'jquery' ) );

		// Add the callback URL.
		$url = "InstantSearch.url = '" . URL::get( 'ajax', array( 'context' => 'instant_search') ) . "';";
		Stack::add('template_header_javascript', $url, 'instant_search_url', 'instant_search');
	}

	/**
	 * Respond to Javascript callbacks
	 * The name of this method is action_ajax_ followed by what you passed to the context parameter above.
	 */
	public function action_ajax_instant_search( $handler )
	{
		// Get the data that was sent
		$response = $handler->handler_vars[ 'q' ];

		// Wipe anything else that's in the buffer
		ob_end_clean();

		$new_response = Posts::get( array( "criteria"=>$response ) );
		
		$final_response = array();
		foreach ( $new_response as $post ) {
						
			$final_response[] = array(
				'title' => $post->title,
				'content' => $post->content,
				'pubdate' => $post->pubdate->format(),
				'updated' => $post->updated->format(),
				'modified' => $post->modified->format(),
				'url' => $post->permalink,
			);
		}
		// Send the response
		echo json_encode( $final_response );
	}
}
?>
