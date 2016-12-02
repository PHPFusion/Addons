<?php
/**
 * Created by PhpStorm.
 * User: rizado
 * Date: 02.12.16
 * Time: 3:11 PM
 */

require_once "../../maincore.php";
require_once __DIR__ . "/includes/qrlib.php";
require_once INCLUDES . "infusions_include.php";

$target = isset($_GET['target']) ? base64_decode($_GET['target']) : $settings['siteurl'];

$opts = get_settings("cv_qr_code_panel");

QRcode::png($target, false, $opts['corr_level'], $opts['square_size'], $opts['margin_size'], false, hexdec(substr($opts['bg_color'], 1)), hexdec(substr($opts['main_color'], 1)));