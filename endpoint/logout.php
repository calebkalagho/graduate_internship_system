<?php
session_start();

session_unset();
session_destroy();

header("Location: http://localhost/graduate_internship_system/");
