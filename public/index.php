<?php 

require __DIR__.'/../vendor/autoload.php'; 

use Symfony\Component\HttpFoundation\Request; 

/**
 * Parse a string, trying to find a valid telephone number. As soon as it finds a 
 * valid number, it'll return it in E1624 format. If it can't find any, it'll 
 * simply return NULL.
 * 
 * @param  string   $text           The string to parse
 * @param  string   $country_code   The two digit country code to use as a "hint"
 * @return string | NULL
 */
function findPhoneNumber($text, $country_code = 'GB') {

  // Get an instance of Google's libphonenumber
  $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

  // Use a simple regular expression to try and find candidate phone numbers
  preg_match_all('/(\+\d+)?\s*(\(\d+\))?([\s-]?\d+)+/', $text, $matches);
  
  // Iterate through the matches
  foreach ($matches as $match) {

    foreach ($match as $value) {

      try {
        
        // Attempt to parse the number
        $number = $phoneUtil->parse(trim($value), $country_code);    
        
        // Just because we parsed it successfully, doesn't make it vald - so check it
        if ($phoneUtil->isValidNumber($number)) {
          
          // We've found a telephone number. Format using E.164, and exit
          return $phoneUtil->format($number, \libphonenumber\PhoneNumberFormat::E164);

        }

      } catch (\libphonenumber\NumberParseException $e) {
        
        // Ignore silently; getting here simply means we found something that isn't a phone number
                
      }

    }
  }

  return null;

}

$app = new Silex\Application(); 

$app->register(new Silex\Provider\TwigServiceProvider(), [
  'twig.path' => __DIR__.'/../views',
]);

$app['debug'] = true; 

$app->get('/', function() use ($app) { 

  return $app['twig']->render('index.twig');

}); 

$app->post('/', function(Request $request) use ($app) { 
  
  // Grab the uploaded file
  $file = $request->files->get('upload'); 
  
  // Extract some information about the uploaded file
  $info = new SplFileInfo($file->getClientOriginalName());
  
  // Create a quasi-random filename
  $filename = sprintf('%d.%s', time(), $info->getExtension());

  // Copy the file
  $file->move(__DIR__.'/../uploads', $filename); 

  // Instantiate the Tessearct library
  $tesseract = new TesseractOCR(__DIR__ . '/../uploads/' . $filename);

  // Perform OCR on the uploaded image
  $text = $tesseract->recognize();

  return $app['twig']->render(
    'results.twig',
    [
      'text'  =>  $text,
    ]
  );

}); 

$app->post('/identify-telephone-number', function(Request $request) use ($app) { 
  
  // Grab the uploaded file
  $file = $request->files->get('upload'); 
  
  // Extract some information about the uploaded file
  $info = new SplFileInfo($file->getClientOriginalName());
  
  // Create a quasi-random filename
  $filename = sprintf('%d.%s', time(), $info->getExtension());

  // Copy the file
  $file->move(__DIR__.'/../uploads', $filename); 

  // Instantiate the Tessearct library
  $tesseract = new TesseractOCR(__DIR__ . '/../uploads/' . $filename);

  // Perform OCR on the uploaded image
  $text = $tesseract->recognize();

  $number = findPhoneNumber($text, 'GB');

  return $app->json(
    [
      'number'     =>  $number,
    ]
  );

}); 

$app->run(); 