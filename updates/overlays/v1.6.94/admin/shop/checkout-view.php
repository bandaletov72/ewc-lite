<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$label = static function (string $key): string { return site_escape(shop_label($key)); };
$forms = site_forms();
$chosen = $shopState['checkoutForm'] ?? '';
$delivery = shop_delivery_rules($shopState);
?>
<form class="form-stack" method="post" action="<?= site_escape($tabUrl) ?>">
    <input type="hidden" name="action" value="shop-checkout-settings">
    <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
    <section class="panel form-stack">
        <h2><i class="fa-regular fa-user" aria-hidden="true"></i> <?= $label('checkout_contact') ?></h2>
        <label class="field"><?= $label('checkout_select_form') ?><select name="checkout_form">
            <option value=""><?= $label('checkout_default_form') ?></option>
            <?php foreach ($forms as $id => $form): ?>
                <option value="<?= site_escape($id) ?>"<?= $chosen === $id ? ' selected' : '' ?>><?= site_escape(admin_form_name($id, $form, $adminLanguage) . ' · ' . $id) ?></option>
            <?php endforeach; ?>
        </select></label>
        <div class="header-actions">
            <?php if (isset($forms[$chosen])): ?><a href="<?= site_escape($formsUrl . '&id=' . rawurlencode($chosen)) ?>"><?= $label('record_edit') ?></a><?php endif; ?>
            <a href="<?= site_escape($formsUrl . '&id=new') ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i> <?= $label('checkout_new_form') ?></a>
        </div>
        <p class="hint"><?= $label('checkout_form_help') ?></p>
    </section>
    <section class="panel form-stack">
        <h2><i class="fa-solid fa-truck" aria-hidden="true"></i> <?= $label('delivery_rules') ?></h2>
        <div class="shop-delivery-grid">
            <fieldset class="shop-settings-group form-stack">
                <legend><label class="choice"><input type="checkbox" name="shipping_enabled" value="1"<?= $delivery['shipping'] ? ' checked' : '' ?>> <?= $label('shipping') ?></label></legend>
                <div class="shop-delivery-amounts">
            <?php foreach (['shipping_fee'=>['delivery_fee',$shopState['shipping']], 'shipping_minimum'=>['delivery_minimum',$delivery['minimum']], 'shipping_free'=>['delivery_free',$delivery['freeFrom']]] as $name => [$title,$minor]): ?>
                <label class="field"><?= $label($title) ?>
                    <span class="shop-delivery-amount">
                        <input type="text" inputmode="decimal" name="<?= $name ?>" maxlength="16" aria-describedby="shop-delivery-currency" value="<?= site_escape(shop_price_input((int) $minor)) ?>">
                        <span<?= $name === 'shipping_fee' ? ' id="shop-delivery-currency"' : ' aria-hidden="true"' ?>><?= site_escape($shopState['currency'] === 'RUB' ? '₽' : $shopState['currency']) ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
                </div>
                <p class="hint"><?= $label('delivery_threshold_help') ?></p>
            </fieldset>
            <fieldset class="shop-settings-group form-stack">
                <legend><label class="choice"><input type="checkbox" name="pickup" value="1"<?= $delivery['pickup'] ? ' checked' : '' ?>> <?= $label('pickup') ?></label></legend>
                <label class="field"><?= $label('delivery_pickup_address') ?><input name="pickup_address" maxlength="500" value="<?= site_escape($delivery['pickupAddress']) ?>"></label>
            </fieldset>
        </div>
        <label class="field"><?= $label('delivery_rules_text') ?><textarea name="delivery_notes" rows="3" maxlength="4000"><?= "\n" . site_escape($delivery['notes']) ?></textarea></label>
        <div><button type="submit" class="button-primary"><?= $label('save') ?></button></div>
    </section>
</form>
