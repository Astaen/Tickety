<?php
/*
Plugin Name: Tickety
Plugin URI: http://astaen.fr/tickety/
Version: 0.1
Author: Astaen
Description: Adds a ticketing system for your events. Available as a standalone.
*/

define("TY_PATH", plugin_dir_path( __FILE__ ));
define("TY_URL", plugins_url() . "/tickety/");
define("DEV", true);

class Tickety {

	private $notice = "";
	private $paypal_settings = false;

	public static function install() {
		global $wpdb;
		$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ty_bonus` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,  `name` varchar(32) COLLATE utf8_bin NOT NULL, `discount` int(11) DEFAULT NULL) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;");
		$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ty_buyer` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,  `fname` varchar(32) COLLATE utf8_bin NOT NULL,  `lname` varchar(32) COLLATE utf8_bin NOT NULL,  `adress` varchar(64) COLLATE utf8_bin NOT NULL,  `city` varchar(32) COLLATE utf8_bin NOT NULL,  `p_code` int(11) NOT NULL,  `mail` varchar(32) COLLATE utf8_bin NOT NULL,  `ticket_id` int(11) NOT NULL,  `ticket_coupon` varchar(32) COLLATE utf8_bin DEFAULT NULL,  `ticket_options` varchar(1024) COLLATE utf8_bin NOT NULL,  `ticket_token` varchar(6) COLLATE utf8_bin NOT NULL, `total` float DEFAULT '0', `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6;");
		$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ty_coupon` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,  `token` varchar(32) COLLATE utf8_bin NOT NULL,  `bonus` int(11) NOT NULL, `used` tinyint(1) NOT NULL DEFAULT '0') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16;");
		$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ty_event` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,  `name` varchar(64) COLLATE utf8_bin NOT NULL,  `description` varchar(500) COLLATE utf8_bin NOT NULL,  `date_start` date DEFAULT '0000-00-00',  `date_end` date NOT NULL DEFAULT '0000-00-00',  `enabled` tinyint(1) NOT NULL DEFAULT '1') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;");
		$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ty_settings` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,  `paypal_usr` varchar(64) COLLATE utf8_bin NOT NULL,  `paypal_pwd` varchar(64) COLLATE utf8_bin NOT NULL,  `paypal_sign` varchar(64) COLLATE utf8_bin NOT NULL,  `paypal_devusr` varchar(64) COLLATE utf8_bin NOT NULL,  `paypal_devpwd` varchar(64) COLLATE utf8_bin NOT NULL,  `paypal_devsign` varchar(64) COLLATE utf8_bin NOT NULL,  `dev_mode` tinyint(1) NOT NULL DEFAULT '0',  `use_sandbox` tinyint(1) DEFAULT '0',  `thank_you_msg` text COLLATE utf8_bin NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2;");
		$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ty_ticket` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,  `event_id` int(11) NOT NULL,  `name` varchar(64) COLLATE utf8_bin NOT NULL,  `description` varchar(512) COLLATE utf8_bin NOT NULL,  `short_desc` varchar(32) COLLATE utf8_bin NOT NULL,  `price` float NOT NULL,  `discount` float NOT NULL,  `attributes` varchar(1024) COLLATE utf8_bin NOT NULL,  `options` varchar(500) COLLATE utf8_bin NOT NULL,  `available_qty` int(11) NOT NULL,  `sold_qty` int(11) NOT NULL,  `compare` tinyint(1) DEFAULT '1',  `enabled` tinyint(1) NOT NULL DEFAULT '1') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=21 ;");
	}

	public static function uninstall() {
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ty_bonus`;");
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ty_buyer`;");
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ty_event`;");
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ty_coupon`;");
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ty_ticket`;");
	}	

	public function __construct() {
		$this->getPaypalCreds();		
		register_activation_hook(__FILE__, array('Tickety', 'install'));
		register_uninstall_hook(__FILE__, array('Tickety', 'uninstall'));
		add_shortcode('tickety', array($this, 'displayTicketing'));
		add_action('admin_menu', array($this, 'add_admin_menu'));
		if(DEV) {
			$this->noticeAdd("Developper mode is enabled");
		}
	}

	public function add_admin_menu() {
	    add_menu_page('Tickety Dashboard', 'Tickety', 'manage_options', 'tickety', array($this, 'main_page'));
		add_submenu_page('tickety', 'Tickety Settings', 'Settings', 'manage_options', 'tickety_settings', array($this, 'settings_page'));	    
	}

	public function main_page() {
		if(empty($this->paypal_settings)) {
			$this->noticeAdd('Paypal settings are empty, please <a href="?page=tickety_settings">set up</a> your API credentials.');
		}
		include(TY_PATH."admin/index.php");
	}

	public function settings_page() {
		include(TY_PATH."admin/settings.php");
	}	

	/*
	*
	* PAYPAL RELATED FUNCTIONS
	*
	*/

	public function PaypalInit() {
		include("paypal.php");
	}

	public function getPaypalCreds() {
		global $wpdb;
		$res = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'ty_settings', ARRAY_A);
		if($res) {
			if((bool)$res['use_sandbox']) {
				$this->paypal_settings = Array(
					'usr' => $res['paypal_devusr'],
					'pwd' => $res['paypal_devpwd'],
					'sign' => $res['paypal_devsign'],
					'sandbox' => true
				);
			} else {
				$this->paypal_settings = Array(
					'usr' => $res['paypal_usr'],
					'pwd' => $res['paypal_pwd'],
					'sign' => $res['paypal_sign'],
					'sandbox' => false
				);
			}
		}
	}

	//Set new Paypal Credentials
	public function setPaypalCreds($usr, $pwd, $sign, $sandbox) {
		global $wpdb;
		$this->paypal_settings = Array(
			'usr' => $usr,
			'pwd' => $pwd,
			'sign' => $sign,
			'sandbox' => $sandbox
		);		
		if($sandbox) { //If sandbox credentials must be used
			$res = $wpdb->query('UPDATE `'.$wpdb->prefix.'ty_settings` SET `paypal_devusr`="'.$usr.'",`paypal_devpwd`="'.$pwd.'",`paypal_devsign`="'.$sign.'",`use_sandbox`="1" WHERE 1');			
		} else {
			$res = $wpdb->query('UPDATE `'.$wpdb->prefix.'ty_settings` SET `paypal_usr`="'.$usr.'",`paypal_pwd`="'.$pwd.'",`paypal_sign`="'.$sign.'",`use_sandbox`="0" WHERE 1');						
		}
		return $res;
	}



	/* Returns all events*/
	public function getEvents() {
		global $wpdb;
		$res = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'ty_event');
		return $res;
	}



	/* Returns a specific event's details */
	public function getEventDetails($event_id) {
		global $wpdb;
		$res = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'ty_event WHERE id = '.$event_id);
		return $res[0];			
	}



	public function deleteEvent($event_id) {
		global $wpdb;
		$this->noticeAdd("<strong>Event deleted : </strong>".$this->getEventDetails($event_id)->name);
		return $wpdb->query('DELETE FROM `'.$wpdb->prefix.'ty_event` WHERE id ="'.$event_id.'"');
	}


	public function deleteTicket($ticket_id) {
		global $wpdb;
		$this->noticeAdd("<strong>Ticket deleted : </strong>".$this->getTicketDetails($ticket_id)->name);
		return $wpdb->query('DELETE FROM `'.$wpdb->prefix.'ty_ticket` WHERE id ="'.$ticket_id.'"');
	}	



	public function toggleEvent($event_id, $state) {
		global $wpdb;
		if($state) {
			$this->noticeAdd("<strong>Event enabled : </strong>".$this->getEventDetails($event_id)->name);
			$wpdb->query('UPDATE `'.$wpdb->prefix.'ty_ticket` SET `enabled`=1 WHERE event_id="'.$event_id.'"');
			return $wpdb->query('UPDATE `'.$wpdb->prefix.'ty_event` SET `enabled`=1 WHERE id="'.$event_id.'"');			
		} else {
			$this->noticeAdd("<strong>Event disabled : </strong>".$this->getEventDetails($event_id)->name);
			$wpdb->query('UPDATE `'.$wpdb->prefix.'ty_ticket` SET `enabled`=0 WHERE event_id="'.$event_id.'"');
			return $wpdb->query('UPDATE `'.$wpdb->prefix.'ty_event` SET `enabled`=0 WHERE id="'.$event_id.'"');			
		}
	}


	public function toggleTicket($ticket_id, $state) {
		global $wpdb;
		if($state) {
			$this->noticeAdd("<strong>Ticket enabled : </strong>".$this->getTicketDetails($ticket_id)->name);
			return $wpdb->query('UPDATE `'.$wpdb->prefix.'ty_ticket` SET `enabled`=1 WHERE id="'.$ticket_id.'"');
		} else {
			$this->noticeAdd("<strong>Ticket disabled : </strong>".$this->getTicketDetails($ticket_id)->name);
			return $wpdb->query('UPDATE `'.$wpdb->prefix.'ty_ticket` SET `enabled`=0 WHERE id="'.$ticket_id.'"');
		}
	}		


	public function getTicketsByEvent($event_id) {
		global $wpdb;
		$res = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'ty_ticket WHERE event_id = '.$event_id);
		return $res;
	}


	public function getEnabledTickets() {
		global $wpdb;
		$res = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'ty_ticket WHERE enabled = 1 AND available_qty > 0');
		return $res;
	}


	public function getEnabledEvents() {
		global $wpdb;
		$res = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'ty_event WHERE enabled = 1');
		return $res;
	}

	public function getCouponDetails($coupon_token) {
		global $wpdb;
		$res = $wpdb->get_results('SELECT bonus, name, discount FROM '.$wpdb->prefix.'ty_coupon as C, '.$wpdb->prefix.'ty_bonus as B WHERE token = "'.$coupon_token.'" AND B.id = C.bonus AND used = 0');
		return $res;
	}

	public function useCoupon($coupon_token) {
		global $wpdb;
		$res = $wpdb->get_results('UPDATE '.$wpdb->prefix.'ty_coupon SET used = 1 WHERE token = "'.$coupon_token.'"');
		return $res;
	}

	public function getTicketDetails($ticket_id) {
		global $wpdb;
		$res = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'ty_ticket WHERE id = '.$ticket_id);
		return $res[0];
	}


	public function createEvent($name, $description, $date_start, $date_end) {
		global $wpdb;
		$this->noticeAdd("<strong>Evenement ajouté : </strong>".$name);		
		return $wpdb->query('INSERT INTO `'.$wpdb->prefix.'ty_event`(`name`, `description`, `date_start`, `date_end`) VALUES ("'.$name.'","'.$description.'", "'.$date_start.'", "'.$date_end.'")');
	}


	public function createTicket($event_id, $name, $description, $short_desc, $price, $discount, $attributes, $options, $available) {
		global $wpdb;
		$state = $this->getEventDetails($event_id)->enabled;
		return $wpdb->query('INSERT INTO `'.$wpdb->prefix.'ty_ticket`(`event_id`, `name`, `description`, `short_desc`, `price`, `discount`, `attributes`, `options`, `available_qty`, `enabled`) VALUES("'.$event_id.'", "'.$name.'", "'.$description.'", "'.$short_desc.'", "'.$price.'", "'.$discount.'", "'.$attributes.'", "'.$options.'", "'.$available.'", "'.$state.'")');
	}

	public function editTicket($id, $event_id, $name, $description, $short_desc, $price, $discount, $attributes, $options, $available) {
		global $wpdb;
		$wpdb->show_errors();
		$state = $this->getEventDetails($event_id)->enabled;
		$res = $wpdb->query('UPDATE `'.$wpdb->prefix.'ty_ticket` SET `event_id` = "'.$event_id.'", `name` = "'.$name.'", `description` = "'.$description.'", `short_desc` = "'.$short_desc.'", `price` = "'.$price.'", `discount` = "'.$discount.'", `attributes` = "'.$attributes.'", `options` = "'.$options.'", `available_qty` = "'.$available.'", `enabled` = "'.$state.'" WHERE id = '.$id);
		$wpdb->print_error();
		return $res;
	}	

	public function addBuyer($fname, $lname, $adress, $city, $p_code, $mail, $ticket_id, $ticket_coupon, $ticket_options, $token, $total) {
		global $wpdb;
		$wpdb->query('INSERT INTO `'.$wpdb->prefix.'ty_buyer`(`fname`, `lname`, `adress`, `city`, `p_code`, `mail`, `ticket_id`, `ticket_coupon`, `ticket_options`, `ticket_token`, `total`) VALUES ("'.$fname.'","'.$lname.'","'.$adress.'","'.$city.'","'.$p_code.'","'.$mail.'","'.$ticket_id.'","'.$ticket_coupon.'","'.$ticket_options.'", "'.$token.'", "'.$total.'")');
		$this->ticketSold($ticket_id);
	}


	public function ticketSold($ticket_id) {
		global $wpdb;
		$wpdb->query('UPDATE `wp_ty_ticket` SET `available_qty`=`available_qty`-1, `sold_qty`=`sold_qty`+1 WHERE `id`='.$ticket_id);
	}

	public function getBuyers($event_id) {
		global $wpdb;
		$res = $wpdb->get_results('SELECT buyer.id, fname, lname, adress, city, p_code, mail, ticket_id, ticket_coupon, ticket_token, total FROM '.$wpdb->prefix.'ty_buyer as buyer, '.$wpdb->prefix.'ty_ticket as ticket WHERE ticket.id = buyer.ticket_id AND ticket.event_id = '.$event_id);
		return $res;
	}	



	public function displayTicketing($atts, $content) {
		$atts = $atts;
		$content = $content;
		include('shortcode/index.php');
	}



	public function noticeAdd($string) {
		$this->notice .= "<p>".$string."</p>";
	}



	public function showNotice() {
		echo '<div class="notice">'.$this->notice.'</div>';
	}

}

new Tickety();