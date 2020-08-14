<?php
// fhola
class form_helpers{
    // file_upload method
    public function file_upload($name, $file_type, $validate = TRUE, $file_size = NULL){
        // check file field is empty
        if($validate == TRUE){
            if(empty($_FILES[$name]['name'])){
                throw new Exception("Upload field is empty");
            }   
        }
        
        if(!empty($_FILES[$name]['tmp_name'])){
            $file_tmp_name_m = $_FILES[$name]['tmp_name'];
            $file_name_m = $_FILES[$name]['name'];
            $file_size_m = $_FILES[$name]['size'];

            // check for multiple allowed extension
            if(strpos($file_type, ",") !== FALSE){
                $allowed_ext = explode(",", str_replace(" ", NULL, $file_type));   
            }
            else{
                $allowed_ext = array($file_type);
            }
            // get file extension
            $ext = pathinfo($file_name_m, PATHINFO_EXTENSION);
            if(!in_array($ext, $allowed_ext)){
                throw new Exception("Unsupported file type");
            }
            // check file size
            else if($file_size != NULL){
                // check if file size has 2 values
                if(strpos($file_size, ",") !== FALSE){
                    $file_size = explode(",", $file_size);
                    $max_size = $file_size[0];
                    $min_size = $file_size[1];

                    // validate for max size
                    if($file_size_m > $max_size*1000000){
                        throw new Exception("Maximum file size is $max_size");
                    }
                    // validate for min size
                    else if($file_size_m < $min_size*1000000){
                        throw new Exception("Minimum file size is $min_size");
                    }
                }
                else{
                    // validate for max size
                    if($file_size_m > $file_size*1000000){
                        if(is_float($file_size)){
                            $file_size = (($file_size*1000000)/1000)."KB";
                        }
                        else{
                            $file_size = $file_size."MB";
                        }
                        throw new Exception("Maximum file size is $file_size");
                    }
                }
            }

            return array(
            "tmp_name" => $file_tmp_name_m,
            "name" => $file_name_m,
            "size" => $file_size_m,
            "ext" => $ext
            );   
        }
        else{
            return FALSE;
        }
    }
    
    public function watermark_image($target, $wtrmrk_file, $newcopy){
        // get extension of file_upload
        $ext = explode('.',basename($target))[1];
        
        if($ext == 'png' || $ext == 'PNG'){
            $image = imagecreatefrompng($target);
        }
        else if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG'){
            $image = imagecreatefromjpeg($target);
        }
        $imageSize = getimagesize($target);

        $watermark = imagecreatefrompng($wtrmrk_file);

        $watermark_o_width = imagesx($watermark);
        $watermark_o_height = imagesy($watermark);

        $newWatermarkWidth = $imageSize[0]-20;
        $newWatermarkHeight = $watermark_o_height * $newWatermarkWidth / $watermark_o_width;

        imagecopyresized($image, $watermark, $imageSize[0]/2 - $newWatermarkWidth/2, $imageSize[1]/2 - $newWatermarkHeight/2, 0, 0, $newWatermarkWidth, $newWatermarkHeight, imagesx($watermark), imagesy($watermark));

        if($ext == 'png' || $ext == 'PNG'){
            imagepng($image, $newcopy);
        }
        else if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG'){
            imagejpeg($image, $newcopy);
        }

        imagedestroy($image);
        imagedestroy($watermark);
    }
    
    function resizeImg($src, $dest, $desired_width) {
        // get extension of file_upload
        $ext = explode('.',basename($src))[1];
        
        if($ext == 'png' || $ext == 'PNG'){
            $source_image = imagecreatefrompng($src);
        }
        else if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG'){
            $source_image = imagecreatefromjpeg($src);
        }
        
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desired_height = floor($height * ($desired_width / $width));

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
        
        /* create the physical thumbnail image to its destination */
        if($ext == 'png' || $ext == 'PNG'){
            imagepng($virtual_image, $dest);
        }
        else if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG'){
            imagejpeg($virtual_image, $dest);
        }
        
    }
    
    public function countries(){
        return array("Afghanistan","Albania","Algeria","American Samoa","Andorra","Angola","Anguilla","Antarctica","Antigua and Barbuda","Argentina","Armenia","Aruba","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia and Herzegowina","Botswana","Bouvet Island","Brazil","British Indian Ocean Territory","Brunei Darussalam","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Cayman Islands","Central African Republic","Chad","Chile","China","Christmas Island","Cocos (Keeling) Islands","Colombia","Comoros","Cook Islands","Costa Rica","Cote d'Ivoire","Croatia (Hrvatska)","Cuba","Cyprus","Czech Republic","Democratic Republic Of Congo","Democratic Republic Of Korea","Denmark","Djibouti","Dominica","Dominican Republic","East Timor","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Falkland Islands (Malvinas)","Faroe Islands","Fiji","Finland","France","France, Metropolitan","French Guiana","French Polynesia","French Southern Territories","Gabon","Gambia","Georgia","Germany","Ghana","Gibraltar","Greece","Greenland","Grenada","Guadeloupe","Guam","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Heard and Mc Donald Islands","Holy See (Vatican City State)","Honduras","Hong Kong","Hungary","Iceland","India","Indonesia","Iran (Islamic Republic of)","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Kuwait","Kyrgyzstan","Lao People's Democratic Republic","Latvia","Lebanon","Lesotho","Liberia","Libyan Arab Jamahiriya","Liechtenstein","Lithuania","Luxembourg","Macau","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Martinique","Mauritania","Mauritius","Mayotte","Mexico","Micronesia, Federated States of","Moldova, Republic of","Monaco","Mongolia","Montserrat","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","Netherlands Antilles","New Caledonia","New Zealand","Nicaragua","Niger","Nigeria","Niue","Norfolk Island","Northern Mariana Islands","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Pitcairn","Poland","Portugal","Puerto Rico","Qatar","Reunion","Romania","Russian Federation","Saint Kitts and Nevis", "Saint LUCIA","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe", "Saudi Arabia","Senegal","Seychelles","Sierra Leone","Singapore","Slovakia (Slovak Republic)","Slovenia","Solomon Islands","Somalia","South Africa","South Georgia and the South Sandwich Islands","Spain","Sri Lanka","St. Helena","St. Pierre and Miquelon","Sudan","Suriname","Svalbard and Jan Mayen Islands","Swaziland","Sweden","Switzerland","Syrian Arab Republic","Taiwan, Province of China","Tajikistan","Tanzania, United Republic of","Thailand","The Former Yugoslav Republic of Macedonia","Togo","Tokelau","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Turks and Caicos Islands","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","United States Minor Outlying Islands","Uruguay","Uzbekistan","Vanuatu","Venezuela","Viet Nam","Virgin Islands (British)","Virgin Islands (U.S.)","Wallis and Futuna Islands","Western Sahara","Yemen","Yugoslavia","Zambia","Zimbabwe");
    }
}
// fhola