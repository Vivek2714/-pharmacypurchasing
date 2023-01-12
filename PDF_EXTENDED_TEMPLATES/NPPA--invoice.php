<?php
/**
 * Template Name: NPPA invoice
 * Version: 0.1
 * Description: NPPA invoice
 * Author: Vivek
 * Author URI: https://gravitypdf.com
 * Group: Pharmacypurchasing PDF
 * License: GPLv2
 * Required PDF Version: 4.0
 * Tags: space
 */

/* Prevent direct access to the template (always good to include this) */
if ( ! class_exists( 'GFForms' ) ) {
    return;
}
include_once dirname(__FILE__) ."/NPPA-invoice-functions.php";
  
/**
 * All Gravity PDF v4/v5/v6 templates have access to the following variables:
 *
 * @var array  $form      The current Gravity Form array
 * @var array  $entry     The raw entry data
 * @var array  $form_data The processed entry data stored in an array
 * @var object $settings  The current PDF configuration
 * @var array  $fields    An array of Gravity Form fields which can be accessed with their ID number
 * @var array  $config    The initialised template config class – eg. /config/zadani.php
 */

## Get checkbox values 
function get_choices( $fieldId, $entry  ){
  ## Get form ID
  $formID = $entry['form_id'];
  ## Get field object
  $field = GFAPI::get_field( $formID, $fieldId );
  $selected = [];
  foreach( $field->inputs as $input ){
    if( !empty($entry[$input['id']]) ){
      $selected[] = $entry[$input['id']];
    }
  }
  return $selected;
}

$logoImagePath = dirname(__FILE__) . '/images/';
$stepOneRegistraionFee = explode( " - ", $stepOneRegistraionFee);
$addonFee = explode( " - ", $entry['79']);
$registrationFeeLabel = explode( "|", $stepOneRegistraionFee[1] );
$addonFeeLabellabel = explode( "|", $addonFee[1] );
$refund = empty( $entry['176'] ) ? 0 : $entry['176'];
$rFee = isset( $registrationFeeLabel[1] ) ? $registrationFeeLabel[1] : 0;
$aFee = isset( $addonFeeLabellabel[1] ) ? $addonFeeLabellabel[1] : 0;
$balance = !empty($refund) ? ($rFee + $aFee ): $entry['76']; 
$total = '$'.number_format( (float)( ( $balance + $refund ) ), 2, '.', '');
// echo "<pre>";
//   print_r( $registrationFeeLabel[1] );
//   print_r( $addonFeeLabellabel[1] );
//   print_r($entry);
// echo "<pre>";
// die;

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
  
	.full-width{
    width:100%;
  }

  table td{
    font-size: 13px;
    font-family:sans-serif;
  }
  
	table.medium-font-size td{
    font-size: 13px;
  }

	table.double-medium-font-size td{
    font-size: 14px;
  }

	table.small-font-size td{
    font-size: 11px;
  }

	table.normal-size-font td{
    font-size: 12px;
  }

	table.extra-small-font-size td{
    font-size: 10px;
  }

	table.xxs-small-font-size td{
    font-size: 8px;
  }

	.table-bordered{
		border:1px solid #000;
	}

	.small-gap-top{
		margin-top: 2px;
	}

	.gap-bottom-medium{
    margin-bottom: 20px;
  }

	.gap-bottom{
		margin-bottom:35px;
	}

	.medium-extra-gap-bottom{
		margin-bottom:60px;
	}

	.extra-gap-bottom{
		margin-bottom:160px;
	}

	.extra-more-gap-bottom{
		margin-bottom:300px;
	}

	.small-gap{
		margin-bottom: 8px;
	}
	
	.medium-small-gap{
		margin-bottom: 4px;
	}

	.extra-small-gap{
		margin-bottom: 3px;
	}

  .top-bottom-padding{
    padding: 4px 0px;
	}

  .bg-color{
    background-color:#fcfcfc;
	}

	.double-height tr td{
    height: 38px;
    vertical-align: bottom;
  }
	
	.table-bordered.remove-top-border{
		border-top-color:#fff !important;
	}
	.table-bordered.remove-bottom-border{
		border-bottom-color:#fff !important;
	}
	.table-bordered.remove-right-border{
		border-right-color:#fff !important;
	}
	.side-bordered{
		border-left:1px solid #c7c7c7 !important;
		border-right:1px solid #c7c7c7 !important;
	}
