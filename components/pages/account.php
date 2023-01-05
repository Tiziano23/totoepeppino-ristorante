<?php

namespace Steampixel;

$update_msg = $this->prop('update_msg', [
    'type' => 'string',
    'required' => false
]);
$update_error = $this->prop('update_error', [
    'type' => 'string',
    'required' => false
]);
$register_msg = $this->prop('register_msg', [
    'type' => 'string',
    'required' => false
]);
$register_error = $this->prop('register_error', [
    'type' => 'string',
    'required' => false
]);

$users = get_all_users_data();
?>

<div class="container vstack justify-content-center gap-5 my-5">
    <div>
        <h2 class="mb-3">Cambio password</h2>
        <form action="/account/update" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <!-- <input type="text" class="form-control" id="username" name="username"> -->
                <select class="form-select" name="username" id="username" data-model="user" data-key="username" required>
                    <option value="" selected disabled>Scegli una utente...</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="old_password" class="form-label">Vecchia password</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Nuova password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Cambia password</button>

            <?php if (isset($update_error)) : ?>
                <p class="text-danger p-2 my-4 bg-danger-subtle border border-danger-subtle rounded-3"><?= $update_error ?></p>
            <?php endif; ?>
            <?php if (isset($update_msg)) : ?>
                <p class="text-success p-2 my-4 bg-success-subtle border border-success-subtle rounded-3"><?= $update_msg ?></p>
            <?php endif; ?>
        </form>
    </div>
    <div>
        <h2 class="mb-3">Registrazione nuovo utente</h2>
        <form action="/account/register" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Registra nuovo utente</button>

            <?php if (isset($register_error)) : ?>
                <p class="text-danger p-2 my-4 bg-danger-subtle border border-danger-subtle rounded-3"><?= $register_error ?></p>
            <?php endif; ?>
            <?php if (isset($register_msg)) : ?>
                <p class="text-success p-2 my-4 bg-success-subtle border border-success-subtle rounded-3"><?= $register_msg ?></p>
            <?php endif; ?>
        </form>
    </div>
    <div>
        <h2 class="mb-3">Gestione utenti</h2>
        <table class="table table-hover align-middle" id="users-table">
            <colgroup>
                <col class="col-11">
                <col class="col-1">
            </colgroup>
            <thead>
                <tr>
                    <th>Username</th>
                    <th></th>
                </tr>
            </thead>
            <tr class="user">
              <td>admin</td>
              <td></td>
            </tr>
            <?php foreach ($users as $user) : ?>
              <?php if($user['username'] != 'admin') : ?>
                <tr class="user" data-uid="<?= $user['id'] ?>">
                    <td><?= $user['username'] ?></td>
                    <td class="text-center"><button type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i></button></td>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<script src="/js/account.js"></script>