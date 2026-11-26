<?php
$launcherRows = $object->bot_configuration_array['launcher_links'] ?? [];
$launcherRows = is_array($launcherRows) ? array_values($launcherRows) : [];
$infoUrl = $object->bot_configuration_array['launcher_info_url'] ?? '';
$tr = erTranslationClassLhTranslation::getInstance();
?>
<details class="mb-3" ng-non-bindable<?php if (!empty($errors) && isset($_POST['LauncherLinksPresent'])) : ?> open<?php endif; ?>>
    <summary><?php echo $tr->getTranslation('abstract/widgettheme', 'Contact links in the status widget')?></summary>
    <p class="form-text"><?php echo $tr->getTranslation('abstract/widgettheme', 'Add links to offer visitors a choice alongside live chat. With no links the status widget opens chat directly.')?></p>
    <input type="hidden" name="LauncherLinksPresent" value="1">
    <div id="launcher-links">
        <?php foreach ($launcherRows ?: [[]] as $index => $row) : ?>
            <div class="row align-items-end mb-2">
                <?php foreach (['label' => $tr->getTranslation('abstract/widgettheme', 'Label'), 'url' => $tr->getTranslation('abstract/widgettheme', 'HTTPS URL'), 'icon' => $tr->getTranslation('abstract/widgettheme', 'Icon URL (optional)')] as $key => $caption) : ?>
                    <label class="<?php echo $key === 'label' ? 'col-md-2' : 'col-md-3'?>"><?php echo $caption?>
                        <input class="form-control form-control-sm" type="<?php echo $key === 'label' ? 'text' : 'url'?>" name="LauncherLinks[<?php echo $index?>][<?php echo $key?>]" data-field="<?php echo $key?>" value="<?php echo htmlspecialchars(is_array($row) && is_string($row[$key] ?? null) ? $row[$key] : '', ENT_QUOTES)?>">
                    </label>
                <?php endforeach; ?>
                <label class="col-md-2"><?php echo $tr->getTranslation('abstract/widgettheme', 'Show link')?>
                    <select class="form-select form-select-sm" name="LauncherLinks[<?php echo $index?>][visibility]" data-field="visibility">
                        <?php foreach (['always' => $tr->getTranslation('abstract/widgettheme', 'Always'), 'online' => $tr->getTranslation('abstract/widgettheme', 'When online'), 'offline' => $tr->getTranslation('abstract/widgettheme', 'When offline')] as $value => $caption) : ?>
                            <option value="<?php echo $value?>"<?php if ($value === ($row['visibility'] ?? 'always')) : ?> selected<?php endif; ?>><?php echo $caption?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div class="col-md-auto"><button type="button" class="btn btn-sm btn-outline-secondary" data-remove><?php echo $tr->getTranslation('abstract/widgettheme', 'Remove link')?></button></div>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" id="add-launcher-link" class="btn btn-sm btn-secondary"><?php echo $tr->getTranslation('abstract/widgettheme', 'Add link')?></button>
    <div class="mt-3">
        <label for="launcher-info-url"><?php echo $tr->getTranslation('abstract/widgettheme', 'Information URL (optional)')?></label>
        <input id="launcher-info-url" class="form-control form-control-sm" type="url" name="LauncherInfoUrl" value="<?php echo htmlspecialchars(is_string($infoUrl) ? $infoUrl : '', ENT_QUOTES)?>">
    </div>
</details>
<script>
(() => {
    const rows = document.getElementById('launcher-links');
    const add = document.getElementById('add-launcher-link');
    const template = rows.firstElementChild.cloneNode(true);
    let nextIndex = rows.children.length;
    add.addEventListener('click', () => {
        const row = template.cloneNode(true);
        row.querySelectorAll('[data-field]').forEach(field => {
            field.name = 'LauncherLinks[' + nextIndex + '][' + field.dataset.field + ']';
            field.value = field.dataset.field === 'visibility' ? 'always' : '';
        });
        nextIndex++;
        rows.append(row);
        row.querySelector('input').focus();
    });
    rows.addEventListener('click', event => {
        if (event.target.matches('[data-remove]')) {
            event.target.closest('.row').remove();
            add.focus();
        }
    });
})();
</script>
