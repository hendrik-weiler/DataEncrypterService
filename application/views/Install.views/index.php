<div class="overlay">
    <div class="aligned-box">
        <div class="box">
            <h4>Install</h4>
            <?php if(!$data['folderIsWriteable']): ?>
                <div class="error">
                    The folder "<?php print($data['folderPath']); ?>" needs to be writeable.
                </div>
            <?php else: ?>
                <p>Fill in your admin username and password to get started.</p>
                <?php print form_open('/install/execute'); ?>
                <input class="textfield" type="text" name="username" value="<?php echo set_value('username'); ?>" placeholder="Username">
                <input class="textfield" type="password" name="password" placeholder="Password">
                <input class="textfield" type="password" name="password_repeat" placeholder="Repeat password">
                <br><br>
                <?php if($data['sqliteEnabled']): ?>
                <input class="button" type="submit" value="Install">
                <?php else: ?>
                    <div class="error">SQLITE extension needs to be enabled.</div>
                <?php endif; ?>
                <?php print form_close(); ?>
                <?php echo validation_errors('<div class="error">', '</div>'); ?>
            <?php endif; ?>
        </div>
        <span class="cpytext">© 2019 Hendrik Weiler</span>
    </div>
</div>