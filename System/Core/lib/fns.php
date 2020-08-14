<?php
session_start();

function clean_data($data){
    $data = stripslashes($data);
    $data = htmlspecialchars($data,ENT_QUOTES);
    $data = trim($data);
    
    return $data;
}

function checkSpace($data){
    foreach($data as $value){
        $data1 = strlen($data);
        $data2 = strlen(trim($data));

        if($data1 != $data2){
            return true;
        continue;
        }
            return false;
    }
}

function validateUsername($data){
    $error = array();
    
    // check for white space
    if(str_replace(' ',NULL,$data) != $data){
        $error[] = 'Username cannot have White Spaces';
    }
    else{
        // check for special characters
        if(!preg_match('/^[a-zA-Z0-9]*$/', $data)){
            $error[] = 'Username has invalid characters';
        }
    }
}

function check_email($data){
    $data = filter_var($data, FILTER_VALIDATE_EMAIL);
    
    return $data;
}

function check_int($data){
    $data = is_numeric($data);
    
    return $data;
}

function encrypt_data($data){
    $salt = "istdcrpeojsksrui";
    
    $encrypted_data = (base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    return $encrypted_data;
}

function decrypt_data($data){
    $salt = "istdcrpeojsksrui";
    
    $decrypted_data = (mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($data), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    return $decrypted_data;
}

function simple_encrypt($data){
    $encrypted_data = base64_encode(base64_encode(base64_encode(base64_encode(base64_encode($data)))));
    return $encrypted_data;
}

function simple_decrypt($data){
    $decrypted_data = base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($data)))));
    return $decrypted_data;
}

function Response(){
   if(isset($_SESSION['haptic'])){
       print $_SESSION['haptic'];
       unset($_SESSION['haptic']);
   }
}

/*
    FOOTER PAGINATION BUTTON DISPLAY
    @paginationFooter() requires 1 param
    url
*/

