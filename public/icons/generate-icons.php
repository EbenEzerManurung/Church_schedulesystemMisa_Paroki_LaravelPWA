<?php
$sizes = [72, 96, 128, 144, 152, 192, 384, 512];

foreach ($sizes as $size) {
    $image = imagecreatetruecolor($size, $size);
    
    // Background gradient
    $color1 = imagecolorallocate($image, 30, 64, 175); // #1e40af
    $color2 = imagecolorallocate($image, 59, 130, 246); // #3b82f6
    
    imagefilledrectangle($image, 0, 0, $size, $size, $color1);
    
    // Draw simple church shape
    $white = imagecolorallocate($image, 255, 255, 255);
    $yellow = imagecolorallocate($image, 251, 191, 36);
    
    $centerX = $size / 2;
    $centerY = $size / 2;
    $w = $size * 0.6;
    $h = $size * 0.7;
    
    // Roof (triangle)
    $points = [
        $centerX - $w/2, $centerY - $h/4,
        $centerX, $centerY - $h/2,
        $centerX + $w/2, $centerY - $h/4
    ];
    imagefilledpolygon($image, $points, 3, $white);
    
    // Body
    imagefilledrectangle($image, $centerX - $w/3, $centerY - $h/4, $centerX + $w/3, $centerY + $h/3, $white);
    
    // Cross
    imagefilledrectangle($image, $centerX - 3, $centerY - $h/2, $centerX + 3, $centerY + $h/4, $white);
    imagefilledrectangle($image, $centerX - $w/6, $centerY - $h/3, $centerX + $w/6, $centerY - $h/4, $white);
    
    // Window
    imagefilledrectangle($image, $centerX - $w/8, $centerY, $centerX + $w/8, $centerY + $h/3, $yellow);
    
    // Save image
    imagepng($image, __DIR__ . "/icon-{$size}x{$size}.png");
    imagedestroy($image);
    
    echo "Generated icon-{$size}x{$size}.png\n";
}

echo "All icons generated successfully!\n";