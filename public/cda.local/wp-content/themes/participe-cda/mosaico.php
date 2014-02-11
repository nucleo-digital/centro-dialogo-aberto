<?php

require("../../../wp-config.php");

// this value is from max_width _GET param
$screen_width = $_GET['screen_with'];
// this value is from taxonomy_id _GET param
// taxonomy=category&tag_ID=1&post_type=avaliacao
$tag_id = $_GET['tag_id'];
// list of images separate by comma
$images_id_field  = get_tax_meta($tag_id,'cda_text_field_id');
$images_id = explode(',', $images_id_field);

$max_width_image = 200;
$max_height_image = 200;

$cols = intval(round($screen_width / $max_width_image));
$rows = intval(round(count($images_id) / $cols));

$current_row = $current_col = 0;

// inserting repeat images to complete all mosaic

$total_images = count($images_id);
$missing_images = $total_images % $cols;
$total_missing_images = $cols - $missing_images;

for ($i = 0; $i < $total_missing_images; $i++) {
    $images_id[] = $images_id[$i];
}

//var_dump($missing_images, $cols);

$main_image=imagecreatetruecolor($max_width_image*$cols,$max_height_image*$rows);

foreach ($images_id as $image_id) {
    $image = wp_get_attachment_metadata($image_id);
    $upload_dir = wp_upload_dir();
    $sourcefile = $upload_dir['basedir'] . '/' . $image['file'];

    $fileType = strtolower(substr($sourcefile, strlen($sourcefile)-3));

    switch($fileType) {
        case('gif'):
            $sourcefile_id = imagecreatefromgif($sourcefile);
            break;
            
        case('png'):
            $sourcefile_id = imagecreatefrompng($sourcefile);
            break;
            
        default:
            $sourcefile_id = imagecreatefromjpeg($sourcefile);
    }

    imagecopymerge($main_image, $sourcefile_id, ($max_width_image*$current_col), ($max_height_image*$current_row), 0, 0, imagesx($sourcefile_id), imagesy($sourcefile_id), 100);
    
    if ($current_col+1 === $cols) {
        $current_col = 0;
        $current_row++;
    }else {
        $current_col++;
    }
}
Header("Content-type: image/jpeg");
imagepng($main_image);