function paginationFooter($url, $active_class, $use_list = FALSE){
    global $total_pages,
            $page_num;
    
    // check if there is a query url(GET METHOD INVOLVED)
    $url_prepend = '?';
    if(!empty($_SERVER['QUERY_STRING'])){

        // filter url and remove the last array value(getting rid of the (page value '&page'));
        $return_array = count($_GET)-1;
        
        // check if url contains the $_GET['page'] variable and trim
        $array_trim = (isset($_GET['page'])) ? array_slice($_GET, 0, $return_array) : $_GET;
        foreach($array_trim  as $key => $value){
            $value = str_replace(" ", "+", $value);
            $url_prepend .= $key.'='.urlencode($value).'&';
        }
        $url_prepend = $url_prepend;
    }

        $list = ($use_list == TRUE) ? 'li' : NULL;
        
        if($total_pages > 1){
            $fullUrl = BASE_URL.$url.$url_prepend.'page=';
            $prev_btns = ($page_num <= 4 || $page_num == 0) ? 1 : $page_num-3;
            for($i = $prev_btns; $i < $page_num; $i++){
                $addclass = ($page_num == $i) ? $active_class : NULL;
                print str_replace('li', '<li class="'.$addclass.' page-item">', $list);
                ?>
                    <a href="<?php print $fullUrl.$i; ?>" class="page-link">
                    <?php print $i; ?>
                    </a>
            <?php
                print str_replace('li', '</li>', $list);
            }
            for($i = $page_num; $i <= $page_num+3; $i++){
                $addclass = ($page_num == $i) ? $active_class : NULL;
                $cur_pagebtn = ($page_num == $i) ? 'javascript:void(0);' : $fullUrl.$i;
                if($i <= $total_pages){
                    print str_replace('li', '<li class="'.$addclass.' page-item">', $list);
                ?>
                    <a href="<?php print $cur_pagebtn; ?>" class="page-link">
                    <?php print $i; ?>
                    </a>
                <?php
                    print str_replace('li', '</li>', $list);
                }
            }
        }
}
/*
array values:
    from,
    to,
    subject,
    logo,
    header_color
    body,
    sub_footer = associative array,
    footer,
    error_msg
*/
function fh_mail($content = array(),$use_smtp = FALSE){
    // include php mailer class
    load('System/Vendors/PHPMailer/src/PHPMailer.php');
    load('System/Vendors/PHPMailer/src/SMTP.php');
    load('System/Vendors/PHPMailer/src/Exception.php');
    
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    
    if($use_smtp == TRUE){
        if(!empty(SMTP)){
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = SMTP['host'];  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = SMTP['username'];                 // SMTP username
            $mail->Password = SMTP['password'];                            // SMTP password
            $mail->SMTPSecure = SMTP['secure'];                             // Enable TLS encryption, `ssl` also accepted
            $mail->Port = SMTP['port'];
        }
        else{
            throw new Exception('SMTP Config Error');
        }
    }

    $mail->setFrom($content['from'], SITENAME);
    $mail->addAddress($content['to']);
    $mail->Subject = $content['subject'];
    $mail->isHTML(true);
    $mail->Body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head>
    <!--[if gte mso 9]><xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml><![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
    <title></title>
    <!--[if !mso]><!-- -->
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
    <!--<![endif]-->

    <style type="text/css" id="media-query">
        body {
            margin: 0;
            padding: 0; }

        table, tr, td {
            vertical-align: top;
            border-collapse: collapse; }

        .ie-browser table, .mso-container table {
            table-layout: fixed; }

        * {
            line-height: inherit; }

        a[x-apple-data-detectors=true] {
            color: inherit !important;
            text-decoration: none !important; }

        [owa] .img-container div, [owa] .img-container button {
            display: block !important; }

        [owa] .fullwidth button {
            width: 100% !important; }

        [owa] .block-grid .col {
            display: table-cell;
            float: none !important;
            vertical-align: top; }

        .ie-browser .num12, .ie-browser .block-grid, [owa] .num12, [owa] .block-grid {
            width: 620px !important; }

        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
            line-height: 100%; }

        .ie-browser .mixed-two-up .num4, [owa] .mixed-two-up .num4 {
            width: 204px !important; }

        .ie-browser .mixed-two-up .num8, [owa] .mixed-two-up .num8 {
            width: 408px !important; }

        .ie-browser .block-grid.two-up .col, [owa] .block-grid.two-up .col {
            width: 310px !important; }

        .ie-browser .block-grid.three-up .col, [owa] .block-grid.three-up .col {
            width: 206px !important; }

        .ie-browser .block-grid.four-up .col, [owa] .block-grid.four-up .col {
            width: 155px !important; }

        .ie-browser .block-grid.five-up .col, [owa] .block-grid.five-up .col {
            width: 124px !important; }

        .ie-browser .block-grid.six-up .col, [owa] .block-grid.six-up .col {
            width: 103px !important; }

        .ie-browser .block-grid.seven-up .col, [owa] .block-grid.seven-up .col {
            width: 88px !important; }

        .ie-browser .block-grid.eight-up .col, [owa] .block-grid.eight-up .col {
            width: 77px !important; }

        .ie-browser .block-grid.nine-up .col, [owa] .block-grid.nine-up .col {
            width: 68px !important; }

        .ie-browser .block-grid.ten-up .col, [owa] .block-grid.ten-up .col {
            width: 62px !important; }

        .ie-browser .block-grid.eleven-up .col, [owa] .block-grid.eleven-up .col {
            width: 56px !important; }

        .ie-browser .block-grid.twelve-up .col, [owa] .block-grid.twelve-up .col {
            width: 51px !important; }

        @media only screen and (min-width: 640px) {
            .block-grid {
                width: 620px !important; }
            .block-grid .col {
                vertical-align: top; }
            .block-grid .col.num12 {
                width: 620px !important; }
            .block-grid.mixed-two-up .col.num4 {
                width: 204px !important; }
            .block-grid.mixed-two-up .col.num8 {
                width: 408px !important; }
            .block-grid.two-up .col {
                width: 310px !important; }
            .block-grid.three-up .col {
                width: 206px !important; }
            .block-grid.four-up .col {
                width: 155px !important; }
            .block-grid.five-up .col {
                width: 124px !important; }
            .block-grid.six-up .col {
                width: 103px !important; }
            .block-grid.seven-up .col {
                width: 88px !important; }
            .block-grid.eight-up .col {
                width: 77px !important; }
            .block-grid.nine-up .col {
                width: 68px !important; }
            .block-grid.ten-up .col {
                width: 62px !important; }
            .block-grid.eleven-up .col {
                width: 56px !important; }
            .block-grid.twelve-up .col {
                width: 51px !important; } }

        @media (max-width: 640px) {
            .block-grid, .col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important; }
            .block-grid {
                width: calc(100% - 40px) !important; }
            .col {
                width: 100% !important; }
            .col > div {
                margin: 0 auto; }
            img.fullwidth, img.fullwidthOnMobile {
                max-width: 100% !important; }
            .no-stack .col {
                min-width: 0 !important;
                display: table-cell !important; }
            .no-stack.two-up .col {
                width: 50% !important; }
            .no-stack.mixed-two-up .col.num4 {
                width: 33% !important; }
            .no-stack.mixed-two-up .col.num8 {
                width: 66% !important; }
            .no-stack.three-up .col.num4 {
                width: 33% !important; }
            .no-stack.four-up .col.num3 {
                width: 25% !important; }
            .mobile_hide {
                min-height: 0px;
                max-height: 0px;
                max-width: 0px;
                display: none;
                overflow: hidden;
                font-size: 0px; } }

    </style>
