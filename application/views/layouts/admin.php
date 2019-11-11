<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <base href="/">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/admin.css">
    <title><?php print $data['title'] ?></title>
</head>
<body>
    <div class="body">
        <div class="col-1">
            <ul class="menu">
                <li>
                    <a <?php print $data['mode']==1 ? 'class="active"' : '' ?> data-id="encrypt" href="/admin/files">
                        <img class="normal" src="images/files.png">
                        <img class="active" src="images/files-active.png">
                        <span>Files</span>
                    </a>
                </li>
                <li>
                    <a <?php print $data['mode']==2 ? 'class="active"' : '' ?> href="/admin/settings">
                        <img class="normal" src="images/settings.png">
                        <img class="active" src="images/settings-active.png">
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
            <a href="/admin/profile" class="user <?php print $data['mode']==3 ? 'active' : '' ?>">
                <img class="normal" src="images/user.png">
                <img class="active" src="images/user-active.png">
                <span><?php print $data['user']->username ?></span>
            </a>
        </div>
        <div class="col-2">

            <?php $this->load->view($view,$data); ?>

        </div>
    </div>
</body>
</html>
