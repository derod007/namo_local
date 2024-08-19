<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
include_once("./_common.php");

//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

if( !function_exists('json_encode') ) {
    @include_once("./JSON.php");
    function json_encode($data) {
        $json = new Services_JSON();
        return( $json->encode($data) );
    }
}

@ini_set('gd.jpeg_ignore_warning', 1);

$ym = date('ym', SERVER_TIME);

$data_dir = JSB_DATA_PATH.'/editor/'.$ym.'/';
$data_url = JSB_DATA_URL.'/editor/'.$ym.'/';

@mkdir($data_dir, DIR_PERMISSION);
@chmod($data_dir, DIR_PERMISSION);

if(!function_exists('ft_nonce_is_valid')){
    include_once('../../../editor.lib.php');
}

$is_editor_upload = false;

if( isset($_GET['_nonce']) && ft_nonce_is_valid( $_GET['_nonce'] , 'smarteditor' ) ){
    $is_editor_upload = true;
}

if( $is_editor_upload ) {
    
    //run_event('smarteditor_photo_upload', $data_dir, $data_url);

    require('UploadHandler.php');
    $options = array(
        'upload_dir' => $data_dir,
        'upload_url' => $data_url,
        // This option will disable creating thumbnail images and will not create that extra folder.
        // However, due to this, the images preview will not be displayed after upload
        'image_versions' => array()
    );

    $upload_handler = new UploadHandler($options);

} else {
    echo json_encode(array('files'=>array('0'=>array('error'=>'정상적인 업로드가 아닙니다.'))));
    exit;
}