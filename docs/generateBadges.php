<?php
declare(strict_types=1);

// cc = code coverage (provided by PhpUnit)
// msi = mutation score indicator (provided by Infection)
// mcc = mutation code coverage (provided by Infection)
// ccm = code coverage MSI (provided by Infection)
$options = getopt('', ['cc:', 'msi:', 'mcc:', 'ccm:']);

$height = 20;
$width  = 43;

$img = imagecreate($width, $height);

$white      = imagecolorallocate($img, 255, 255, 255);
$black      = imagecolorallocate($img, 0, 0, 0);
$darkGreen  = imagecolorallocate($img, 61, 125, 71);
$lightGreen = imagecolorallocate($img, 60, 179, 113);
$yellow     = imagecolorallocate($img, 255, 165, 0);
$red        = imagecolorallocate($img, 255, 0, 0);

foreach ($options as $optionName => $option) {
    $option = (int) $option;

    switch (true) {
        case $option >= 90:
            $colour = $darkGreen;
            break;

        case $option >= 80:
            $colour = $lightGreen;
            break;

        case $option >= 60:
            $colour = $yellow;
            break;

        default:
            $colour = $red;
    }

    imagefilledrectangle($img, 0, 0, $width, $height, $colour);
    imagestring($img, 5, 7, 2, $option . '%', $white);
    imagepng($img, sprintf('docs/img/%s.png', $optionName));
}