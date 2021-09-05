<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/anand000/
 * @since      1.0.0
 *
 * @package    Page_Submission
 * @subpackage Page_Submission/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Page_Submission
 * @subpackage Page_Submission/public
 * @author     Anandaraj Balu <anandrajbalu00@gmail.com>
 */
class Page_Submission_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/page-submission-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/page-submission-public.js', array( 'jquery' ), $this->version, true );

		wp_localize_script(
			$this->plugin_name,
			'_wpps_ajax_var',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wpps-ajax-nonce' ),
			)
		);
	}

	/**
	 * Public hook Initialization.
	 *
	 * @since    1.0.0
	 */
	public function wpps_init_public() {
		add_shortcode( 'wpps-form', array( $this, 'wpps_display_front_end_post_form' ) );
		add_shortcode( 'wpps-blog', array( $this, 'wpps_get_post_list' ) );

		add_action( 'wp_ajax_nopriv_wpps_do_create_post', array( $this, 'wpps_ajax_create_post' ) );
		add_action( 'wp_ajax_wpps_do_create_post', array( $this, 'wpps_ajax_create_post' ) );
	}

	/**
	 * Display front-end post create form.
	 *
	 * @since    1.0.0
	 * @return void
	 */
	public function wpps_display_front_end_post_form() {
		ob_start();

		// Allow logged in user only.
		if ( ! is_user_logged_in() ) {
			echo '<p>Login required! Click here to <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '" >login</a>.</p>';
			return;
		}

		// Get custom post types.
		$args = array(
			'public'   => true,
			'_builtin' => false,
		);

		$output   = 'objects';
		$operator = 'and';

		$post_types = get_post_types( $args, $output, $operator );

		?>
		<div id="wpps-form-wrap">
			<h3>Add New Post</h3>
			<form action="" method="post" id="wpps-post-form" enctype="multipart/form-data">
				<div class="wpps-form-row">
					<label for="wpps_post_title">Post Title</label>
					<input type="text" name="wpps_post_title" id="wpps_post_title" class="form-control" size="40" required>
				</div>
				<div class="wpps-form-row">
					<label for="wpps_post_type">Post type</label>
					<select name="wpps_post_type" id="wpps_post_type" class="form-control" required>
						<option value="">Select post type</option>
					<?php
					foreach ( $post_types as $post_type ) {
						?>
						<option value="<?php echo esc_attr( $post_type->name ); ?>"><?php echo esc_html( $post_type->label ); ?></option>
						<?php
					}
					?>
					</select>
				</div>
				<div class="wpps-form-row">
					<label for="wpps_post_content">Description</label>
					<?php
					$content   = '';
					$editor_id = 'wpps_post_content';
					$settings  = array( 'media_buttons' => false );
					wp_editor( $content, $editor_id, $settings );
					?>
				</div>
				<div class="wpps-form-row">
					<label for="wpps_post_excerpt">Excerpt</label>
					<textarea name="wpps_post_excerpt" id="wpps_post_excerpt" cols="30" rows="3" class="form-control"></textarea>
				</div>
				<div class="wpps-form-row">
					<label for="wpps_post_featured_image">Featured image</label>
					<input type="file" name="wpps_post_featured_image" id="wpps_post_featured_image">
				</div>
				<div class="wpps-form-row">
					<input type="submit" value="Submit" class="wpps-btn">
				</div>
			</form>
			<div class="wpps-res"></div>
		</div>
			<?php
			return ob_get_clean();
	}

	/**
	 * Post form ajax action
	 *
	 * @since    1.0.0
	 */
	public function wpps_ajax_create_post() {

		// Check if user logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Security check.
		if ( ! isset( $_POST['wpps_nonce'] ) || ! wp_verify_nonce( $_POST['wpps_nonce'], 'wpps-ajax-nonce' ) ) {
			return;
		}

		// $_POST sanitization.
		$title     = wp_strip_all_tags( wp_unslash( $_POST['wpps_post_title'] ) );
		$post_type = sanitize_text_field( wp_unslash( $_POST['wpps_post_type'] ) );
		$content   = wp_kses_post( $_POST['wpps_post_content'] );
		$excerpt   = sanitize_textarea_field( wp_unslash( $_POST['wpps_post_excerpt'] ) );
		$author_id = get_current_user_id();

		if ( empty( $title ) ) {
			echo '<p style="color: red; border: 2px solid yellow; padding:10px; text-align: center">Title field mandatory!</p>';
			wp_die();
		}

		// Add the content of the form to $post as an array.
		$post = array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'post_status'  => 'draft',
			'post_type'    => $post_type,
			'post_author'  => $author_id,
		);

		$post_id = wp_insert_post( $post );
		$respone = '';

		if ( ! empty( $post_id ) ) {

			// send email to admin.
			$to      = get_option( 'admin_email' );
			$subject = 'Page/Post Moderation';
			$body    = 'New post/page have been successfully created by Guest user.';
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			wp_mail( $to, $subject, $body, $headers );

			if ( isset( $_FILES ) ) {
				// For Featured Image.
				$upload = wp_upload_bits( $_FILES['wpps_post_featured_image']['name'], null, file_get_contents( $_FILES['wpps_post_featured_image']['tmp_name'] ) );

				if ( ! $upload_file['error'] ) {
					$filename    = $upload['file'];
					$wp_filetype = wp_check_filetype( $filename, null );
					$attachment  = array(
						'post_mime_type' => $wp_filetype['type'],
						'post_title'     => sanitize_file_name( $filename ),
						'post_content'   => '',
						'post_status'    => 'inherit',
					);

					$attachment_id = wp_insert_attachment( $attachment, $filename, $post_id );

					if ( ! is_wp_error( $attachment_id ) ) {
						require_once ABSPATH . 'wp-admin/includes/image.php';

						$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
						wp_update_attachment_metadata( $attachment_id, $attachment_data );
						set_post_thumbnail( $post_id, $attachment_id );
					}
				} else {
					$respone = '<span style="color: red">and Failed to update attachment.</span>';
				}
			}
			echo '<p style="border: 2px solid green; padding:10px; text-align: center">Saved your post successfully! ' . esc_html( $respone ) . '</p>';
		} else {
			echo '<p style="color: red; border: 2px solid yellow; padding:10px; text-align: center">Failed to save your post!</p>';
		}

		wp_die();
	}

	/**
	 * Display post list by author
	 *
	 * @since    1.0.0
	 */
	public function wpps_get_post_list() {
		ob_start();

		// Get logged-in user id.
		$author_id = get_current_user_id();

		// Get custom post types.
		$args       = array(
			'public'   => true,
			'_builtin' => false,
		);
		$output     = 'names';
		$operator   = 'and';
		$post_types = get_post_types( $args, $output, $operator );

		$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
		$query = new WP_Query(
			array(
				'post_type'      => $post_types,
				'author'         => $author_id,
				'posts_per_page' => 10,
				'paged'          => $paged,
			)
		);

		if ( $query->have_posts() ) {
			echo '<div class="" id="wpps-blog-wrap" >';
			while ( $query->have_posts() ) {
				$query->the_post();
				?>
					<div class="wpps-blog-item">
						<h3><?php the_title(); ?></h3>
					<?php the_excerpt(); ?>
						<a href="<?php the_permalink(); ?>">More...</a>
					</div>
				<?php
			}
			echo '</div>';
			$big = 999999999;
			?>
			<div class="wpps-paninate-link">
				<?php
				echo paginate_links(
					array(
						'base'    => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
						'format'  => '?paged=%#%',
						'current' => max( 1, get_query_var( 'paged' ) ),
						'total'   => $query->max_num_pages,
					)
				);
				?>

			</div>
			<?php
		}
		wp_reset_postdata();
		return ob_get_clean();
	}

}
