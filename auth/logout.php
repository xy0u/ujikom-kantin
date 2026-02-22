<?php
session_start();
require '../core/helpers.php';
session_destroy();
redirect('/auth/login.php');