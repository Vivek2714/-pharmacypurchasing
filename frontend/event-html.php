<?php

## Form fields
$fields = $this->eventForm['fields'];

## GF filter array 
$search = [ 
	'status'        => 'active',
	'field_filters' => [
		[
			'key'   => $fields['status'],
			'value' => 'active'
		]
	]
];

## Get events
$events = GFAPI::get_entries( $this->eventForm['id'], $search );  
?>
<div style="display:flex">
	<?php
	foreach( $events as $event ){

		## current year
		$currentYear = date('Y');

		## Event year
		$eventYear = date( 'Y', strtotime( rgar( $event, $fields['date'] ) ) );

		## Don't show event for previous / next years
		if( $currentYear != $eventYear ){
			continue;
		}

		$registerPageURL = add_query_arg( '_event_id', $event['id'], $registerPageURL );

		$image = explode( '|', rgar( $event, $fields['image'] ) );
		?>
		<div class="row-pharmacy-purchasing" style="margin:20px">
			<h4 class="event-title">
				<?php echo rgar( $event, $fields['name'] ); ?><br>
				<span style="float: left; font-size: 14px; padding: 10px 0;"> Event Date : <?php echo rgar( $event, $fields['date'] ); ?> </span>
			</h4>
			<div class="event-image">
				<img src="<?php echo $image[0]; ?>">
			</div>
			<span class="event-date">
				<?php //echo rgar( $event, $fields['date'] ); ?>
			</span>
			<span class="event-register">
				<?php 
					if( strtotime( rgar( $event, $fields['date'] ) ) > time() ){
				?>
					<a href="<?php echo $registerPageURL; ?>"><?php echo $buttonText; ?></a>
				<?php }else{
				?>
					<a style="background: #c7c7c7" href="javascript:void(0)">Expired</a>
				<?php
				} ?>
			</span>
		</div>
		<?php
		}
	?>
</div>
<style>
	.wp-block-post-content{
		display:flex;
	}
	.row-pharmacy-purchasing{
		max-width:300px;
		width:100%;
	}
	.row-pharmacy-purchasing .event-title{
		margin:10px auto;
	}
	.row-pharmacy-purchasing .event-image img{
		max-width:300px;
		width:100%;
		height:290px;
	}
	.row-pharmacy-purchasing .event-register a{
		float:left;
		width:100%;
		padding:10px;
		text-align:center;
		text-decoration:none;
		background:#00208c;
		color:#FFF !important;
		box-sizing:border-box;
	}
</style>