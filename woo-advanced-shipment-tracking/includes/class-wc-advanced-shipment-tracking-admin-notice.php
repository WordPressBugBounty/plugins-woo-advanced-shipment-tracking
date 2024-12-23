<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Advanced_Shipment_Tracking_Admin_Notice {

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {
		$this->init();	
	}
	
	/**
	 * Get the class instance
	 *
	 * @return WC_Advanced_Shipment_Tracking_Admin_Notice
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/*
	* init from parent mail class
	*/
	public function init() {

		//add_action( 'admin_notices', array( $this, 'ast_pro_v_3_4_admin_notice' ) );	
		//add_action( 'admin_init', array( $this, 'ast_pro_v_3_4_admin_notice_ignore' ) );

		add_action( 'ast_settings_admin_notice', array( $this, 'ast_settings_admin_notice' ) );
		add_action( 'admin_init', array( $this, 'ast_settings_admin_notice_ignore' ) );

		//add_action( 'admin_notices', array( $this, 'ast_fulfillment_survay' ) );	
		//add_action( 'admin_init', array( $this, 'ast_fulfillment_survay_ignore' ) );
		
		add_action( 'before_shipping_provider_list', array( $this, 'ast_db_update_notice' ) );	
		add_action( 'admin_init', array( $this, 'ast_db_update_notice_ignore' ) );

		add_action( 'admin_notices', array( $this, 'ast_trackship_notice' ) );	
		add_action( 'admin_init', array( $this, 'ast_trackship_notice_ignore' ) );

		add_action( 'admin_notices', array( $this, 'ast_return_for_woocommerce_notice' ) );
		add_action( 'admin_init', array( $this, 'ast_return_for_woocommerce_notice_ignore' ) );

	}
	
	public function ast_settings_admin_notice() {

		$ignore = get_transient( 'ast_settings_admin_notice_ignore' );
		if ( 'yes' == $ignore ) {
			return;
		}

		include 'views/admin_message_panel.php';
	}

	public function ast_settings_admin_notice_ignore() {
		if ( isset( $_GET['ast-pro-settings-ignore-notice'] ) ) {
			// if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'ast_dismiss_notice')) {
			// 	set_transient( 'ast_settings_admin_notice_ignore', 'yes', 2592000 );
			// }

			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'ast_dismiss_notice')) {
					set_transient( 'ast_settings_admin_notice_ignore', 'yes', 2592000 );
				}
			}
		}
	}
	
	public function ast_fulfillment_survay() {
		
		if ( get_option('ast_fulfillment_survay_notice_ignore') ) {
			return;
		}	
		
		$current_user = wp_get_current_user();
		$display_name = $current_user->display_name;

		$dismissable_url = esc_url( add_query_arg( 'ast-fulfillment-survay-notice-ignore', 'true' ) );
		?>		
		<style>		
		.wp-core-ui .notice.ast-dismissable-notice{
			position: relative;
			padding-right: 38px;
			border-left-color: #005B9A;
			padding-bottom:10px;
		}
		.wp-core-ui .notice.ast-dismissable-notice h3{
			margin-bottom: 5px;
		} 
		.wp-core-ui .notice.ast-dismissable-notice a.notice-dismiss{
			padding: 9px;
			text-decoration: none;
		} 
		.wp-core-ui .button-primary.ast_notice_btn {
			background: #005B9A;
			color: #fff;
			border-color: #005B9A;
			text-transform: uppercase;
			padding: 0 11px;
			font-size: 12px;
			height: 30px;
			line-height: 28px;
			margin: 5px 0 15px;
		}
		</style>
		<div class="notice updated notice-success ast-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			
			
			<h2>Help us improve AST - Take our 2023 fulfillment operations survey</h2>
			<p>Dear <?php esc_html_e( $display_name ); ?>, help us improve our plugins by taking our quick survey on WooCommerce fulfillment operations. <a href="https://forms.gle/frPAoYEMK2LCpjcVA" target="blank">Your feedback matters!</a></p>
			<p>Thank you, zorem Team.</p>			
		</div>	
		<?php	
	}

	public function ast_fulfillment_survay_ignore() {
		if ( isset( $_GET['ast-fulfillment-survay-notice-ignore'] ) ) {
			update_option( 'ast_fulfillment_survay_notice_ignore', 'true' );
		}
	}
	/*
	* Display admin notice on plugin install or update
	*/
	public function ast_pro_v_3_4_admin_notice() { 		
		
		if ( class_exists( 'ast_pro' ) ) {
			return;
		}
		
		$date_now = gmdate( 'Y-m-d' );

		if ( get_option('ast_pro_v_3_4_admin_notice_ignore') || $date_now > '2022-05-31' ) {
			return;
		}	
		
		$dismissable_url = esc_url(  add_query_arg( 'ast-pro-v-3-4-ignore-notice', 'true' ) );
		?>
		<style>		
		.wp-core-ui .notice.ast-dismissable-notice{
			position: relative;
			padding-right: 38px;
			border-left-color: #005B9A;
		}
		.wp-core-ui .notice.ast-dismissable-notice h3{
			margin-bottom: 5px;
		} 
		.wp-core-ui .notice.ast-dismissable-notice a.notice-dismiss{
			padding: 9px;
			text-decoration: none;
		} 
		.wp-core-ui .button-primary.ast_notice_btn {
			background: #005B9A;
			color: #fff;
			border-color: #005B9A;
			text-transform: uppercase;
			padding: 0 11px;
			font-size: 12px;
			height: 30px;
			line-height: 28px;
			margin: 5px 0 15px;
		}
		</style>
		<div class="notice updated notice-success ast-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			
			
			<p>Get 20% when you upgrade to <a target="blank" href="https://www.zorem.com/ast-pro/?utm_source=wp-admin&utm_medium=ast-notice-dismiss&utm_campaign=upgrad-now">Advanced Shipment Tracking Pro</a> by 05/31! AST PRO will automate your fulfillment workflow, will save you time on your daily tasks and will keep your customers happy and informed on their shipped orders. Use code <strong>ASTPRO20</strong> to redeem your discount (valid by May 31st, 2022).</p>			
			
			<a class="button-primary ast_notice_btn" target="blank" href="https://www.zorem.com/ast-pro/?utm_source=wp-admin&utm_medium=ast-notice-dismiss&utm_campaign=upgrad-now">Upgrade Now</a>
			<a class="button-primary ast_notice_btn" href="<?php esc_html_e( $dismissable_url ); ?>">Dismiss</a>				
		</div>	
		<?php 				
	}	
	
	/*
	* Dismiss admin notice for trackship
	*/
	public function ast_pro_v_3_4_admin_notice_ignore() {
		if ( isset( $_GET['ast-pro-v-3-4-ignore-notice'] ) ) {
			update_option( 'ast_pro_v_3_4_admin_notice_ignore', 'true' );
		}
	}		
	
	/*
	* Dismiss admin notice for trackship
	*/
	public function ast_pro_admin_notice_ignore() {
		if ( isset( $_GET['ast-pro-1-3-4-ignore-notice'] ) ) {
			update_option( 'ast_pro_1_3_4_admin_notice_ignore', 'true' );
		}
	}	
	
	/*
	* Display admin notice on plugin install or update
	*/
	public function ast_db_update_notice() { 		
		
		if ( get_option('ast_3_6_6_db_update_notice_updated_ignore') ) {
			return;
		}	
		
		$dismissable_url = esc_url(  add_query_arg( 'ast-3-6-6-db-update-notice-updated-ignore', 'true' ) );
		// $update_providers_url = esc_url( admin_url( '/admin.php?page=woocommerce-advanced-shipment-tracking&tab=shipping-providers&open=synch_providers' ) );
		?>
		<style>		
		.wp-core-ui .notice.ast-pro-dismissable-notice a.notice-dismiss{
			padding:13px 10px;
			text-decoration: none;
		}
		.wp-core-ui .button-primary.ast_notice_btn {
			background: #005B9A;
			color: #fff;
			border-color: #005B9A;
			padding: 0 11px;
			font-size: 12px;
			height: 30px;
			line-height: 28px;
			margin: 5px 0 15px;
		}
		.ast-notice{
			background: #fff;
			border: 1px solid #e0e0e0;
			margin: 0 0 25px;
			padding: 1px 12px;
			box-shadow: none;
		}
		.ast_notice_p{
			display:inline-block;
			margin:10px 0 !important;
		}
		</style>	
		<div class="ast-notice notice notice-success is-dismissible ast-pro-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			
			<p class="ast_notice_p">Shipping carriers update is available, please click on update carriers to update the shipping providers list.</p>
			<!-- <a class="button-primary ast_notice_btn" href="<?php //esc_html_e( $update_providers_url ); ?>">Update Providers</a>	 -->
			<button class="sync_providers shipping_carriers_notice_btn" target="_blank">UPDATE CARRIERS</button>		
		</div>
	<?php 		
	}	
	
	/*
	* Dismiss admin notice for trackship
	*/
	public function ast_db_update_notice_ignore() {
		if ( isset( $_GET['ast-3-6-6-db-update-notice-updated-ignore'] ) ) {
			update_option( 'ast_3_6_6_db_update_notice_updated_ignore', 'true' );
		}
		if ( isset( $_GET['open'] ) && 'synch_providers' == $_GET['open'] ) {
			update_option( 'ast_3_6_6_db_update_notice_updated_ignore', 'true' );
		}
	}

	/*
	* Display admin notice on plugin install or update
	*/
	public function ast_trackship_notice() { 		
		
		$ts4wc_installed = ( function_exists( 'trackship_for_woocommerce' ) ) ? true : false;
		if ( $ts4wc_installed ) {
			return;
		}
		
		if ( get_option('ast_trackship_notice_ignore') ) {
			return;
		}	
		
		$nonce = wp_create_nonce('ast_trackship_dismiss_notice');
		$dismissable_url = esc_url(add_query_arg(['ast-trackship-notice' => 'true', 'nonce' => $nonce]));

		?>
		<style>		
		.wp-core-ui .notice.ast-dismissable-notice{
			position: relative;
			padding-right: 38px;
			border-left-color: #005B9A;
		}
		.wp-core-ui .notice.ast-dismissable-notice h3{
			margin-bottom: 5px;
		} 
		.wp-core-ui .notice.ast-dismissable-notice a.notice-dismiss{
			padding: 9px;
			text-decoration: none;
		} 
		.wp-core-ui .button-primary.ast_notice_btn {
			background: #005B9A;
			color: #fff;
			border-color: #005B9A;
			text-transform: uppercase;
			padding: 0 11px;
			font-size: 12px;
			height: 30px;
			line-height: 28px;
			margin: 5px 0 15px;
		}
		</style>
		<div class="notice updated notice-success ast-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			
			<h3>Supercharge Customer Experience!</h3>
			<p>Enhance your WooCommerce store with TrackShip's powerful shipment tracking features! Streamline your post-purchase experience, reduce customer inquiries, boost customer satisfaction and build long lasting relationships with your customers</p>				
			
			<a class="button-primary ast_notice_btn" target="blank" href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=search&s=TrackShip For WooCommerce&plugin-search-input=Search+Plugins' ) ); ?>">Install TrackShip for WooCommerce</a>
			<a class="button-primary ast_notice_btn" href="<?php esc_html_e( $dismissable_url ); ?>">Dismiss</a>				
		</div>	
		<?php 				
	}	
	
	/*
	* Dismiss admin notice for trackship
	*/
	public function ast_trackship_notice_ignore() {
		if ( isset( $_GET['ast-trackship-notice'] ) ) {
			// if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'ast_trackship_dismiss_notice')) {
			// 	update_option( 'ast_trackship_notice_ignore', 'true' );
			// }
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'ast_trackship_dismiss_notice')) {
					update_option('ast_trackship_notice_ignore', 'true');
				}
			}
			
		}
	}

	/*
	* Dismiss admin notice for return
	*/
	public function ast_return_for_woocommerce_notice_ignore() {
		if ( isset( $_GET['ast-return-for-woocommerce-notice'] ) ) {
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'ast_return_for_woocommerce_dismiss_notice')) {
					update_option('ast_return_for_woocommerce_notice_ignore', 'true');
				}
			}
			
		}
	}

	/*
	* Display admin notice on plugin install or update
	*/
	public function ast_return_for_woocommerce_notice() { 		
		
		$return_installed = ( function_exists( 'zorem_returns_exchanges' ) ) ? true : false;
		if ( $return_installed ) {
			return;
		}
		
		if ( get_option('ast_return_for_woocommerce_notice_ignore') ) {
			return;
		}	
		
		$nonce = wp_create_nonce('ast_return_for_woocommerce_dismiss_notice');
		$dismissable_url = esc_url(add_query_arg(['ast-return-for-woocommerce-notice' => 'true', 'nonce' => $nonce]));

		?>
		<style>		
		.wp-core-ui .notice.ast-dismissable-notice{
			position: relative;
			padding-right: 38px;
			border-left-color: #005B9A;
		}
		.wp-core-ui .notice.ast-dismissable-notice h3{
			margin-bottom: 5px;
		} 
		.wp-core-ui .notice.ast-dismissable-notice a.notice-dismiss{
			padding: 9px;
			text-decoration: none;
		} 
		.wp-core-ui .button-primary.ast_notice_btn {
			background: #005B9A;
			color: #fff;
			border-color: #005B9A;
			text-transform: uppercase;
			padding: 0 11px;
			font-size: 12px;
			height: 30px;
			line-height: 28px;
			margin: 5px 0 15px;
		}
		.ast-dismissable-notice strong{
			font-weight: bold;
		}
		</style>
		<div class="notice updated notice-success ast-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			
			<h3>Launching Zorem Returns!</h3>
			<p>We’re thrilled to announce the launch of our new <a href="https://www.zorem.com/product/zorem-returns/"><strong>Zorem Returns Plugin!</strong></a> This powerful tool is designed to streamline and automate your returns and exchanges management process, freeing up your time to focus on what truly matters—growing your business.</p>				
			<p><strong>Act fast!</strong> For a limited time you can enjoy an exclusive <strong>40% discount</strong> on Zorem Returns Plugin with the coupon code <strong>RETURNS40.</strong> Don’t miss out—the offer expires 2 weeks after installing this plugin or update.</p>
			<a class="button-primary ast_notice_btn" target="blank" href="<?php echo esc_url( 'https://www.zorem.com/product/zorem-returns/' ); ?>">Unlock 40% Off</a>
			<a class="button-primary ast_notice_btn" href="<?php esc_html_e( $dismissable_url ); ?>">Dismiss</a>				
		</div>
		<?php 				
	}
}
