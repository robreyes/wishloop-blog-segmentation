<?php

class WBS_Segmentation {



	public $VERSION = '1.0';

	protected $plugin_slug = 'wbs_seg';

	protected static $instance = null;

	private $custom_meta_prefix = 'wbs_seg_custom_';

	private $settings_name = 'wbs_seg_settings';

	private static $default_settings = null;



	public function __construct() {

		

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ));

		add_action( 'save_post', array( $this, 'save_custom_meta' ) );

		add_action( 'init', array( $this, 'plugin_init' ), 0 );
		
		
	}
	

	public function lead_magnet_title() {

	

	register_post_type( 'leadmagnet',

    array(

      'labels' => array(

        'name' => __( 'Lead Magnet' ),

        'singular_name' => __( 'Lead Magnet' )

      ),

      'public' => true,

      'has_archive' => true,

      'rewrite' => array('slug' => 'lead-magnet'),

    )

  );

		

	}

	

	public static function activate( $network_wide ) {



	

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {



			if ( $network_wide  ) {



				// Get all blog ids

				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );

					self::single_activate();

				}

				restore_current_blog();

			} else {

				self::single_activate();

			}

			

		} else {

			self::single_activate();

		}

	}



	private static function single_activate() {

		// @TODO: Define activation functionality here

	}



	private static function single_deactivate() {

		// @TODO: Define deactivation functionality here

	}







	public static function deactivate( $network_wide ) {



		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids

				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );

					self::single_deactivate();

				}

				restore_current_blog();

			} else {

				self::single_deactivate();

			}

		} else {

			self::single_deactivate();

		}

	}

	public function add_meta_box( $post_type ) {

        $post_types = array('post', 'page');     //limit meta box to certain post types

        if ( in_array( $post_type, $post_types )) {

			add_meta_box(

				'some_meta_box_name'

				,'Lead Magnet'

				,array( $this, 'show_custom_meta_box' )

				,$post_type

				,'advanced'

				,'high'

			);

        }

	}



	/**

	 * Save the meta when the post is saved.

	 *

	 * @param int $post_id The ID of the post being saved.

	 */

	public function show_custom_meta_box() {

		global $post;

		echo '

		<div id="loader">

			<div id="facebookG">

				<div id="blockG_1" class="facebook_blockG"></div>

				<div id="blockG_2" class="facebook_blockG"></div>

				<div id="blockG_3" class="facebook_blockG"></div>

			</div>

			<div class="clear"></div>

		</div>';

		echo '<input type="hidden" name="custom_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';

		echo '<table class="form-table">';

		foreach ( $this->custom_meta_fields as $field ) {

			$meta = get_post_meta( $post->ID, $field['id'], true );

			echo '<tr><th><label for="' . $field['id'] . '">' . $field['label'] . '</label></th><td>';

			switch ( $field['type'] ) {

				//text

				case 'text':

					echo '<input class="' . $field['class'] . '" type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="60" />

                        <br /><span class="description">' . $field['desc'] . '</span>';

					break;

				//textarea

				case 'textarea':

					echo '<textarea name="' . $field['id'] . '" id="' . $field['id'] . '" cols="60" rows="4">' . $meta . '</textarea>

                        <br /><span class="description">' . $field['desc'] . '</span>';

					break;

				//checkbox

				case 'checkbox':

					echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" ', $meta ? ' checked="checked"' : '', '/>

                        <label for="' . $field['id'] . '">' . $field['desc'] . '</label>';

					break;

					

				//select

				case 'select':

					echo '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';

 					foreach ( $field['options'] as $option ) {

						if ($meta != null){

							echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="' . $option['value'] . '">' . $option['label'] . '</option>';

						}else{

							echo '<option', $option['default']=='yes' ? ' selected="selected"' : '', ' value="' . $option['value'] . '">' . $option['label'] . '</option>';

						}

					} 

					

					break;

					

				//date

				case 'date':

					echo '<input type="text" class="datepicker" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="30" />

			            <br /><span class="description">' . $field['desc'] . '</span>';

					break;

				//image

				case 'image':

					$image = get_template_directory_uri() . '/images/image.png';

					echo '<span class="custom_default_image" style="display:none">' . $image . '</span>';

					if ( $meta ) {

						$image = wp_get_attachment_image_src( $meta, 'medium' );

						$image = $image[0];

					}

					echo '<input name="' . $field['id'] . '" type="hidden" class="custom_upload_image" value="' . $meta . '" />

                        <img src="' . $image . '" class="custom_preview_image" alt="" /><br />

                    <input class="custom_upload_image_button button" type="button" value="Choose Image" />

                    <small> <a href="#" class="custom_clear_image_button">Remove Image</a></small>';

					break;

			}

			echo '</td></tr>';

		}

		echo '</table>';

	  }



	public function save_custom_meta( $post_id ) {

			if ( isset( $_POST['custom_meta_box_nonce'] ) ) {

				$nonce = $_POST['custom_meta_box_nonce'];

			} else {

				$nonce = false;

			}

			if ( ! wp_verify_nonce( $nonce, basename( __FILE__ ) ) ) {

				return $post_id;

			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {

				return $post_id;

			}

			if ( 'page' == $_POST['post_type'] ) {

				if ( ! current_user_can( 'edit_page', $post_id ) ) {

					return $post_id;

				}

			} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {

				return $post_id;

			}

			foreach ( $this->custom_meta_fields as $field ) {

				$old = get_post_meta( $post_id, $field['id'], true );

				$new = $_POST[$field['id']];

				if ( $new && $new != $old ) {

					update_post_meta( $post_id, $field['id'], $new );

				} elseif ( '' == $new && $old ) {

					delete_post_meta( $post_id, $field['id'], $old );

				}

			}

		update_post_meta( $post_id, 'scrape_image', $_POST['scrape_image']);

	}



	public function plugin_init() {

		$this->custom_meta_fields = array(

		
			array(

				'label' => 'Select Lead Magnet *',

				'desc'  => 'This field is required',

				'id'    => $this->custom_meta_prefix . 'lead_magnet_post',

				'type'  => 'select',

				'options' =>$this->getLeadMagnetpost(), 

				

				'class' => 'lead_magnet_post'

			)

		);

		

		

		

	}

	/* hook ka sa wp_head para macapture mo ung metapost attributes */

	public static function getLeadMagnetpost() {

		global $post;

		global $wpdb;

		

		$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."posts WHERE post_type = 'leadmagnet'", OBJECT );

		if (count($results)>0){

			

			for ($df=0; $df < count($results); $df++ ){

			$allLMpost[$df] = array('label'=>$results[$df]->post_title ,'value' => $results[$df]->post_title);

			}

			$allLMpost[count($results)+1] = array('label'=> 'Select Lead Magnet', 'value'=>'','default'=>'yes');

			

		}



		return $allLMpost ;



	}

}

