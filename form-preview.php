<?php

$formID = isset( $_POST['gform_submit'] ) ? $_POST['gform_submit'] : null;
if( null === $formID ){
    return "";
}

$entry = [];
foreach( $_POST as $key => $value ){
    $entry[ str_replace( "_", ".", str_replace( "input_", "", $key ) ) ] = $value;
}

## Get form object
$form = GFAPI::get_form( $formID );

## Print output
foreach( $form['fields'] as $field ){

    ## Fields to skip
    $skipFields = [ 116, 150, 71, 74, 73, 93 ];
    if( 
        'hidden' == $field->visibility ||
        in_array( $field->id,$skipFields )
    ){
        continue;
    }

    // echo $field->label;
    switch( $field->type ){
        case 'section':
            echo '<div style="float:left;width:100%;font-size: 14px; font-weight: bold; background-color: #EEE; border-bottom: 1px solid #DFDFDF; padding: 7px 7px;
            ">'.$field->label.'</div>'; 
        break;
        case 'hidden':
        break;
        case 'name':
            $value = []; 
            foreach( $field->inputs as $input ){
                if( empty(rgar( $entry, (string) $input['id'] )) ){
                    continue;
                }
                $value[] = rgar( $entry, (string) $input['id'] );
            }
            if( empty($value) ){
                break;
            }
            echo '<div style="font-family: sans-serif; font-size:12px;float:left;width:100%; font-weight: bold; background-color: #EAF2FA; padding: 7px 7px;
            ">'.$field->label.'</div>'; 
            echo '<div style="font-family: sans-serif; font-size:12px;float:left;width:100%; background-color: #FFF; padding: 7px 7px 7px 30px;
            ">'.implode( " ", $value ).'</div>'; 
        break;
        case 'address':
            $value = []; 
            foreach( $field->inputs as $input ){
                if( empty( rgar( $entry, (string) $input['id'] ) ) ){
                    continue;
                }
                $value[] = rgar( $entry, (string) $input['id'] );
            }
            if( empty($value) ){
                break;
            }
            echo '<div style="font-family: sans-serif; font-size:12px;float:left;width:100%; font-weight: bold; background-color: #EAF2FA; padding: 7px 7px;
            ">'.$field->label.'</div>'; 
            echo '<div style="font-family: sans-serif; font-size:12px;float:left;width:100%; background-color: #FFF; padding: 7px 7px 7px 30px;
            ">'.implode( "<br>", $value ).'</div>'; 
        break;
        case 'checkbox':
            $value = []; 
            foreach( $field->inputs as $input ){
                if( empty(rgar( $entry, (string) $input['id'] )) ){
                    continue;
                }
                $value[] = '<li style="font-family: sans-serif; font-size:12px;">'.rgar( $entry, (string) $input['id'] ).'</li>';
            }
            if( empty($value) ){
                break;
            }
            echo '<div style="font-family: sans-serif; font-size:12px;float:left;width:100%; font-weight: bold; background-color: #EAF2FA; padding: 7px 7px;
            ">'.$field->label.'</div>'; 
            echo '<div style="float:left;width:100%; background-color: #FFF;
            "><ul>'.implode( "<br>", $value ).'</ul></div>'; 
        break;
        case 'date':
            if( empty(rgar( $entry, $field->id )) ){
                break;
            }
            echo '<div style="font-family: sans-serif; font-size:12px;float:left;width:100%; font-weight: bold; background-color: #EAF2FA; padding: 7px 7px;
            ">'.$field->label.'</div>'; 
            echo '<div style="font-family: sans-serif; font-size:12px;float:left;width:100%; background-color: #FFF; padding: 7px 7px 7px 30px;
            ">'.date( 'm/d/Y', strtotime( rgar( $entry, $field->id ) ) ).'</div>'; 
        break;
        case 'list':
            $values = unserialize(rgar( $entry, $field->id ));
            if( empty($values) ){
                break;
            }
            echo '<div style="font-family: sans-serif; font-size:12px;float:left;width:100%; font-weight: bold; background-color: #EAF2FA; padding: 7px 7px;
            ">'.$field->label.'</div>'; 
            echo '<font style="font-family: sans-serif; font-size:12px;"><table class="gfield_list" style="border-top: 1px solid #DFDFDF; border-left: 1px solid #DFDFDF; border-spacing: 0; padding: 0; margin: 2px 0 6px; width: 100%">';
            foreach( $values as $key => $row ) {
                $headingTable = [];
                $dataTable    = [];
                foreach( $row as $head => $data ) {
                    if( $key == 0 ){
                        $headingTable[] = '<th style="background-image: none; border-right: 1px solid #DFDFDF; border-bottom: 1px solid #DFDFDF; padding: 6px 10px; font-family: sans-serif; font-size: 12px; font-weight: bold; background-color: #F1F1F1; color:#333; text-align:left">'.$head.'</th>';
                    }	
                    $dataTable[] = '<td style="padding: 6px 10px; border-right: 1px solid #DFDFDF; border-bottom: 1px solid #DFDFDF; border-top: 1px solid #FFF; font-family: sans-serif; font-size:12px;">'.$data.'</td>';
                }

                if( !empty($headingTable) ){
                    echo '<tr>'.implode( "", $headingTable ).'</tr>';
                }
                
                if( !empty($dataTable) ){
                    echo '<tr>'.implode( "", $dataTable ).'</tr>';
                }
            }
            echo "</table></font>";
        break;
        default:
            if( empty(rgar( $entry, $field->id )) ){
                break;
            }
            echo '<div style="font-family: sans-serif; font-size:12px;float:left;width:100%; font-weight: bold; background-color: #EAF2FA; padding: 7px 7px;
            ">'.$field->label.'</div>'; 
            echo '<div style="font-family: sans-serif; font-size:12px;float:left;width:100%; background-color: #FFF; padding: 7px 7px 7px 30px;
            ">'.rgar( $entry, $field->id ).'</div>'; 
    }
}
?>