</head>
<body class="clean-body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #FFFFFF">
<style type="text/css" id="media-query-bodytag">
    @media (max-width: 520px) {
        .block-grid {
            min-width: 320px!important;
            max-width: 100%!important;
            width: 100%!important;
            display: block!important;
        }

        .col {
            min-width: 320px!important;
            max-width: 100%!important;
            width: 100%!important;
            display: block!important;
        }

        .col > div {
            margin: 0 auto;
        }

        img.fullwidth {
            max-width: 100%!important;
        }
        img.fullwidthOnMobile {
            max-width: 100%!important;
        }
        .no-stack .col {
            min-width: 0!important;
            display: table-cell!important;
        }
        .no-stack.two-up .col {
            width: 50%!important;
        }
        .no-stack.mixed-two-up .col.num4 {
            width: 33%!important;
        }
        .no-stack.mixed-two-up .col.num8 {
            width: 66%!important;
        }
        .no-stack.three-up .col.num4 {
            width: 33%!important;
        }
        .no-stack.four-up .col.num3 {
            width: 25%!important;
        }
        .mobile_hide {
            min-height: 0px!important;
            max-height: 0px!important;
            max-width: 0px!important;
            display: none!important;
            overflow: hidden!important;
            font-size: 0px!important;
        }
    }
