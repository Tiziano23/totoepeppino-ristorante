<?php

namespace Steampixel;

$error = $this->prop('error', [
    'type' => 'string',
    'required' => false
]);
?>

<div class="login-group container vstack justify-content-center gap-3">
    <img src="/images/logo.png" class="logo mx-auto d-block" alt="Logo">
    <form action="/login" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <?php if (isset($error)) : ?>
            <p class="text-danger p-2 my-2 bg-danger-subtle border border-danger-subtle rounded-3"><?= $error ?></p>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>