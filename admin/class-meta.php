<?php
/**
 * @package Admin
 */

class Lazyload_Videos_Meta {

	private $select_thumbnail_quality = 'lazyload_thumbnail_quality';

	function __construct() {
		$this->init_meta_boxes();
	}

	/**
	 * Add additonal fields to the page where you create your posts and pages
	 * (Based on http://wp.tutsplus.com/tutorials/plugins/how-to-create-custom-wordpress-writemeta-boxes/)
	 */
	function init_meta_boxes() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	function add_meta_box() {
		$lazyload_videos_general = new Lazyload_Videos_General();
		
		$screens = $lazyload_videos_general->get_post_types();

		foreach ( $screens as $screen ) {
			add_meta_box(
				'meta-box-lazyload-for-videos',
				'Lazyload for Videos',
				array( $this, 'meta_box' ),
				$screen,
				'side',	// position
				'high'	// priority
			);
		}

	}

	function meta_box( $post ) {
		$values = get_post_custom( $post->ID );
		// $text = isset( $values['oembed_link'] ) ? esc_attr( $values['oembed_link'][0] ) : '';
		// $check = isset( $values['lazyload_check_custom'] ) ? esc_attr( $values['lazyload_check_custom'][0] ) : '';
		
		$select_thumbnail_quality = $this->select_thumbnail_quality;
			$selected = isset( $values[$select_thumbnail_quality] ) ? esc_attr( $values[$select_thumbnail_quality][0] ) : '';

		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );

		?>

<!-- 		<h4>Custom Text</h4>
		<p>
			<p>Description &hellip;</p>
			<input type="text" name="oembed_link" id="oembed_link" value="<?php echo $text; ?>" style="width:100%;" />
			<label for="oembed_link">E.g. <i>example</i></label>	
		</p> -->

<!-- 		<h4>Checkbox</h4>
		<p>
			<input type="checkbox" name="lazyload_check_custom" id="lazyload_check_custom" <?php checked( $check, 'on' ); ?> />
			<label for="lazyload_check_custom">If checked: Display ...</label>
		</p> -->

		<h4>Youtube thumbnail quality</h4>
		<p>
			<select class="select" type="select" name="<?= $select_thumbnail_quality; ?>" id="<?= $select_thumbnail_quality; ?>">
			<?php $meta_element_class = get_post_meta($post->ID, $select_thumbnail_quality, true);	?>
		      <option value="default" <?php selected( $meta_element_class, 'default' ); ?>>Default</option>
		      <option value="0" <?php selected( $meta_element_class, '0' ); ?>>Standard quality</option>
		      <option value="max" <?php selected( $meta_element_class, 'max' ); ?>>Max resolution</option>
			</select>
		</p>

	<?php }

	function save( $post_id ) {

		// Bail if we're doing an auto save
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		// If our nonce isn't there, or we can't verify it, bail
		if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
		
		// Now we can actually save the data
		$allowed = array( 
			'a' => array( // on allow a tags
				'href' => array() // and those anchords can only have href attribute
			)
		);
		
		// Probably a good idea to make sure your data is set

		// TEXTBOX
		// if( isset( $_POST['oembed_link'] ) )
		// 	update_post_meta( $post_id, 'oembed_link', wp_kses( $_POST['oembed_link'], $allowed ) );

		// CHECKBOX
		// $chk = ( isset( $_POST['lazyload_check_custom'] ) && $_POST['lazyload_check_custom'] ) ? 'on' : 'off';
		// update_post_meta( $post_id, 'lazyload_check_custom', $chk );

		// SELECT
		$select_thumbnail_quality = $this->select_thumbnail_quality;
		if( isset( $_POST[$select_thumbnail_quality] ) )
			update_post_meta( $post_id, $select_thumbnail_quality, esc_attr( $_POST[$select_thumbnail_quality] ) );	
	}

}

new Lazyload_Videos_Meta();