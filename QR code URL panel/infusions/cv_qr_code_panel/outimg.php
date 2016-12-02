<?php
/**
 * Created by PhpStorm.
 * User: rizado
 * Date: 02.12.16
 * Time: 3:11 PM
 */

require_once "../../maincore.php";
require_once __DIR__ . "/includes/qrlib.php";

$target = isset($_GET['target']) ? base64_decode($_GET['target']) : $settings['siteurl'];

QRcode::png($target, false, "L", 2, 1, false, 0xffffff, 0x601117);