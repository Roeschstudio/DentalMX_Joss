<div class="ds-alert ds-alert--danger" role="alert">
    <h4 class="ds-alert__heading">
        ⚠️ Errores de Validación
    </h4>
    <p>Por favor, corrige los siguientes errores:</p>
    <hr>
    <ul class="mb-0">
        <?php foreach ($validation->getErrors() as $field => $errors): ?>
            <?php if (is_array($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li><?= esc($errors) ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
