<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <base href="/">
    <link rel="stylesheet" href="css/app.css">
    <title><?php print $data['title'] ?></title>
</head>
<body>
    <?php $this->load->view($view,$data); ?>
</body>
</html>
