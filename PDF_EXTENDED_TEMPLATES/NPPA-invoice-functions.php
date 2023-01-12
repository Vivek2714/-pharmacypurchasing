<?php

function row($params, $breakTag = true, $extraAttr = [] ){

  $tableCSS   = !empty($extraAttr['tableCSS'])   ? $extraAttr['tableCSS']   : "";
  $trCSS      = !empty($extraAttr['trCSS'])      ? $extraAttr['trCSS']      : "";
  $tableClass = !empty($extraAttr['tableClass']) ? $extraAttr['tableClass'] : "";

  if(gettype($breakTag) === 'string'){
    $tableClass = "{$tableClass} {$breakTag}";
    $breakTag = false;
  }

  ?>
  <table class="<?php echo $tableClass; ?>" style="<?php echo $tableCSS; ?>">
    <tr style="<?php echo $trCSS; ?>">
      <?php foreach($params as $param){ 

        $suffix   = !empty($param['suffix'])  ? $param['suffix']  : "";
        $value    = !empty($param['value'])   ? $param['value']   : "";        
        $prefix   = !empty($param['prefix'])  ? $param['prefix']  : "";
        $style    = !empty($param['style'])   ? $param['style']   : "";
        $type     = !empty($param['type'])    ? $param['type']    : "";
        $align    = !empty($param['align'])   ? $param['align']   : "";
        $nbsp     = !empty($param['nbsp'])    ? "&nbsp;"          : "";
        $width    = !empty($param['width'])   ? $param['width']   : "";
        $wrapTag  = !empty($param['wrapTag']) ? $param['wrapTag'] : "";
        $class    = !empty($param['class'])   ? $param['class']   : "";
        $colspan  = !empty($param['colspan']) ? "colspan='".$param['colspan']."'" : "";
        $rowspan  = !empty($param['rowspan']) ? "rowspan='".$param['rowspan']."'" : "";
        ?>
        <td 
          align="<?php echo $align; ?>" 
          style="<?php echo $style; ?>" 
          width="<?php echo $width; ?>" 
          class="<?php echo $class; ?>"
          <?php echo $colspan; ?>
          <?php echo $rowspan; ?>
        >
          <?php echo $wrapTag ? "<{$wrapTag}>" : ""; ?>
          <?php echo $prefix . $value . $suffix . $nbsp; ?>
          <?php echo $wrapTag ? "</{$wrapTag}>" : ""; ?>
          <?php 
            //  if(gettype($value) === 'boolean'){
            //   $type = 'radio';
            // }
            
            // switch($type){
            //   case 'radio':
            //     $value = getCheckBoxValue($value);
            //     break;
            //   case 'check':
            //     $value = getCheckBoxValue($value);
            //     break;                
            //   default:
            // }
          ?>
        </td>
      <?php } ?>
    </tr>
  </table>
  
  <?php echo $breakTag ? '<br/>' : ''; ?>
  <?php
}


function getCheckBoxValue($value){
  return $value ? "&#9745;" : "&#x25fb;";
}

$upload = wp_upload_dir();

$stepOnePurchaseOrder       =     rgar( $entry, '73' );
$stepOneFirstName           =     rgar( $entry, '1.3' );
$stepOneLastName            =     rgar( $entry, '1.6' );
$stepOneCompany             =     rgar( $entry, '22' );


$stepOneStreetAddress       =     rgar( $entry, '4.1' );
$stepOneAddressLineTwo      =     rgar( $entry, '4.2' );
$stepOneCity                =     rgar( $entry, '4.3' );
$stepOneState               =     rgar( $entry, '4.4' );
$stepOneZipCode             =     rgar( $entry, '4.5' );

$stepOneRegistraionFee      =     rgar( $entry, '78' );
$stepOneRegistraionPayemnts =     empty( rgar( $entry, 'payment_status' ) ) ? 'Not paid' : rgar( $entry, 'payment_status' );
$stepOneRegistraionTotal    =     rgar( $entry, '76' );