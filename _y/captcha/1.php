<?php
session_start();

if( !function_exists('hex2rgb') )
{
    function hex2rgb($hex_str, $return_string = false, $separator = ',')
    {
        $hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str); // Gets a proper hex string
        $rgb_array = array();
        if( strlen($hex_str) == 6 ) {
            $color_val = hexdec($hex_str);
            $rgb_array['r'] = 0xFF & ($color_val >> 0x10);
            $rgb_array['g'] = 0xFF & ($color_val >> 0x8);
            $rgb_array['b'] = 0xFF & $color_val;
        } elseif( strlen($hex_str) == 3 ) {
            $rgb_array['r'] = hexdec(str_repeat(substr($hex_str, 0, 1), 2));
            $rgb_array['g'] = hexdec(str_repeat(substr($hex_str, 1, 1), 2));
            $rgb_array['b'] = hexdec(str_repeat(substr($hex_str, 2, 1), 2));
        } else {
            return false;
        }
        return $return_string ? implode($separator, $rgb_array) : $rgb_array;
    }
}
function simple_php_captcha($config = array())
{
    // Check for GD library
    if( !function_exists('gd_info') ){throw new Exception('Required GD library is missing');}

    $bg_path = dirname(__FILE__) . '/backgrounds/';
    $font_path = dirname(__FILE__) . '/fonts/';

    // Default values
    $captcha_config = array(
        'code' => '',
        'min_length' => 5,
        'max_length' => 5,
        'backgrounds' => array(
            $bg_path . '45-degree-fabric.png',
            $bg_path . 'cloth-alike.png',
            $bg_path . 'grey-sandbag.png',
            $bg_path . 'kinda-jean.png',
            $bg_path . 'polyester-lite.png',
            $bg_path . 'stitched-wool.png',
            $bg_path . 'white-carbon.png',
            $bg_path . 'white-wave.png'
        ),
        'fonts' => array(
            $font_path . 'times_new_yorker.ttf'
        ),
        'characters' => '0123456789',
        'min_font_size' => 28,
        'max_font_size' => 28,
        'color' => '#666',
        'angle_min' => 0,
        'angle_max' => 10,
        'shadow' => true,
        'shadow_color' => '#fff',
        'shadow_offset_x' => -1,
        'shadow_offset_y' => 1
    );

    // Overwrite defaults with custom config values
    if( is_array($config) ) {foreach( $config as $key => $value ) $captcha_config[$key] = $value;}

    // Restrict certain values
    if( $captcha_config['min_length'] < 1 ) $captcha_config['min_length'] = 1;
    if( $captcha_config['angle_min'] < 0 ) $captcha_config['angle_min'] = 0;
    if( $captcha_config['angle_max'] > 10 ) $captcha_config['angle_max'] = 10;
    if( $captcha_config['angle_max'] < $captcha_config['angle_min'] ) $captcha_config['angle_max'] = $captcha_config['angle_min'];
    if( $captcha_config['min_font_size'] < 10 ) $captcha_config['min_font_size'] = 10;
    if( $captcha_config['max_font_size'] < $captcha_config['min_font_size'] ) $captcha_config['max_font_size'] = $captcha_config['min_font_size'];

    // Generate CAPTCHA code if not set by user
    if( empty($captcha_config['code']) )
    {
        $captcha_config['code'] = '';
        $length = mt_rand($captcha_config['min_length'], $captcha_config['max_length']);
        while( strlen($captcha_config['code']) < $length )
        {
            $captcha_config['code'] .= substr($captcha_config['characters'], mt_rand() % (strlen($captcha_config['characters'])), 1);
        }
    }


    //$image_src='/1.php?captchaName='.$captchaName.'&'.$captchaName.'&amp;t=' . urlencode(microtime());
    $image_src='';

    return array(
        'code' => $captcha_config['code'],
        'image_src' => $image_src,
        'config' => serialize($captcha_config)
    );
}

$captchaName= $_GET['captchaName'] ?? 'captcha';


$captchaResult = simple_php_captcha([]);

$_SESSION[$captchaName] = $captchaResult;



// Draw the image

$captchaSession = $_SESSION[$captchaName];
$captcha_config = $captchaSession ? unserialize($captchaSession['config']) : false;
if( !$captcha_config ) exit("ss");

// Pick random background, get info, and start captcha
$background = $captcha_config['backgrounds'][mt_rand(0, count($captcha_config['backgrounds']) -1)];
list($bg_width, $bg_height, $bg_type, $bg_attr) = getimagesize($background);

$captchaImage = imagecreatefrompng($background);
if (!$captchaImage) {
    die('imagecreatefrompng failed');
}

$color = hex2rgb($captcha_config['color']);
$color = imagecolorallocate($captchaImage, $color['r'], $color['g'], $color['b']);

// Determine text angle
$angle = mt_rand( $captcha_config['angle_min'], $captcha_config['angle_max'] ) * (mt_rand(0, 1) == 1 ? -1 : 1);

// Select font randomly
$font = $captcha_config['fonts'][mt_rand(0, count($captcha_config['fonts']) - 1)];

// Verify font file exists
if( !file_exists($font) ) throw new Exception('Font file not found: ' . $font);

//Set the font size.
$font_size = mt_rand($captcha_config['min_font_size'], $captcha_config['max_font_size']);
$text_box_size = imagettfbbox($font_size, $angle, $font, $captcha_config['code']);

// Determine text position
$box_width = abs($text_box_size[6] - $text_box_size[2]);
$box_height = abs($text_box_size[5] - $text_box_size[1]);
$text_pos_x_min = 0;
$text_pos_x_max = ($bg_width) - ($box_width);
$text_pos_x = mt_rand($text_pos_x_min, $text_pos_x_max);
$text_pos_y_min = $box_height;
$text_pos_y_max = ($bg_height) - ($box_height / 2);
if ($text_pos_y_min > $text_pos_y_max)
{
    $temp_text_pos_y = $text_pos_y_min;
    $text_pos_y_min = $text_pos_y_max;
    $text_pos_y_max = $temp_text_pos_y;
}
$text_pos_y = mt_rand(round($text_pos_y_min), round($text_pos_y_max));

// Draw shadow
if( $captcha_config['shadow'] )
{
    $shadow_color = hex2rgb($captcha_config['shadow_color']);
    $shadow_color = imagecolorallocate($captchaImage, $shadow_color['r'], $shadow_color['g'], $shadow_color['b']);
    $drawn = imagettftext($captchaImage, $font_size, $angle, $text_pos_x + $captcha_config['shadow_offset_x'], $text_pos_y + $captcha_config['shadow_offset_y'], $shadow_color, $font, $captcha_config['code']);
    if (!$drawn) {
        die('imagettftext failed');
    }
}

// Draw text
$drawn = imagettftext($captchaImage, $font_size, $angle, $text_pos_x, $text_pos_y, $color, $font, $captcha_config['code']);
if (!$drawn) {
    die('imagettftext failed');
}
// Output image
header("Content-type: image/png");
$result = imagepng($captchaImage);
if (!$result) {
    die('imagepng failed');
}
