<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
session_start();
define('LIB_IMAGES_PATH','graphics/libimages/');
include('image_auth.php');
$capt = new captcha();
$capt->m_GenImage(5);
$capt->m_GetImage();
$capt->m_ShowImage();
?>