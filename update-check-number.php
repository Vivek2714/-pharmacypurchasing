<style>
	.popover{
		position:fixed;
		left:0;
		top:0;
		z-index: 9999999;
		width:100%;
		height:100%;
		display:none;
	}
	#overlay{
		position:absolute;
		width:100%;
		height:100%;
		left:0;
		top:0;
		background:#000;
		z-index: -1;
		opacity:0.6;
	}
	#popup-body{
		background:#FFF;
		z-index: 9999999;
		max-width:500px;
		width:100%;
		margin:0 auto;
		margin-top:-20%;
	}
	#popup-body .row{
		background:#FFF;	
		float:left;
		width:100%;
		padding:0 30px;
		position: relative;
	}
	#popup-body h2{
		text-align:center;
		padding:10px 0px 0px 0px !important;
		font-size: 18px !important;
    line-height: 53px !important;	
	}
	#popup-body .row input{
		float:left;
		width:100%;
		padding:15px;
		margin-bottom:10px;
		border:1px solid #c7c7c7;
	}
	#popup-body .row.last{
		padding-bottom:30px;
	}
	#popup-body .row.last input{
		background:#0693e3;
		color:#fff;
		padding:10px;
		cursor:pointer;
	}
	#popup-body .row.last input:hover{
		background:grey;
	}
	#popup-body .close{
    position: absolute;
    right: -12px;
    background: #0693e3;
    padding: 7px 12px;
    color: #fff;
    border-radius: 100%;
    font-weight: bold;
    top: -12px;
		cursor:pointer;
	}
</style>

<script>
	jQuery( 'document' ).ready(function(){
		jQuery( '#change-check-number-form' ).click(function(){
			jQuery( '.popover' ).show();
			jQuery("#popup-body").animate({
					'margin-top': '20%'
			}, 800);
		});
		jQuery( '.popover .close' ).click(function(){
			jQuery("#popup-body").animate({
				'margin-top': '-20%'
			}, 800);
			setTimeout(() => {
				jQuery( '.popover' ).hide();
			}, 800);
		});
	});
</script>

<div class="popover">
	<div id="overlay"></div>
	<div id="popup-body">
		<form action="" method="post">
			<div class="row">
				<div class="close">X</div>
				<h2>Update check number</h2>
			</div>
			<div class="row">
				<input class="field" name = 'check-no' placeholder="Enter check number" type="number" required>
			</div>
			<div class="row last">
			<input type="submit" name="update-check-number" value="Update" class="button">
			</div>
		</form>
	</div>
</div>
<input type="button" id="change-check-number-form" value="Update" class="button">