<?php
/*
Plugin Name: Pharmacy Purchasing Customization
Plugin URI: https://idevelopingsolutions.com/
Description: Pharmacy purchasing per year event manager with Gravity forms
Author: Vivek
Version: 1.0
Author URI: https://idevelopingsolutions.com/
*/

error_reporting(0);
class pharmacyPurchasingCustomization{

	public $debugging = false;
	public $formClass = "nppa-register-form";
	public $registerationYear = '';
	public function __construct(){

		if( $this->debugging === true ){
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1');
			error_reporting(E_ALL);
		}

		## 
		$this->registerationYear = date("Y");

		## Register shortcode for event render
		add_shortcode( 'show-events', [ $this, 'renderEventHTML' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'pharmacyPurchasingScripts' ] );
		
		## Prefil user inputs for logged in user
		add_filter( 'gform_pre_render', [ $this, 'populateUserFields' ] );
	
		## Access user info if already registered
		add_filter( 'gform_pre_render', [ $this, 'populateFieldAccess' ], 999, 1 );

		## Check for email address 
		add_filter( 'gform_validation', [ $this, 'checkUserEntry' ] );

		## Update existing entry
		add_filter( 'gform_entry_id_pre_save_lead', [ $this, 'updateUserEntry' ], 99, 2 );

		add_shortcode( 'form-preview', [$this, 'renderFormPreview' ] );

		## Overriding the default validation of the email field
		add_filter("gform_user_registration_validation", [$this, "ignore_already_registered_error"], 10, 3);

		## Assigning specified coutries to the counties field in the form
		add_filter( 'gform_countries', [$this, 'view_the_specified_countries'] );

		## Update CONF number after form submission
		add_action( 'gform_after_submission', [ $this, 'updateConfNumber' ], 10, 2 );
		
		## Add payment status change button to entry view page
		add_filter('gform_entry_detail_meta_boxes', [ $this, 'change_payment_status_box' ], 99, 3);

    ## Add update check number button 
		add_filter('gform_entry_detail_meta_boxes', [ $this, 'Update_check_number' ], 99, 3);

		# Add button for the refund
		add_filter('gform_entry_detail_meta_boxes', [ $this, 'Refund_payment' ], 99, 3);

		## Before page load
		add_action( 'init', [ $this, 'initialization' ] );

	}

	## Process functions before page load
	public function initialization(){
		
		## Get entry
		$entry_id = $_GET['lid'];

		## Get entry details
		$entry = GFAPI::get_entry($entry_id);

		## Validate entry
		if( is_wp_error($entry) ){
			return;
		}

		## Update check number
		if( isset($_POST['update-check-number'] ) ){
			$updatecheq = $_POST['check-no'];
			GFAPI::update_entry_field( $entry_id, '165' , $updatecheq );
		}

		## Refund the payement
		if(isset($_POST['refund-payment'])){
			$entry['168'] = '-'.$_POST['refund-amount'];
		}

		## Update payment status and amount
		if( isset($_POST['change_payment_status']) ){
			$user = wp_get_current_user();
			$entry['payment_status'] = 'Paid';
			$entry['payment_amount'] = $_POST['manual_payment'];
			if( $_POST['change_payment_status'] == 'Mark as unpaid' ){
				$entry['payment_status'] = '';
				$entry['payment_amount'] = '';
			}
			$entry['payment_method'] = 'Manually';
			$entry['transaction_id'] = '';
			$entry['payment_date'] = date("Y-m-d h:i:s a");

			## Update name of editor
			gform_update_meta( $entry_id, 'updator_name', $user->data->user_login );
		}
			## Update entry
			GFAPI::update_entry($entry);

	}

	## Remove already registered users
	public function ignore_already_registered_error($form, $config, $pagenum){

		## Make sure we only run this code on the specified form ID
		$formClasses = explode( " ", $form['cssClass'] );
		if( !in_array( $this->formClass, $formClasses ) ){
			return $form;
		}	

		## Get the ID of the email field from the User Registration config
		$email_id = $config['meta']['email'];
		
		$email = !empty( $_POST['input_'.$config['meta']['email']] ) ? $_POST['input_'.$config['meta']['email']] : null;
		if( $email == null ){
			return $form;
		}
		
		if( false !==  $this->getUserEntry( $this->registerationYear, $email, $form ) ){
			return $form;
		}

		// Loop through the current form fields
		foreach($form['fields'] as &$field) {
			if($field->id == $email_id ){
				$field->failed_validation = false;
			}
		}

		return $form;
	}

