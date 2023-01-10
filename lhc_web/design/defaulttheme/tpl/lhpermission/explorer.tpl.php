<div>
<h1 ng-non-bindable><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Permissions explorer');?></h1>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li role="presentation" class="nav-item"><a href="#permissions-explorer" class="nav-link active" aria-controls="permissions-explorer" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Explorer');?></a></li>
    <li role="presentation" class="nav-item"><a href="#permissions-url-explorer" class="nav-link" aria-controls="permissions-url-explorer" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','URL Explorer');?></a></li>
    <li role="presentation" class="nav-item"><a href="#permissions-user-explorer" class="nav-link" aria-controls="permissions-user-explorer" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User permissions');?></a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="permissions-explorer">
        <div class="pb-2">
        <a class="btn btn-outline-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('permission/explorer')?>/(action)/1">
            <span class="material-icons">file_download</span>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Download as CSV');?>
        </a>
        </div>
        <table class="table table-sm" ng-non-bindable>
            <thead>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Module');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Permission');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Explain');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Used by URL');?></th>
            </thead>
            <?php foreach (erLhcoreClassModules::getModuleList() as $key => $Module) : ?>
                <?php $moduleFunctions = erLhcoreClassModules::getModuleFunctions($key, array('extract_url' => true)); ?>
                <?php if (count($moduleFunctions) > 0) : ?>
                    <?php foreach ($moduleFunctions as $keyFunction => $function) :?>
                        <tr>
                            <td>[<?php echo $key?>] <?php echo htmlspecialchars($Module['name']);?></td>
                            <td>
                                <?php echo $keyFunction?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($function['explain'])?>
                            </td>
                            <td>
                                <?php if (isset($function['url'])) : ?>
                                <ul class="mb-0">
                                <?php foreach ($function['url'] as $urlData) : ?>
                                    <li><a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('/')?><?php echo htmlspecialchars(preg_replace('/^lh/','',$urlData))?>"><?php echo htmlspecialchars(preg_replace('/^lh/','',$urlData))?></a></li>
                                <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </div>
    <div role="tabpanel" class="tab-pane" id="permissions-url-explorer">

        <div class="input-group">
            <input type="text" class="form-control" value="" id="url-permission-explore" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Enter URL to see required permissions');?>" />
            <div class="input-group-text" >
                <button type="button" id="explore-permission-button" class="btn btn-sm m-0 p-0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Search');?></button>
            </div>
        </div>

        <div id="permission-url-explore-result" class="pt-3"></div>

    </div>
    <div role="tabpanel" class="tab-pane" id="permissions-user-explorer">

        <div class="input-group">
            <input type="text" class="form-control" value="" id="module-explore" placeholder="lhfront" />
            <input type="text" class="form-control" value="" id="function-explorer" placeholder="use" />
            <div class="input-group-text" >
                <button type="button" id="explore-user-button" class="btn btn-sm m-0 p-0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Find users who can use it');?></button>
            </div>
        </div>

        <div id="permission-user-explore-result" class="pt-3"></div>

    </div>


</div>
</div>
<script>
    $(function() {
        $('#explore-permission-button').click(function(){
            $.post('<?php echo erLhcoreClassDesign::baseurl('permission/explorer')?>/(action)/2',{url: $('#url-permission-explore').val()}, function(data){
                $('#permission-url-explore-result').html(data);
            })
        });
        $('#explore-user-button').click(function(){
            $.post('<?php echo erLhcoreClassDesign::baseurl('permission/explorer')?>/(action)/3',{
                'module': $('#module-explore').val(),
                'function': $('#function-explorer').val()
            }, function(data){
                $('#permission-user-explore-result').html(data);
            })
        });
    });
</script>