</style>
</head>
<body>
  <table border="0">
    <tr>
      <td>
        <img src="<?php echo $logoImagePath . 'invoice-banner-'.date('Y', strtotime($entry['date_created'])).'.png'; ?>"  >	
      </td>  
    </tr>
  </table>
  <?php
	row([[
		'value'  => "<br>".date( "Y", strtotime($entry['date_created'] ) )." NPPA Conference - Invoice/Receipt",
		'align'  => 'center',
    'style'  => 'font-size:14px',
    'wrapTag'=> 'strong'
	]], 'small-gap small-gap-top');

  ob_start();
    row([[
        'value'  => "Purchase Order #",
        'width'  => '36%'
      ],[
        'value'  => $stepOnePurchaseOrder,
        'style'  => 'border-bottom:1px solid #000',
        'width'  => '44%',
        'align' => 'center'
      ],[
    ]],false);

    row([[
      'value'  => '<br>Make Checks Payable To:',
      'width'  => '19%',
      'wrapTag'=> 'strong',
    ]], false);

    row([[
      'value'  => 'NPPA (or, to: “National Pharmacy Purchasing Association”)',
      'width'  => '80%',
    ],[
    ]], false);

    row([[
      'value'  => '<br>Send Checks To:',
      'wrapTag'=> 'strong',
    ]], 'small-gap-top');

    row([[
      'value'  => 'National Pharmacy Purchasing Association (NPPA)',
    ]], false);

    row([[
      'value'  => '4747 Morena Blvd., Suite 340',
    ]], false);


    row([[
      'value'  => 'San Diego, CA 92117-3468',
    ]]);
  $leftContent = ob_get_clean();

  ob_start();
    // row([[
    //   'value'  => '',
    // ]]);

    row([[
      'value'  => "<b>Payment Deadlines:</b> PO's no longer accepted after July 1. Checks must be received in the NPPA offices in San Diego by July 29, when we leave for Vegas. Credit card payments may be applied to your registration account online by August 6. After Aug. 6, as long as you are already registered, you may bring your facility check payment with you to Vegas (no personal checks onsite). If you are not registered by Aug. 6, you will need to do so onsite, with an additional $20 Onsite Reg Fee due.",
      'style'  => 'font-size:11px'
    ]]);
  $rightContent = ob_get_clean();

    row([[
      'value'  => $leftContent,
      'width'  => '48%',
    ],[
      'value'  => $rightContent
    ]]);
    
  echo "<div style='border:2px solid #c7c7c7;padding: 2px 4px;'>";
    row([[
      'value'  => 'CONF #: '.$entry['117'],
      'wrapTag'=> 'strong'
    ]], 'small-gap');

    row([[
      'value'  => "{$stepOneFirstName}   {$stepOneLastName}",
    ]], false);

    row([[
      'value'  => $stepOneCompany
    ]], false);
    
    row([[
      'value'  => "{$stepOneStreetAddress} {$stepOneAddressLineTwo}",
    ]], false);

    row([[
      'value'  => "{$stepOneCity}, {$stepOneState }  {$stepOneZipCode}",
    ]], 'small-gap');

    row([[
      'value'  => 'Balance: '.$total,
    ]],false);
  echo "</div>";
  
  row([[
    'value'  => '',
    'style'  => 'border:1px solid #c7c7c7;'
  ]],false);

  echo "<div style='border:2px solid #c7c7c7;padding: 2px;'>";
    row([[
      'value'  => 'Qty &nbsp;&nbsp;&nbsp;&nbsp; Item',
      'wrapTag'=> 'strong',
      'prefix' => '&nbsp;'
    ],[
      'value'  => 'Amount',
      'wrapTag'=> 'strong',
      'align'  => 'right'
    ]], 'bg-color');

    row([[
      'value'  => '',
      'style'  => 'border-bottom:1px solid #c7c7c7'
    ]], false);


    row([[
      'value'  => '1 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.$registrationFeeLabel[0],
      'width'  => '88%',
      'style'  => 'padding-left:10px'
    ],[ 
      'value'  => $stepOneRegistraionFee[0],
      'width'  => '12%',
      'wrapTag'=> 'strong',
      'align'  => 'right',
      'style'  => 'padding:5px 0px'
    ],[
    ]], 'bg-color');
    
    if( !empty($entry['79']) ){
      row([[
        'value'  => '1 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.$addonFeeLabellabel[0],
        'width'  => '88%',
        'style'  => 'padding-left:10px'
      ],[ 
        'value'  => $addonFee[0],
        'width'  => '12%',
        'wrapTag'=> 'strong',
        'align'  => 'right',
        'style'  => 'padding:5px 0px'
      ],[
      ]], 'bg-color');
    }

    if( !empty($entry['164']) && empty($refund) ){
      $totalPayment = ( $registrationFeeLabel[1] + $addonFeeLabellabel[1] ) - $entry['76'];
      row([[
        'value'  => '1 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Coupon code : '.$entry['164'],
        'width'  => '88%',
        'style'  => 'padding-left:10px'
      ],[ 
        'value'  => '-$'.number_format( (float)($totalPayment), 2, '.', ''),
        'width'  => '12%',
        'wrapTag'=> 'strong',
        'align'  => 'right',
        'style'  => 'padding:5px 0px'
      ],[
      ]], 'bg-color');
    }

    if( !empty($entry['176']) ){
      row([[
        'value'  => '1 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Refunded amount',
        'width'  => '88%',
        'style'  => 'padding-left:10px'
      ],[ 
        'value'  => '-$'.(number_format((float) abs($refund), 2, '.', '')),
        'width'  => '12%',
        'wrapTag'=> 'strong',
        'align'  => 'right',
        'style'  => 'padding:5px 0px'
      ],[
      ]], 'bg-color');
    }

    row([[
      'value'  => '',
      'style'  => 'border-bottom:2px dotted #c7c7c7'
    ]], false);

    row([[
      'value'  => "<b>Payments:</b> &nbsp;&nbsp; {$stepOneRegistraionPayemnts}",
      'align'  => 'right'
    ]], 'bg-color medium-small-gap');

    row([[
      'value'  => "<b>Total:</b> &nbsp;&nbsp; ".$total." ",
      'align'  => 'right'
    ]],'bg-color');

    row([[
      'style'  => 'border-bottom:1px solid #c7c7c7',
    ]]);

    row([[
      'style'  => 'border-bottom:1px solid #c7c7c7',
    ]], false);

   /* row([[
      'value'  => 'Pay ID',
      'wrapTag'=> 'strong',
      'width'  => '14%',
      'prefix' => '&nbsp;',
      'style'  => 'padding:4px 0px'
    ],[
      'value'  => 'Type',
      'wrapTag'=> 'strong',
      'width'  => '14%',
      'style'  => 'padding:4px 0px'
    ],[
      'value'  => 'Amount',
      'wrapTag'=> 'strong',
      'width'  => '16%',
      'style'  => 'padding:4px 0px'
    ],[
      'value'  => 'Date',
      'wrapTag'=> 'strong',
      'width'  => '13%',
      'style'  => 'padding:4px 0px'
    ],[
      'value'  => 'CC No/Check No.',
      'wrapTag'=> 'strong',
      'style'  => 'padding:4px 0px'
    ]], 'bg-color');

    row([[
      'style'  => 'border-top:1px solid #c7c7c7',
    ]], false);

    row([[
      'width'  => '16%'
    ],[
      'value'  => '<i>There are no payments</i>',
      'wrapTag'=> 'strong',
      'style'  => 'padding:3px 0px'
    ]], 'bg-color');

    row([[
      'style'  => 'border-bottom:1px solid #c7c7c7',
    ]], 'gap-bottom');*/
     
  echo "</div>";
  
	?>

</body>
</html