</style>
<!--[if IE]><div class="ie-browser"><![endif]-->
<!--[if mso]><div class="mso-container"><![endif]-->
<table class="nl-container" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #FFFFFF;width: 100%" cellpadding="0" cellspacing="0">
    <tbody>
    <tr style="vertical-align: top">
        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color: #FFFFFF;"><![endif]-->

            <div style="background-color:transparent;">
                <div style="Margin: 0 auto;min-width: 320px;max-width: 620px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;" class="block-grid two-up ">
                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                        <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 620px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->

                        <!--[if (mso)|(IE)]><td align="center" width="310" style=" width:310px; padding-right: 10px; padding-left: 10px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                        <div class="col num6" style="max-width: 320px;min-width: 310px;display: table-cell;vertical-align: top;">
                            <div style="background-color:'.$content['header_color'].'; width: 100% !important;">
                                <!--[if (!mso)&(!IE)]><!--><div style="border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 10px; padding-left: 10px;"><!--<![endif]-->


                                    <div align="left" class="img-container left  autowidth  " style="padding-right: 0px;  padding-left: 0px;">
                                        <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr style="line-height:0px;line-height:0px;"><td style="padding-right: 0px; padding-left: 0px;" align="left"><![endif]-->
                                        <div style="line-height:15px;font-size:1px">&#160;</div>  <img class="left  autowidth " align="left" border="0" src="'.$content['logo'].'" alt="Image" title="Image" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: 0;height: auto;float: none;width: 100%;max-width: 186px" width="186">
                                        <div style="line-height:15px;font-size:1px">&#160;</div><!--[if mso]></td></tr></table><![endif]-->
                                    </div>


                                    <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="background-color:transparent;">
                <div style="Margin: 0 auto;min-width: 320px;max-width: 620px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;" class="block-grid ">
                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                        <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 620px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->

                        <!--[if (mso)|(IE)]><td align="center" width="620" style=" width:620px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                        <div class="col num12" style="min-width: 320px;max-width: 620px;display: table-cell;vertical-align: top;">
                            <div style="background-color: transparent; width: 100% !important;">
                                <!--[if (!mso)&(!IE)]><!--><div style="border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;"><!--<![endif]-->


                                    <div class="">
                                        <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 30px;"><![endif]-->
                                        <div style="color:#71777D;font-family:\'Lato\', Tahoma, Verdana, Segoe, sans-serif;line-height:150%; padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 30px;">
                                            <div style="font-size:12px;line-height:18px;font-family:Lato, Tahoma, Verdana, Segoe, sans-serif;color:#71777D;text-align:left;">
                                                '.$content['body'].'
                                            </div>
                                        </div>
                                        <!--[if mso]></td></tr></table><![endif]-->
                                    </div>

                                    <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                            </div>
                        </div>
                        <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                    </div>
                </div>
            </div>
      
            <div style="background-color:transparent;">
                <div style="Margin: 0 auto;min-width: 320px;max-width: 620px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;" class="block-grid ">
                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                        <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 620px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->

                        <!--[if (mso)|(IE)]><td align="center" width="620" style=" width:620px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                        <div class="col num12" style="min-width: 320px;max-width: 620px;display: table-cell;vertical-align: top;">
                            <div style="background-color: transparent; width: 100% !important;">
                                <!--[if (!mso)&(!IE)]><!--><div style="border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;"><!--<![endif]-->



                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="divider " style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 100%;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                        <tbody>
                                        <tr style="vertical-align: top">
                                            <td class="divider_inner" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;padding-right: 10px;padding-left: 10px;padding-top: 20px;padding-bottom: 20px;min-width: 100%;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                <table class="divider_content" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 1px dotted #CCCCCC;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                    <tbody>
                                                    <tr style="vertical-align: top">
                                                        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                            <span></span>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                            </div>
                        </div>
                        <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                    </div>
                </div>
            </div>';
            if(isset($content['sub_footer'])){
                $mail->Body .= '<div style="background-color:transparent;">
                                <div style="Margin: 0 auto;min-width: 320px;max-width: 620px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;" class="block-grid four-up ">
                                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                                        <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 620px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->

                                        <!--[if (mso)|(IE)]><td align="center" width="155" style=" width:155px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->';
                    
                                foreach($content['sub_footer'] as $key => $value){
                                    $mail->Body .= '<div class="col num3" style="max-width: 320px;min-width: 155px;display: table-cell;vertical-align: top;">
                                                    <div style="background-color: transparent; width: 100% !important;">
                                                        <!--[if (!mso)&(!IE)]><!--><div style="border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;"><!--<![endif]-->


                                                            <div class="">
                                                                <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 5px; padding-left: 5px; padding-top: 5px; padding-bottom: 5px;"><![endif]-->
                                                                <div style="color:#000000;font-family:\'Lato\', Tahoma, Verdana, Segoe, sans-serif;line-height:120%; padding-right: 5px; padding-left: 5px; padding-top: 5px; padding-bottom: 5px;">
                                                                    <div style="font-size:12px;line-height:14px;font-family:Lato, Tahoma, Verdana, Segoe, sans-serif;color:#000000;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center"><span style="font-size: 12px; line-height: 14px;"><a style="color:#000000;text-decoration: none" href="'.$value.'" target="_blank">'.$key.' </a></span></p></div>
                                                                </div>
                                                                <!--[if mso]></td></tr></table><![endif]-->
                                                            </div>

                                                            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                                                    </div>
                                                </div>';
                                }
                            
                $mail->Body .= '</div>
                                </div>
                            </div>';
            }
            
            $mail->Body .= '<div style="background-color:transparent;">
                <div style="Margin: 0 auto;min-width: 320px;max-width: 620px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;" class="block-grid ">
                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                        <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 620px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->

                        <!--[if (mso)|(IE)]><td align="center" width="620" style=" width:620px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                        <div class="col num12" style="min-width: 320px;max-width: 620px;display: table-cell;vertical-align: top;">
                            <div style="background-color: transparent; width: 100% !important;">
                                <!--[if (!mso)&(!IE)]><!--><div style="border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;"><!--<![endif]-->



                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="divider " style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 100%;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                        <tbody>
                                        <tr style="vertical-align: top">
                                            <td class="divider_inner" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;padding-right: 10px;padding-left: 10px;padding-top: 20px;padding-bottom: 20px;min-width: 100%;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                <table class="divider_content" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 1px dotted #CCCCCC;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                    <tbody>
                                                    <tr style="vertical-align: top">
                                                        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                            <span></span>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                            </div>
                        </div>
                        <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                    </div>
                </div>
            </div>
            <div style="background-color:transparent;">
                <div style="Margin: 0 auto;min-width: 320px;max-width: 620px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;" class="block-grid ">
                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                        <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 620px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->

                        <!--[if (mso)|(IE)]><td align="center" width="620" style=" width:620px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                        <div class="col num12" style="min-width: 320px;max-width: 620px;display: table-cell;vertical-align: top;">
                            <div style="background-color: transparent; width: 100% !important;">
                                <!--[if (!mso)&(!IE)]><!--><div style="border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;"><!--<![endif]-->


                                    <div class="">
                                        <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
                                        <div style="color:#555555;font-family:\'Lato\', Tahoma, Verdana, Segoe, sans-serif;line-height:120%; padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
                                            <div style="font-size:12px;line-height:14px;font-family:Lato, Tahoma, Verdana, Segoe, sans-serif;color:#555555;text-align:left;">
                                                '.$content['footer'].'
                                                <br />
                                                <p>Copyright &copy; '. date("Y").' '.SITENAME.'. All rights reserved. </p>
                                            </div>
                                        </div>
                                        <!--[if mso]></td></tr></table><![endif]-->
                                    </div>

                                    <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                            </div>
                        </div>
                        <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                    </div>
                </div>
            </div>
            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
        </td>
    </tr>
    </tbody>
