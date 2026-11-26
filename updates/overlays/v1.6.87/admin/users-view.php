<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$labels = site_account_labels();
$users = site_accounts();
$search = site_account_input($_GET, 'q');
$role = site_account_input($_GET, 'role');
$status = site_account_input($_GET, 'status');
$selectedUser = $users[site_account_input($_GET, 'user')] ?? [];
$filtered = array_filter($users, static function (array $user) use ($search,$role,$status): bool {
    return ($role === '' || in_array($role, explode(',', $user['roles']), true))
        && ($status === '' || $user['enabled'] === ($status === 'active'))
        && ($search === '' || preg_match('~' . preg_quote($search, '~') . '~iu', $user['name'] . ' ' . $user['email'] . ' ' . $user['phone']));
});
$pageNumber = max(1, (int) site_account_input($_GET, 'p'));
?>
<h1><?= site_escape($labels['users']) ?></h1>
<form method="get" class="header-actions">
    <input type="hidden" name="users" value="1">
    <label><?= site_escape($labels['search']) ?> <input name="q" type="search" value="<?= site_escape($search) ?>"></label>
    <label><?= site_escape($labels['role']) ?> <select name="role"><option value=""><?= site_escape($labels['all']) ?></option>
        <?php foreach (SITE_ACCOUNT_ROLES as $key): ?><option value="<?= $key ?>"<?= $role === $key ? ' selected' : '' ?>><?= site_escape($labels[$key]) ?></option><?php endforeach; ?>
    </select></label>
    <select name="status" aria-label="<?= admin_l('Статус') ?>"><option value=""><?= site_escape($labels['all']) ?></option>
        <?php foreach (['active','blocked'] as $key): ?><option value="<?= $key ?>"<?= $status === $key ? ' selected' : '' ?>><?= site_escape($labels[$key]) ?></option><?php endforeach; ?>
    </select><button><?= site_escape($labels['search']) ?></button>
</form>
<ul class="panel">
    <?php foreach (array_slice($filtered, ($pageNumber - 1) * 40, 40) as $user): ?>
        <li><a href="<?= site_escape($usersUrl . '&user=' . $user['id']) ?>"><?= site_escape($user['name'] . ' · ' . $user['email']) ?></a>
            · <?= site_escape($labels[$user['enabled'] ? 'active' : 'blocked']) ?></li>
    <?php endforeach; ?>
</ul>
<?php if (count($filtered) > 40): ?>
<nav class="scope-menu">
    <?php for ($number = 1, $last = (int) ceil(count($filtered) / 40); $number <= $last; $number++): ?>
        <a href="<?= site_escape($usersUrl . '&' . http_build_query(['q'=>$search,'role'=>$role,'status'=>$status,'p'=>$number])) ?>"<?= $number === $pageNumber ? ' aria-current="page"' : '' ?>><?= $number ?></a>
    <?php endfor; ?>
</nav>
<?php endif; ?>
<form class="panel form-stack" method="post" action="<?= site_escape($usersUrl . (isset($selectedUser['id']) ? '&user=' . $selectedUser['id'] : '')) ?>">
    <h2><?= site_escape($selectedUser['name'] ?? $labels['new']) ?></h2>
    <input type="hidden" name="action" value="account-save">
    <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
    <input type="hidden" name="id" value="<?= site_escape($selectedUser['id'] ?? '') ?>">
    <?php foreach (['name'=>'text','email'=>'email','phone'=>'tel'] as $key => $type): ?>
        <label class="field"><?= site_escape($labels[$key]) ?><input type="<?= $type ?>" name="<?= $key ?>" <?= $key === 'phone' ? '' : 'required' ?> value="<?= site_escape($selectedUser[$key] ?? '') ?>"></label>
    <?php endforeach; ?>
    <label class="field"><?= site_escape($labels['address']) ?><textarea name="addresses"><?= site_escape($selectedUser['addresses'] ?? '') ?></textarea></label>
    <label class="field"><?= site_escape($labels['password']) ?><input type="password" name="password" autocomplete="new-password" <?= $selectedUser ? '' : 'required' ?> minlength="10" maxlength="72">
        <?php if ($selectedUser): ?><small><?= site_escape($labels['keep_password']) ?></small><?php endif; ?>
    </label>
    <fieldset><legend><?= site_escape($labels['role']) ?></legend>
        <?php foreach (SITE_ACCOUNT_ROLES as $key): ?>
            <label class="choice"><input type="checkbox" name="roles[]" value="<?= $key ?>"<?= in_array($key, explode(',', $selectedUser['roles'] ?? 'buyer'), true) ? ' checked' : '' ?>> <?= site_escape($labels[$key]) ?></label>
        <?php endforeach; ?>
    </fieldset>
    <label class="choice"><input type="checkbox" name="enabled" value="1"<?= ($selectedUser['enabled'] ?? true) ? ' checked' : '' ?>> <?= site_escape($labels['active']) ?></label>
    <div><button class="button-primary"><?= site_escape($labels['save']) ?></button></div>
</form>
