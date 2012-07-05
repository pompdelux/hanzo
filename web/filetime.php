<?php
error_log(print_r($_POST));

die(json_encode(array('status' => 'true'), JSON_FORCE_OBJECT));
