<?php /*$currentUser = erLhcoreClassUser::instance(); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom p-0 pl-1">
    <?php //include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?>

    <button class="btn btn-outline-light mr-auto" type="button" ng-click="lhc.toggleList('lmtoggle')" title="Expand or collapse left menu" aria-expanded="true" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">

            <?php $hideULSetting = true;?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>

            <?php //include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>
            <li class="li-icon nav-item">
                <a class="nav-link" ng-click="lhc.toggleList('lmtoggler')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Expand or collapse right menu')?>">
                    <span class="navbar-toggler-icon"></span>
                </a>
            </li>
        </ul>
    </div>

</nav>*/ ?>