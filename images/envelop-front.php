<?php
// Suppress deprecated warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

require '../SimpleImage.php';

// Get the name from URL parameter
$name = isset($_GET['love']) ? $_GET['love'] : 'Guest';
CreateOG($name);

function CreateOG($author_data){
	$background_file	=	"./envelop-front.jpg";

	
	
	try {
	  // Create a new SimpleImage object
	  $image 			= new \claviska\SimpleImage();		  
	  $author_name		= new \claviska\SimpleImage();
	  $author_name_boundary = 0;
	  
	  // Create a blank canvas for the text
	  $author_name
		->fromNew(400, 100, 'transparent')
		->text($author_data, array('fontFile' =>"./DVN-Parisienne-Regular-c6gxjr.ttf",'size' =>60,'color' =>"#04274d",'anchor' =>"bottom"),$author_name_boundary)
		->bestFit(320,60);
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
			->overlay($author_name, 'bottom',1,60,-148,false)
			//->text($author_data[1], array('fontFile' =>"./SVN-Arsilon.ttf",'size' =>30,'color' =>"white",'anchor' =>"center",'xOffset' =>65,'yOffset' =>-15,'calculateOffsetFromEdge' =>true))  // add a watermark image
			//->text($author_data[1], array('fontFile' =>"./SVN-Arsilon.ttf",'size' =>30,'color' =>"white",'anchor' =>"center",'xOffset' =>65,'yOffset' =>-15,'calculateOffsetFromEdge' =>true))  // add a watermark image
			//->toScreen('image/jpeg');      // convert to PNG and save a copy to new-image.png
			->toScreen();                               // output to the screen

		  // And much more! 💪
		} catch(Exception $err) {
		  // Handle errors
		  echo $err->getMessage();
		}
	}



?>