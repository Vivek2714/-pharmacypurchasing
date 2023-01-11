<style>
input#fname {
    max-width: 126px;
    width: 100%;
    padding: 8px;
    margin: 0px 4px;
}
</style>
<script>
jQuery( 'document' ).ready(function(){
	jQuery( 'input[name="change_payment_status"]' ).click(function(	){
		if( jQuery( 'input[name="manual_payment"]' ).val() == "" ){
			alert("Please enter amount");
			return false;
		}
		if (confirm( jQuery(this).attr('data-alert') ) == true) {
			jQuery( '#update-payment-status' ).submit();
		}else{
			return false;
		}

	});
});
</script>
<form action="" method="post" id="update-payment-status">
	<?php 
	$entry_id = $_GET['lid'];
	$entry = GFAPI::get_entry($entry_id);
	$outputField = "";
	if( $entry['payment_status'] != 'Paid' ){
		$output = '';
		$buttonText = 'Mark as paid';
		$alerttext = 'Are you sure to mark this entry as paid?';
		$outputField = '<input type="text" id="fname" name="manual_payment" placeholder="0.00" value="" >'  ;
	}else{
		$output = 'Entry has been marked as paid by '.gform_get_meta( $entry_id, 'updator_name', true ).'<br>';
		$buttonText = 'Mark as unpaid';
		$alerttext = 'Are you sure to mark this entry as unpaid?';
	}
	$output .= '<input type="submit" name="change_payment_status" data-alert="'.$alerttext.'" value="'.$buttonText.'" class="button" style="">';
	echo $outputField.$output;
	?>
</form>