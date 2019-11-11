<div class="overlay">
    <div class="aligned-box">
        <div class="box">
            <?php if($data['success']): ?>
                <h4>Login successful</h4>
                <p>You will be redirect in a few seconds...</p>
                <meta http-equiv="refresh" content="2; URL=/admin/files">
            <?php else: ?>
                <h4>Login failed</h4>
                <p>The username or password is invalid...</p>
                <meta http-equiv="refresh" content="2; URL=/login">
            <?php endif; ?>
        </div>
        <span class="cpytext">© 2019 Hendrik Weiler</span>
    </div>
</div>