<?php

defined( 'ABSPATH' ) or die( 'You shall not pass!' );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

function wpcf7_add_json_for_1clicksuite($WPCF7_ContactForm) {
		
	$wpcf = WPCF7_ContactForm::get_current();
	$submission = WPCF7_Submission::get_instance();	
	
	if ($submission) {

		$data = $submission->get_posted_data();
		if (empty($data))
			return;
		
		$object_json = array();  
		isset($data['customer']) ? $object_json["customer"] = $data['customer'] : "";  
		isset($data['email']) ? $object_json["email"] = $data['email'] : "";  
		
		isset($data['phone']) ? $object_json["phone"] = $data['prefisso'][0] . $data['phone'] : "";
		
		isset($data['information']) ? $object_json["information"] = $data['information'] : "";  
		$object_json["sender"] = "sender.***";
		$object_json["newsletter"] = $data['newsletter'][0] == '' ? false : true;
		$object_json["whatsapp"] = $data['whatsapp'][0] == '' ? false : true;

		$object_json["privacy"] = ($data['privacy'] == 1 ? date("Y-m-d H:i:s") : 0);
		$object_json["language"] = "it_IT";

		$object_json['rooms'] = [];
		isset($data['adult']) ? $object_json['rooms'][0]["adult"] = $data['adult'][0] : "";
		$children = array();
		$children_groups = array();
		
		$children_groups[0] = [];
		$children_groups[1] = ['ch_1_1'];
		$children_groups[2] = ['ch_2_1','ch_2_2'];
		$children_groups[3] = ['ch_3_1','ch_3_2','ch_3_3'];

		foreach ($children_groups[$data["child"][0]] as $child) {
			if ($data[$child][0] !== "...") {
				$children[] = $data[$child][0];
			} 
		}
		
		$object_json['rooms'][0]["children"] = $children;
		isset($data['meal_plan']) ? $object_json['rooms'][0]["meal_plan"] = $data['meal_plan'][0] : "";
		isset($data['flex_date']) && $data['flex_date'][0] == "SI" ? $object_json['rooms'][0]["flex_date"] = true : $object_json['rooms'][0]["flex_date"] = false;

		if ( is_plugin_active( 'contact-form-7-datepicker/contact-form-7-datepicker.php' ) ) { /* SE CLIENTE USA CF7 DATEPICKER */
			isset($data['checkin']) ? $object_json['rooms'][0]["checkin"] = date("d/m/Y", strtotime( str_replace('/', '-', $data['checkin'] ))) : "";
			isset($data['checkout']) ? $object_json['rooms'][0]["checkout"] = date("d/m/Y", strtotime( str_replace('/', '-', $data['checkout'] ))) : "";
		} else {
			isset($data['checkin']) ? $object_json['rooms'][0]["checkin"] = date("d/m/Y", strtotime($data['checkin'])) : "";
			isset($data['checkout']) ? $object_json['rooms'][0]["checkout"] = date("d/m/Y", strtotime($data['checkout'])) : "";
		}

		$str = base64_encode(json_encode($object_json));
		$mail = $wpcf->prop('mail');
		$mail['body'] = str_replace('[codice_json]', $str, $mail['body']);
		$wpcf->set_properties(array(
			"mail" => $mail
		));
		
	}
	
	return $wpcf;

}

add_action("wpcf7_before_send_mail", "wpcf7_add_json_for_1clicksuite");  
