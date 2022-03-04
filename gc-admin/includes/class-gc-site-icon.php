<?php
/**
 * Administration API: GC_Site_Icon class
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/**
 * Core class used to implement site icon functionality.
 *
 *
 */
class GC_Site_Icon {

	/**
	 * The minimum size of the site icon.
	 *
	 * @var int
	 */
	public $min_size = 512;

	/**
	 * The size to which to crop the image so that we can display it in the UI nicely.
	 *
	 * @var int
	 */
	public $page_crop = 512;

	/**
	 * List of site icon sizes.
	 *
	 * @var int[]
	 */
	public $site_icon_sizes = array(
		/*
		 * Square, medium sized tiles for IE11+.
		 *
		 * See https://msdn.microsoft.com/library/dn455106(v=vs.85).aspx
		 */
		270,

		/*
		 * App icon for Android/Chrome.
		 *
		 * @link https://developers.google.com/web/updates/2014/11/Support-for-theme-color-in-Chrome-39-for-Android
		 * @link https://developer.chrome.com/multidevice/android/installtohomescreen
		 */
		192,

		/*
		 * App icons up to iPhone 6 Plus.
		 *
		 * See https://developer.apple.com/library/prerelease/ios/documentation/UserExperience/Conceptual/MobileHIG/IconMatrix.html
		 */
		180,

		// Our regular Favicon.
		32,
	);

	/**
	 * Registers actions and filters.
	 *
	 */
	public function __construct() {
		add_action( 'delete_attachment', array( $this, 'delete_attachment_data' ) );
		add_filter( 'get_post_metadata', array( $this, 'get_post_metadata' ), 10, 4 );
	}

	/**
	 * Creates an attachment 'object'.
	 *
	 *
	 * @param string $cropped              Cropped image URL.
	 * @param int    $parent_attachment_id Attachment ID of parent image.
	 * @return array Attachment object.
	 */
	public function create_attachment_object( $cropped, $parent_attachment_id ) {
		$parent     = get_post( $parent_attachment_id );
		$parent_url = gc_get_attachment_url( $parent->ID );
		$url        = str_replace( gc_basename( $parent_url ), gc_basename( $cropped ), $parent_url );

		$size       = gc_getimagesize( $cropped );
		$image_type = ( $size ) ? $size['mime'] : 'image/jpeg';

		$object = array(
			'ID'             => $parent_attachment_id,
			'post_title'     => gc_basename( $cropped ),
			'post_content'   => $url,
			'post_mime_type' => $image_type,
			'guid'           => $url,
			'context'        => 'site-icon',
		);

		return $object;
	}

	/**
	 * Inserts an attachment.
	 *
	 *
	 * @param array  $object Attachment object.
	 * @param string $file   File path of the attached image.
	 * @return int           Attachment ID
	 */
	public function insert_attachment( $object, $file ) {
		$attachment_id = gc_insert_attachment( $object, $file );
		$metadata      = gc_generate_attachment_metadata( $attachment_id, $file );

		/**
		 * Filters the site icon attachment metadata.
		 *
		 *
		 * @see gc_generate_attachment_metadata()
		 *
		 * @param array $metadata Attachment metadata.
		 */
		$metadata = apply_filters( 'site_icon_attachment_metadata', $metadata );
		gc_update_attachment_metadata( $attachment_id, $metadata );

		return $attachment_id;
	}

	/**
	 * Adds additional sizes to be made when creating the site icon images.
	 *
	 *
	 * @param array[] $sizes Array of arrays containing information for additional sizes.
	 * @return array[] Array of arrays containing additional image sizes.
	 */
	public function additional_sizes( $sizes = array() ) {
		$only_crop_sizes = array();

		/**
		 * Filters the different dimensions that a site icon is saved in.
		 *
		 *
		 * @param int[] $site_icon_sizes Array of sizes available for the Site Icon.
		 */
		$this->site_icon_sizes = apply_filters( 'site_icon_image_sizes', $this->site_icon_sizes );

		// Use a natural sort of numbers.
		natsort( $this->site_icon_sizes );
		$this->site_icon_sizes = array_reverse( $this->site_icon_sizes );

		// Ensure that we only resize the image into sizes that allow cropping.
		foreach ( $sizes as $name => $size_array ) {
			if ( isset( $size_array['crop'] ) ) {
				$only_crop_sizes[ $name ] = $size_array;
			}
		}

		foreach ( $this->site_icon_sizes as $size ) {
			if ( $size < $this->min_size ) {
				$only_crop_sizes[ 'site_icon-' . $size ] = array(
					'width ' => $size,
					'height' => $size,
					'crop'   => true,
				);
			}
		}

		return $only_crop_sizes;
	}

	/**
	 * Adds Site Icon sizes to the array of image sizes on demand.
	 *
	 *
	 * @param string[] $sizes Array of image size names.
	 * @return string[] Array of image size names.
	 */
	public function intermediate_image_sizes( $sizes = array() ) {
		/** This filter is documented in gc-admin/includes/class-gc-site-icon.php */
		$this->site_icon_sizes = apply_filters( 'site_icon_image_sizes', $this->site_icon_sizes );
		foreach ( $this->site_icon_sizes as $size ) {
			$sizes[] = 'site_icon-' . $size;
		}

		return $sizes;
	}

	/**
	 * Deletes the Site Icon when the image file is deleted.
	 *
	 *
	 * @param int $post_id Attachment ID.
	 */
	public function delete_attachment_data( $post_id ) {
		$site_icon_id = get_option( 'site_icon' );

		if ( $site_icon_id && $post_id == $site_icon_id ) {
			delete_option( 'site_icon' );
		}
	}

	/**
	 * Adds custom image sizes when meta data for an image is requested, that happens to be used as Site Icon.
	 *
	 *
	 * @param null|array|string $value    The value get_metadata() should return a single metadata value, or an
	 *                                    array of values.
	 * @param int               $post_id  Post ID.
	 * @param string            $meta_key Meta key.
	 * @param bool              $single   Whether to return only the first value of the specified `$meta_key`.
	 * @return array|null|string The attachment metadata value, array of values, or null.
	 */
	public function get_post_metadata( $value, $post_id, $meta_key, $single ) {
		if ( $single && '_gc_attachment_backup_sizes' === $meta_key ) {
			$site_icon_id = get_option( 'site_icon' );

			if ( $post_id == $site_icon_id ) {
				add_filter( 'intermediate_image_sizes', array( $this, 'intermediate_image_sizes' ) );
			}
		}

		return $value;
	}
}