	## Auto prefil form fields
	public function populateUserFields($form){
		if( !is_user_logged_in() ){
			return $form;
		}
		$formClasses = explode( " ", $form['cssClass'] );
		if( !in_array( $this->formClass, $formClasses ) ){
			return $form;
		}
    	foreach( $form['fields'] as &$field ) {
			$cssClasses = explode( " ", $field->cssClass );
			if( in_array( 'user-email-input', $cssClasses ) ){
				$user = wp_get_current_user();
				if( !empty($field->inputs) ){
					$tempInputs = [];
					foreach( $field->inputs as $key => $inputs ){
						$tempInputs[$key] = $inputs;
						$tempInputs[$key]['defaultValue'] = $user->user_email;
					}
					$field->inputs = $tempInputs;
					continue;
				}
				$field->defaultValue = $user->user_email;
			}
		}
		return $form;
	}

	## Add enqueued scripts
	public function pharmacyPurchasingScripts() {
		wp_enqueue_script('jquery');
		wp_register_script( 'pharmacypurchasing_script', plugin_dir_url(__FILE__)."assets/js/custom.js?".time() );
		$localize = array(
			'ajax_url' => admin_url('admin-ajax.php')
		);
		wp_localize_script(  'pharmacypurchasing_script', 'admin_script', $localize );
		wp_enqueue_script( 'pharmacypurchasing_script' );
		wp_enqueue_style( 'pharmacypurchasing_style', plugin_dir_url(__FILE__)."css/style.css?".time() );
	}
	
	## Callback function for shortcode i.e [show-event]
	public function renderEventHTML($attrs){
		ob_start();
		$registerPageURL = isset($attrs['register-page-url']) ? $attrs['register-page-url'] : "";
		$buttonText      = isset($attrs['text']) ? $attrs['text'] : "APPLY NOW";
		include_once 'frontend/event-html.php';
		$output = ob_get_contents();
		ob_get_clean();
		return $output;
	}

	## Get user entry for event
	public function getUserEntry( $eventId ="", $email ="", $form =[] ){

		if(
			empty($eventId) ||
			empty($email) ||
			empty($form)
		){
			return false;
		}

		$eventIdFieldID = $emailIdFieldID = 0;
		foreach( $form['fields'] as &$field ) {
			$cssClasses = explode( " ", $field->cssClass );
			if( in_array( 'user-email-input', $cssClasses ) ){
				$emailIdFieldID = $field->id;
			}
			if( in_array( 'year-of-registration', $cssClasses ) ){
				$eventIdFieldID = $field->id;
			}
		}

		$search = [
			'status'        => 'active',
			'field_filters' => [
				'mode' => 'all',
				[
					'key'   => $eventIdFieldID,
					'value' => $eventId
				],
				[
					'key'   => $emailIdFieldID,
					'value' => $email
				]
			]
		];

		$paging = [ 'offset' => 0, 'page_size' => 1 ];
		$entries = GFAPI::get_entries( $form['id'], $search );
		if( empty($entries) ){
			return false;
		}

		return $entries[0];
	}

	## Prepopulate already registered user information
	public function populateFieldAccess($form){

		$formClasses = explode( " ", $form['cssClass'] );
		if( !in_array( $this->formClass, $formClasses ) ){
			return $form;
		}	

		if( $_POST['gform_target_page_number_'.$form['id']] == 1 ){
			$_POST = [];
		}

 		foreach( $form['fields'] as &$field ) {
			$cssClasses = explode( " ", $field->cssClass );
			if( in_array( 'signed-email', $cssClasses ) ){
				$email = $_POST['input_'.$field->id];
				if( empty( $email ) ){
					break;
				}

				## Validate user entry object
				$entry = $this->getUserEntry( $this->registerationYear, $email, $form );
				if( is_wp_error($entry) ){
					break;
				}
			}

			// $items = array();
			switch( $field->type ) {

				case 'name':
					$items = array();
					foreach( $field->inputs as $input ){
						$items[] = array_merge( $input, array( 'defaultValue' => rgar( $entry, $input['id'] ) ) );
					}
					$field->inputs = $items;
				break;

				case 'email':
					$items = array();
					foreach( $field->inputs as $input ){
						$items[] = array_merge( $input, array( 'defaultValue' => rgar( $entry, $field->inputs[0]['id'] ) ) );
					}
					$field->inputs = $items;
				break;

				case 'address':
					$items = array();
					foreach( $field->inputs as $input ){
						$items[] = array_merge( $input, array( 'defaultValue' => rgar( $entry, $input['id'] ) ) );
					}
					$field->inputs = $items;
				break;

				case 'checkbox':
					$items = array();
					$i = 1;
					foreach( $field->choices as $choice ){
						//skipping index that are multiples of 10 (multiples of 10 create problems as the input IDs)
						if ( $i % 10 == 0 ) {
								$i++;
						}							
						$selected = false;
						if( !empty(rgar( $entry, $field->id.'.'.$i ) ) ){
							if( !isset( $_POST['input_'.$field->id.'_'.$i] ) ){
								$_POST['input_'.$field->id.'_'.$i] = $choice['value'];
							}
							$selected = true;
						}  
						$items[] = array( 'value' => $choice['value'], 'text' => $choice['text'], 'isSelected' =>  $selected );
						$i++;
					}
					$field->choices = $items;
				break;

				default:
					# code...
					$field->defaultValue = rgar( $entry, $field->id );
				break;

			}
	
		}

		return $form;
	}

