---------------------------------------------------------
PHP-FUSION 9 - INFUSION DEVELOPMENT KIT
---------------------------------------------------------

PHP-Fusion 9 has an add-in system called Infusions. An infusion provides
a one-click installation which requires absolutely no coding skills from the
end user.

Before you begin working on your infusion, first, think about what you want
to do. Will your infusion be a panel or a seperate page? Does it need it's
own admin panel? Does it require additional database tables? The infusion
system can accommodate all of the aformentioned options.

The crucial part is the name folder which will contain your infusion file.
There are two options, if your infusion is going to be panel based, then your
folder name must end with the phrase '_panel' (without quotes). You don't
need to do this if your infusion does not utilise a panel.

The one important file that must be present in any infusion is the installation
information file 'infusion.php'. This file is automatically detected by the
Infusion admin panel in PHP-Fusion's admin panel. You'll find a descriptive copy
of the infusion.php file in this development kit.

---------------------------------------------------------
INFUSION DEVELOPMENT KIT CONTENTS
---------------------------------------------------------
new_infusion.php        A stand alone page template
new_infusion_admin.php  An admin panel template
infusion.php            The installation information file
infusion_db.php         Database table definition file
