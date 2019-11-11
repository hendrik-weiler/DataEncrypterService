<h5>Your profile</h5>
<?php print form_open('/admin/profile/save'); ?>
<input class="textfield" type="password" name="password_old" placeholder="Current password">
<input class="textfield" type="password" name="password" placeholder="Password">
<input class="textfield" type="password" name="password_repeat" placeholder="Repeat password">
<br><br>
<div>
<input style="display: inline-block" class="button" type="submit" value="Save">
    or
    <a href="/admin/logout">Logout</a>
</div>
<?php print form_close(); ?>
<?php echo validation_errors('<div class="error">', '</div>'); ?>
<?php if(isset($_SESSION['error'])): ?>
    <div class="error">
        The current password was wrong.
    </div>
<?php endif; ?>
<?php if(isset($_SESSION['success'])): ?>
    <div class="success">
        The password was updated.
    </div>
<?php endif; ?>