</table>
<!--[if (mso)|(IE)]></div><![endif]-->


</body></html>';
    
// check mail    
if(!$mail->send()){
    throw new Exception($mail->ErrorInfo);
}
else{
    return TRUE;
}
}


function formatDate($date, $type){
    if($type == 'full'){
        return date_format(date_create($date), 'l jS F, Y, h:i a');
    }
    else if($type == 'full abb'){
        return date_format(date_create($date), 'D jS F, Y, h:i a');
    }
    else if($type == 'date'){
        return date_format(date_create($date), 'l jS F, Y');
    }
    else if($type == 'time'){
        return date_format(date_create($date), 'h:i a');
    }
}

function now($format = TRUE){
    if($format == TRUE){
        return date("Y-m-d H:i:s");  
    }
    else{
        return date("ymdHis");  
    }
}

function token(){
    return sha1(date('h11iys11m'));
}

// redirect function
function redir($page = NULL){
    // check for redirect link
    if(isset($_GET['redir_url'])){
        header("Location: ".BASE_URL.urldecode($_GET['redir_url']));
    }
    else{
        // check included ext
        header("Location: ".BASE_URL.$page);
    }
    exit;
}


// autoload functions
function autoLoad($path, $exception = NULL){
    // get dir files
    $filedir = scandir($path);

    // loop through dir and include files
    for($i=0; $i<=count($filedir)-1; $i++){
        // skip values without php extension
        $file_ext = pathinfo($filedir[$i], PATHINFO_EXTENSION);
        if($file_ext != "php"){
            continue;
        }

        // skip exception file
        if($filedir[$i] == $exception){
            continue;
        }
        include_once $path.'/'.$filedir[$i]; 
    }
}


// validate empty fields
function isempty($data, $msgs = array()){
    $errors = array();
    $i = 0;
    
    foreach($data as $value){
        if($value == NULL){
            if(!empty($msgs)){
                if(isset($msgs[$i])){
                    $errors[] = $msgs[$i];   
                }
            }
            else{
                $errors = array(1);   
            }
        }
        
        $i++;
    }
    
    return $errors;
}

function pre_var($field_data, $data = NULL){
    if($_POST){
        if(isset($_POST[$field_data])){
            return $_POST[$field_data];
        }
        else{
            if(!empty($data)){
                return $data;
            }
            else{
                return NULL;   
            }
        }
    }
    else{
        if(!empty($data)){
            return $data;
        }
        else{
            return NULL;   
        }
    }
}

function Feed($feed){
    $_SESSION['haptic'] = $feed;
}

