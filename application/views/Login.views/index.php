<div class="overlay">
    <div class="aligned-box">
        <div class="box">
            <?php print form_open('login/tryout'); ?>
                <input class="textfield" name="username" type="text" placeholder="Username">
                <input class="textfield" name="password" type="password" placeholder="Password">
                <br><br>
                <input class="button" type="submit" value="Login">
            <?php print form_close(); ?>
        </div>
        <span class="cpytext">© 2019 Hendrik Weiler</span>
    </div>
</div>