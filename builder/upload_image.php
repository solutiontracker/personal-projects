<?php if($_FILES)
{
$resultArray = array();
	foreach ( $_FILES as $file){
             	$fileName = $file['name'];
             	$tmpName = $file['tmp_name'];
             	$fileSize = $file['size'];
             	$fileType = $file['type'];
             	if ($file['error'] != UPLOAD_ERR_OK)
             	{
                 		error_log($file['error']);
                 		echo JSON_encode(null);
             	}
             	$fp = fopen($tmpName, 'r');
             	$content = fread($fp, filesize($tmpName));
             	fclose($fp);
            	$result=array(
                 		'name'=>$file['name'],
                 		'type'=>'image',
                 		'src'=>"data:".$fileType.";base64,".base64_encode($content),
                 		'height'=>350,
                 		'width'=>250
             	); 
		// we can also add code to save images in database here.
              	array_push($resultArray,$result);
 	}    
$response = array( 'data' => $resultArray );
echo json_encode($response);
} ?>