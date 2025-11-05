<?php
// Suppress deprecated warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

require '../../SimpleImage.php';

// Helper: detect and decode base64 (handles URL-safe variants). Returns original if not base64.
function decodeIfBase64($value){
	$trimmed = trim((string) $value);
	if($trimmed === '') return $value;
	// Normalize URL-safe base64 to standard for decoding
	$normalized = strtr($trimmed, '-_', '+/');
	// Length sanity: valid base64 length mod 4 is 0,2,3 (1 is invalid)
	$lenMod = strlen($normalized) % 4;
	if($lenMod === 1) return $value;
	// Add padding if missing
	if($lenMod > 0) {
		$normalized .= str_repeat('=', 4 - $lenMod);
	}
	$decoded = base64_decode($normalized, true);
	if($decoded === false) return $value;
	// Re-encode and compare (ignore padding differences and variant)
	$re = rtrim(strtr(base64_encode($decoded), '+/', '-_'), '=');
	$in = rtrim($trimmed, '=');
	if($re === $in) return $decoded;
	return $value;
}

// Get the name from URL parameter (decode if base64)
$rawName = isset($_GET['love']) ? $_GET['love'] : 'Guest';
$name = decodeIfBase64($rawName);
if(!file_exists("./".base64_encode($name).".jpg")){
	CreateOG($name);
}else{
	header("Location: ./".base64_encode($name).".jpg");
	exit;
}

function CreateOG($author_data){
	$background_file	=	"../letter.jpg";

	
	
	try {
	  // Create a new SimpleImage object
	  $image 			= new \claviska\SimpleImage();		  
	  $author_name		= new \claviska\SimpleImage();
	  $author_name_boundary = 0;
	  
	  // Create a blank canvas for the text
	  $author_name
		->fromNew(800, 200, 'transparent')
		->text($author_data, array('fontFile' =>"../DVN-Parisienne-Regular-c6gxjr.ttf",'size' =>70,'color' =>"#04274d",'anchor' =>"bottom"),$author_name_boundary)
		->bestFit(500,150);
		//->toFile("./images/".$author_data[1].".png", 'image/jpeg');
		
			//var_dump($author_name_boundary);

		  // Magic! ✨
		  $image
			->fromFile($background_file)                     // load image.jpg
			->autoOrient()                              // adjust orientation based on exif data
			//->thumbnail(800,420,"center")                         // resize to 320x200 pixels
			//->flip('x')                                 // flip horizontally
			//->colorize('DarkBlue')                      // tint dark blue
			//->border('black', 10)                       // add a 10 pixel black border
			//->overlay($author_avatar,"top left",1,110,127,true)  // add a watermark image
			//->overlay($watermark_file, 'bottom right')  // add a watermark image
			//->text("Chu Văn Tài", array('fontFile' =>"./SVN-Arsilon.ttf",'size' =>15,'color' =>"white",'anchor' =>"center",'xOffset' =>325,'yOffset' =>178,'calculateOffsetFromEdge' =>true))  // add a watermark image
			->overlay($author_name, 'bottom',1,-210,-415,false)
			//->text($author_data[1], array('fontFile' =>"./SVN-Arsilon.ttf",'size' =>30,'color' =>"white",'anchor' =>"center",'xOffset' =>65,'yOffset' =>-15,'calculateOffsetFromEdge' =>true))  // add a watermark image
			//->text($author_data[1], array('fontFile' =>"./SVN-Arsilon.ttf",'size' =>30,'color' =>"white",'anchor' =>"center",'xOffset' =>65,'yOffset' =>-15,'calculateOffsetFromEdge' =>true))  // add a watermark image
			//->toScreen('image/jpeg');      // convert to PNG and save a copy to new-image.png
			->toFile("./".base64_encode($author_data).".jpg", 'image/jpeg')
			->toScreen();                               // output to the screen

		  // And much more! 💪
		} catch(Exception $err) {
		  // Handle errors
		  echo $err->getMessage();
		}
	}



?>