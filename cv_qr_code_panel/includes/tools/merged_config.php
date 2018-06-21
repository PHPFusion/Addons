<?php
/*
 * PHP QR Code encoder
 *
 * Config file, tuned-up for merged verion
 */

define('QR_CACHEABLE', FALSE);       // use cache - more disk reads but less CPU power, masks and format templates are stored there
define('QR_CACHE_DIR', FALSE);       // used when QR_CACHEABLE === true
define('QR_LOG_DIR', FALSE);         // default error logs dir

define('QR_FIND_BEST_MASK', TRUE);                                                          // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
define('QR_FIND_FROM_RANDOM', 2);                                                       // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
define('QR_DEFAULT_MASK', 2);                                                               // when QR_FIND_BEST_MASK === false

define('QR_PNG_MAXIMUM_SIZE', 1024);                                                       // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images
