<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: about.php
| Author: Frederick MC Chan (Chan)
| Co-Author: RobiNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once file_exists('maincore.php') ? 'maincore.php' : __DIR__."/../../maincore.php";
require_once THEMES.'templates/header.php';

$theme->set_display_mode('single');
$theme->right = FALSE;
$theme->show_menu = FALSE;
$theme->grid = 12;

opentable('About Shadowness Theme'); ?>
    <div class="text-center"><img src="<?php echo THEME; ?>images/logo.png" alt="Logo"></div>
    <p>
        Shadowness.com theme has been here since late year 2000, (2nd December 2000 to be exact).
        It started as a digital art showcase site, and became the one one of the first art online community before
        Facebook or Google even existed.
        Shadowness.com Theme is created in the memorial for Shadowness.com's support for the internet fan and graphic
        arts online community.
        Meng To, the founder of Shadowness, have been a source of inspiration for many, including myself. It is still
        one of the best looking site so far...
        I am one hundred percent sure that thousands of its current users will support me when I'm saying this.
    </p>
    <p>
        Why would one close this site down? I was mezmerized by this fact. Man, believe me when I say I have thought and
        puzzled for very long time,
        and still questioned by the founder's decision to close down his site... but rested on a conclusion, that
        perhaps he has his own personal reason.
        It must have been real tough to make such a big sacrifice. <i>(Who doesn't sacrifice? &hellip; Deja vu.)</i>
    </p>
    <p>
        At time of closing Shadowness has 446 community group count, 50,796 hi-res digital art pictures fueld by
        subscribers and artists members.
    </p>
    <blockquote class='m-b-20 p-10' style='background-color: rgba(255,255,255,0.05);'>
        "Shadowness.com is ranked #107191 in the world, a low rank means that this website gets lots of visitors. Its
        primary traffic from United States and is ranked #65,075 in United States. It has more than 1 subdomains with
        traffic. It has 628 visitors per day, and has 2.2 K pageviews per day."
    </blockquote>
    <p>
        By creating this theme, I wish to make my expression and show my final effort to ensure this beautiful
        masterpiece will not be lost,
        and to preserve legacy and memories for a dear friend. May his work last forever throughout all times.
    </p>
    <p>
        The theme's art and css were originally from Shadowness.com itself, and was ported into the latest PHP-Fusion
        version 9 standards to illustrate
        the capabilities of our latest operating system. Due to copyrights of the original authors, the
        unreleased <strong>PHP-Fusion Core 9</strong>..
    </p>
    <p>
        The Shadowness.com site was very unique because the table header changes its layout when different CSS classes
        wrapper are used - full, single and topic.
        To tackle this situation, I coded a flexible controller class for the different purpose to re-instanced table
        headers to adapt to
        Shadowness design, concept and visualization required. Finally, Shadowness.com is fully ported into PHP-Fusion
        49 hours 33 minutes
        because the robusity of PHP-Fusion 9.00 Core. It would be by all means a lot more harder with next closest
        publishing platform.. not that I would bother to try.
    </p>
    <p>
        <strong>About the author:</strong> Frederick MC Chan (Chan) is the lead developer, application and sofware
        engineer and theme designer for PHP-Fusion Open Source Content Management System.
    </p>
    <p>
        <strong>Copyright Notice:</strong> This showcase is brought to you by PHP-Fusion Inc. The original art,
        combination, design and work belongs to Shadowness.com and its owner.
    </p>
<?php
$render_time = substr((microtime(TRUE) - START_TIME), 0, 7);
?>
    <p>
        <small>
            This message was brought to you in under <?php echo $render_time; ?> micro second by the fastest web
            operating system ever created.
        </small>
    </p>
<?php closetable();
require_once THEMES.'templates/footer.php';
