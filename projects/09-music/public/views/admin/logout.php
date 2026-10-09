<?php
// Destroy session and redirect to login
session_destroy();
header('Location: index.php?page=login');
exit;