	## Check exisitng user entry for event
	public function checkUserEntry( $validation_result ) {
    	$form = $validation_result['form'];
		foreach( $form['fields'] as &$field ) {

			$cssClasses = explode( " ", $field->cssClass );
			if( in_array( 'signed-email', $cssClasses ) ){
				$email = $_POST['input_'.$field->id];
				if( empty( $email ) ){
					break;
				}

				## Validate user entry object
				$entry = $this->getUserEntry( $this->registerationYear, $email, $form );
				if( is_wp_error($entry) ){
					break;
				}

				if( empty( $entry ) ){
					$validation_result['is_valid'] = false;
					$field->failed_validation = true;
					$field->validation_message = 'Entered email does not have any registration.';
					break;
				}
			}
		}
    	//Assign modified $form object back to the validation result
   		 $validation_result['form'] = $form;
    	return $validation_result;
	}

	## Update entry instead creating new one
	public function updateUserEntry( $entryId, $form ) {
	
		$formClasses = explode( " ", $form['cssClass'] );
		if( !in_array( $this->formClass, $formClasses ) ){
			return $entryId;
		}	

		foreach( $form['fields'] as &$field ) {
			$cssClasses = explode( " ", $field->cssClass );
			if( in_array( 'signed-email', $cssClasses ) ){
				$email = $_POST['input_'.$field->id];
				if( empty( $email ) ){
					break;
				}

				## Validate user entry object
				$entry = $this->getUserEntry( $this->registerationYear, $email, $form );
				if( is_wp_error($entry) ){
					break;
				}
				$entryId = $entry['id'];
			}
		}

		return $entryId;
	}

	## Form preview
	public function renderFormPreview( $attr ){
		ob_start();
		include_once('form-preview.php');
		$output = ob_get_contents();
		ob_get_clean();
		return $output;
	}

	## Update conf number
	public function updateConfNumber( $entry, $form ){

		$formClasses = explode( " ", $form['cssClass'] );
		if( !in_array( $this->formClass, $formClasses ) ){
			return;
		}	

		foreach( $form['fields'] as &$field ) {
			$cssClasses = explode( " ", $field->cssClass );
			if( in_array( 'conf-number', $cssClasses ) ){
				## Update tiral status field
				GFAPI::update_entry_field( $entry['id'], $field->id, date( "Y", strtotime($entry['date_created'] ) )."-".substr( strtotime($entry['date_created'] ), -5 ) );
			}

			if( in_array( 'year-of-registration', $cssClasses ) ){
				GFAPI::update_entry_field( $entry['id'], $field->id, $this->registerationYear );
			}
		}

	}

	## Show few countris
	public function view_the_specified_countries( $countries ) {
		$specified_countries = [
			'United States' => 'United States',
			'Puerto Rico'   => 'Puerto Rico',
			'Guam'          => 'Guam',
			'Canada'        => 'Canada'
		];
		return $specified_countries;
	}

	/**
 	* Allow custom meta boxes to be added to the entry detail page.
 	*
 	* @param array $meta_boxes The properties for the meta boxes.
 	* @param array $entry      The entry currently being viewed/edited.
 	* @param array $form       The form object used to process the current entry.
 	*/
	public function change_payment_status_box( $meta_boxes, $entry, $form ){
		$meta_boxes['change_payment_button'] = array(
		'title'    => esc_html__( 'Update payment status', 'gravityforms' ),
		'callback' => [ $this, 'change_payment_button_html' ],
		'context'  => 'side',		
		);
		return $meta_boxes;
	}

	## Adding button to sidebar
	public function change_payment_button_html( $args ){
		include_once('change-payment-status-button.php');
	}

  ## Adding button of update check number to sidebar
	public function Update_check_number( $meta_boxes, $entry, $form ){
		$meta_boxes['update_number'] = array(
		'title'    => esc_html__( 'Update check number', 'gravityforms' ),
		'callback' => [ $this, 'update_number_html' ],
		'context'  => 'side',		
		);
		return $meta_boxes;
	}

	public function update_number_html( $args ){ 
    include_once('update-check-number.php');
  }

	## Adding a box for refund the payment 
	public function Refund_payment( $meta_boxes, $entry, $form ){
		$meta_boxes['repay'] = array(
		'title'    => esc_html__( 'Refund', 'gravityforms' ),
		'callback' => [ $this, 'refund_payment_html' ],
		'context'  => 'side',		
		);
		return $meta_boxes;
	}

	## Render refund payment
	public function refund_payment_html($args){
		include_once('refund-payment.php');
	}

}

add_action( 'plugins_loaded', function(){
    new pharmacyPurchasingCustomization();
});