// html feed init
function htmlFeed($status, $text, $text_pos = NULL){
    if($status == 'error'){
        $_SESSION['haptic'] = '<div class="alert alert-danger '.$text_pos.'">'.$text.'</div>';
    }
    else if($status == 'success'){
        $_SESSION['haptic'] = '<div class="alert alert-success '.$text_pos.'">'.$text.'</div>';
    }
    else if($status == 'warning'){
        $_SESSION['haptic'] = '<div class="alert alert-warning '.$text_pos.'">'.$text.'</div>';
    }
}


// html default feed
function htmlOut($status, $text, $text_pos = NULL){
    if($status == 'error'){
        print '<div class="alert alert-danger '.$text_pos.'">'.$text.'</div>';
    }
    else if($status == 'success'){
        print '<div class="alert alert-success '.$text_pos.'">'.$text.'</div>';
    }
    else if($status == 'warning'){
        print '<div class="alert alert-warning '.$text_pos.'">'.$text.'</div>';
    }
}

function load($path){
    // check included ext
    if(strpos($path, ".php") !== false){

    }
    else{
        $path = $path.".php";
    }
    
    include_once "$path";
}

function createStr_array($string){
    $array = explode(',', $string);
    
    return $array;
}

/*
var type value = db,model,helper
*/
function inst($class,$type){
    if($type == 'db'){
        $path = "System/Engine/model/$class.php";
    }
    else if($type == 'model'){
        $path = "model/$class.php";
    }
    else if($type == 'helper'){
        $path = "System/Engine/helpers/$class.php";
    }
    else{
        $path = NULL;
    }
    
    // include file
    if($path != NULL){
        include_once "$path";
    }
    else{
        throw new Exception("Invalid instatiation type");
    }
    $obj = new $class;
    return $obj;
}

function create_url($data){
    $string = html_entity_decode($data);
    $string = strtolower($data);
    $string = str_replace("/", "_", $string);
    $string = str_replace("|", "_", $string);
    $string = str_replace("\\", "_", $string);
    $string = str_replace(" ", "_", $string);
    $string = str_replace("__","_",$string);
    $string = str_replace("___","_",$string);
    $string = str_replace(";",NULL,$string);
    $string = str_replace(">",NULL,$string);
    $string = str_replace("<",NULL,$string);
    $string = str_replace("}",NULL,$string);
    $string = str_replace("{",NULL,$string);
    $string = str_replace("]",NULL,$string);
    $string = str_replace("[",NULL,$string);
    $string = str_replace(")",NULL,$string);
    $string = str_replace("(",NULL,$string);
    $string = str_replace("'",NULL,$string);
    $string = str_replace('"',NULL,$string);
    $string = str_replace(";",NULL,$string);
    $string = str_replace(":",NULL,$string);
    $string = str_replace("?",NULL,$string);
    $string = str_replace("!",NULL,$string);
    $string = str_replace("`",NULL,$string);
    $string = str_replace("%",NULL,$string);
    $string = str_replace("#",NULL,$string);
    $string = str_replace("^",NULL,$string);
    $string = str_replace("@",NULL,$string);
    $string = str_replace("-",NULL,$string);
    $string = str_replace("+",NULL,$string);
    $string = str_replace("=",NULL,$string);
    $string = str_replace(",",NULL,$string);
    $string = str_replace(".",NULL,$string);
    $string = str_replace("~",NULL,$string);
    $string = str_replace("*",NULL,$string);
    $string = str_replace("&",NULL,$string);
    $string = str_replace("$",NULL,$string);
    
    return $string.now(FALSE);
}

