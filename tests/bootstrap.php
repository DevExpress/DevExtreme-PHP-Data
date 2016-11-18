<?php
chdir(__DIR__);
require_once("../DevExtreme/LoadHelper.php");
spl_autoload_register(array("DevExtreme\LoadHelper", "LoadModule"));