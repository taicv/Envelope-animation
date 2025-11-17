<?php
// Suppress deprecated warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);


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
if(!file_exists("./".base64_encode($name).".mp4")){
	getVideo($name);
}else{
	header("Location: ./".base64_encode($name).".mp4");
	exit;
}

function getVideo($author_data){
	
	try {
		// 1) Prepare encoded name for love param (match JS utoa + encodeURIComponent)
		$utf8Name = mb_convert_encoding((string)$author_data, 'UTF-8', 'UTF-8');
		$loveBase64 = base64_encode($utf8Name);
		$loveEncoded = rawurlencode($loveBase64);

		// 2) Build base URL to target app (use real domains). Default to Saigon on localhost
		$hostHeader = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		$isLocal = $hostHeader === 'localhost' || $hostHeader === '127.0.0.1';
		$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$uriLower = strtolower($requestUri);
		
		$useSaigon = $isLocal ? true : (strpos($uriLower, 'thiep') !== false || strpos($uriLower, 'saigon') !== false);
		if (!$useSaigon) {
			$useSaigon = !(strpos($uriLower, 'card') !== false || strpos($uriLower, 'tuyphong') !== false);
		}
		
		$baseUrl = $useSaigon
			? 'https://vy.taicv.com/thiep/?play=true&love='
			: 'https://vy.taicv.com/card/?play=true&love=';
		
		$appUrl = $baseUrl . $loveEncoded;

		// 3) Request to ngrok service to generate video
		$serviceBase = 'https://powerful-totally-terrapin.ngrok-free.app';
		$key = 'babb4d6ba4dcd4a952c315226bfccf6c4b86907e';
		$apiUrl = $serviceBase . '/?url=' . rawurlencode($appUrl) . '&key=' . rawurlencode($key);

		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $apiUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTPHEADER => ['ngrok-skip-browser-warning: true'],
			CURLOPT_TIMEOUT => 60,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 0,
		]);
		$response = curl_exec($ch);
		$curlErr = curl_error($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($response === false) {
			throw new Exception('Failed to contact video service: ' . $curlErr);
		}
		if ($httpCode < 200 || $httpCode >= 300) {
			throw new Exception('Video service HTTP ' . $httpCode . ': ' . $response);
		}

		$data = json_decode($response, true);
		if (!is_array($data) || empty($data['success']) || empty($data['file'])) {
			throw new Exception('Video generation failed or invalid response.');
		}

		// 4) Download the generated video file
		$remotePath = $data['file']; // e.g., /files/xxx.mp4
		$downloadUrl = $serviceBase . $remotePath;

		$ch2 = curl_init();
		curl_setopt_array($ch2, [
			CURLOPT_URL => $downloadUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTPHEADER => ['ngrok-skip-browser-warning: true'],
			CURLOPT_TIMEOUT => 180,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 0,
		]);
		$videoBinary = curl_exec($ch2);
		$curlErr2 = curl_error($ch2);
		$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
		curl_close($ch2);

		if ($videoBinary === false) {
			throw new Exception('Failed to download video: ' . $curlErr2);
		}
		if ($httpCode2 < 200 || $httpCode2 >= 300) {
			throw new Exception('Download HTTP ' . $httpCode2);
		}

		// 5) Save to current path using base64_encode($name).mp4
		$filename = base64_encode($utf8Name) . '.mp4';
		$savePath = __DIR__ . DIRECTORY_SEPARATOR . $filename;

		$bytes = @file_put_contents($savePath, $videoBinary);
		if ($bytes === false) {
			throw new Exception('Failed to save video to disk.');
		}

		// 6) Deliver video file
		header('Location: ./' . $filename);
		exit;
		
	} catch(Exception $err) {
		// Handle errors
		echo $err->getMessage();
	}
}

?>