function sanitize_name($data){
    $string = html_entity_decode($data);
    $string = str_replace("/", NULL, $string);
    $string = str_replace("|", NULL, $string);
    $string = str_replace("\\", NULL, $string);
    $string = str_replace("__",NULL,$string);
    $string = str_replace("_",NULL,$string);
    $string = str_replace("___",NULL,$string);
    $string = str_replace(";",NULL,$string);
    $string = str_replace(">",NULL,$string);
    $string = str_replace("<",NULL,$string);
    $string = str_replace("}",NULL,$string);
    $string = str_replace("{",NULL,$string);
    $string = str_replace("]",NULL,$string);
    $string = str_replace("[",NULL,$string);
    $string = str_replace(")",NULL,$string);
    $string = str_replace("(",NULL,$string);
    $string = str_replace("'",NULL,$string);
    $string = str_replace('"',NULL,$string);
    $string = str_replace(";",NULL,$string);
    $string = str_replace(":",NULL,$string);
    $string = str_replace("?",NULL,$string);
    $string = str_replace("!",NULL,$string);
    $string = str_replace("`",NULL,$string);
    $string = str_replace("%",NULL,$string);
    $string = str_replace("#",NULL,$string);
    $string = str_replace("^",NULL,$string);
    $string = str_replace("@",NULL,$string);
    $string = str_replace("-",NULL,$string);
    $string = str_replace("+",NULL,$string);
    $string = str_replace("=",NULL,$string);
    $string = str_replace(",",NULL,$string);
    $string = str_replace(".",NULL,$string);
    $string = str_replace("~",NULL,$string);
    $string = str_replace("*",NULL,$string);
    $string = str_replace("&",NULL,$string);
    $string = str_replace("$",NULL,$string);
}

function firstChar($string){
    // remove any white space from string
    $string = str_replace(' ',NULL,$string);
    if(!empty($string)){
        $char = str_split($string);
    }
    else{
        $char = NULL;
    }
    
    return strtoupper($char[0]);
}

function alphaCol($letter){
    $letter = strtoupper($letter);
    $alpha = array(
        'A' => '#E98A15',
        'B' => '#59114D',
        'C' => '#329F5B',
        'D' => '#23C9FF',
        'E' => '#52050A',
        'F' => '#832161',
        'G' => '#9F2042',
        'H' => '#211103',
        'I' => '#DF57BC',
        'J' => '#FF6B6B',
        'K' => '#4ECDC4',
        'L' => '#C08552',
        'M' => '#59FFA0',
        'N' => '#50514F',
        'O' => '#6D454C',
        'P' => '#E63946',
        'Q' => '#1D3557',
        'R' => '#F4743B',
        'S' => '#BEEE62',
        'T' => '#70AE6E',
        'U' => '#1E555C',
        'V' => '#F15152',
        'W' => '#E83151',
        'X' => '#387780',
        'Y' => '#D2CCA1',
        'Z' => '#DBD4D3'
    );
    
    return $alpha[$letter];
}

function elipsen($string, $length){
    // remove any white space from string
    if(!empty($string)){
        $sen = NULL;
        $char = str_split($string);
        if($length < count($char)){
            // loop through string
            for($i=0;$i<=$length-1;$i++){
                $sen .= $char[$i];
            }

            $sen = $sen.'...';   
        }
        else{
            $sen = $string;
        }
    }
    else{
        $sen = NULL;
    }
    
    return $sen;
}

function uncreate_url($data){
    $string = str_replace("_", " ", $data);
    
    return $string;
}

function base_url($path = NULL, $alt_path = NULL){
    if(!empty($alt_path)){
        if(!empty(ALT_BASE_URL)){
            print ALT_BASE_URL."$alt_path";   
        }
        else{
            print 'ALT_BASE_URL is NULL';
        }
    }
    else{
        if($path !== NULL){
            print BASE_URL."$path";
        }
        else{
            print BASE_URL;
        }   
    }
}

// open form
function formOpen($method,$action,$enctype=FALSE){
    $enctype = ($enctype == TRUE) ? "enctype = '$enctype'" : NULL;
    print "<form method='$method' action='$action' $enctype>";
}

function formClose($name,$text,$container_class=NULL){
    // check for class name in button name
    if(strpos($name,":") !== FALSE){
        $btn_name = explode(":",$name);
        $btn_class = 'class="'.$btn_name[1].'"';
        $btn_name = $btn_name[0];
    }
    else{
        $btn_class = NULL;
        $btn_name = $name;
    }
    
    print ($container_class != NULL) ? '<div class="'.$container_class.'">' : NULL;
        // print form button
        print '<button type="submit" name="'.$btn_name.'" '.$btn_class.'>'.$text.'</button>';
    print ($container_class != NULL) ? '</div>' : NULL;
    // print closing form tag
    print '</form>';
}

// call sessions
function call_sess($sess_name,$array_val = NULL){
    if(isset($_SESSION[$sess_name])){
           if($array_val !== NULL){
                return $_SESSION[$sess_name][$array_val];   
            }
            else{
                return $_SESSION[$sess_name];
            }   
    }
    else{
        return NULL;
    }
}