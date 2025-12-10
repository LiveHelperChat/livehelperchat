<script>
    import { onMount } from 'svelte';
    import { t } from "../../i18n/i18n.js";

    /* global lhinst, jQuery, ColorPicker, WWW_DIR_JAVASCRIPT, lhc */

    export let selector = "";

    function hashCode(stringToHash) {
        var hash = 0;
        for (var i = 0; i < stringToHash.length; i++) {
            var char = stringToHash.charCodeAt(i);
            hash = ((hash<<5)-hash)+char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash;
    }

    onMount(() => {
        var colorP = new ColorPicker({
             dom: document.getElementById('color-picker-chat-'+hashCode(selector)),
             value: '#0F0'
        });

        colorP.addEventListener('change', function (colorItem) {
            jQuery('#color-apply-'+hashCode(selector)).attr('data-bbcode','color='+colorP.getValue('hex'));
        });

        jQuery('.downdown-menu-color-'+hashCode(selector)).on('click', function (e) {
            if (jQuery(this).parent().is(".show")) {
                var target = jQuery(e.target);
                if (target.hasClass("keepopen") || target.parents(".keepopen").length){
                    return false;
                } else {
                    return true;
                }
            }
        });

        jQuery('.downdown-menu-color-'+hashCode(selector)+' .color-item').on('click',function () {
            colorP.setValue(jQuery(this).attr('data-color'));
        });
    });
</script>

<div class="btn-toolbar pb-2">
    <div class="btn-group btn-group-sm me-2" role="group">
        <button type="button" class="btn btn-outline-secondary" data-selector={selector} data-bbcode="s" onclick={(e) => lhinst.handleBBCode(jQuery(e.currentTarget))} title={$t("bbcode.strike")}><strike>S</strike></button>
        <button type="button" class="btn btn-outline-secondary" data-selector={selector} data-bbcode="quote" onclick={(e) => lhinst.handleBBCode(jQuery(e.currentTarget))} title={$t("bbcode.quote")}>&quot;</button>
        <button type="button" class="btn btn-outline-secondary" data-selector={selector} data-bbcode="youtube" onclick={(e) => lhinst.handleBBCode(jQuery(e.currentTarget))} title={$t("bbcode.youtube")}><i class="material-icons me-0">ondemand_video</i></button>
        <button type="button" class="btn btn-outline-secondary" data-selector={selector} data-bbcode="html" onclick={(e) => lhinst.handleBBCode(jQuery(e.currentTarget))} title={$t("bbcode.html_code")}><i class="material-icons me-0">code</i></button>
        <button type="button" class="btn btn-outline-secondary" data-selector={selector} data-bbcode="b" onclick={(e) => lhinst.handleBBCode(jQuery(e.currentTarget))} title={$t("bbcode.bold")}><b>B</b></button>
        <button type="button" class="btn btn-outline-secondary" data-selector={selector} data-bbcode="i" onclick={(e) => lhinst.handleBBCode(jQuery(e.currentTarget))} title={$t("bbcode.italic")}><i>I</i></button>
        <button type="button" class="btn btn-outline-secondary" data-selector={selector} data-bbcode="u" onclick={(e) => lhinst.handleBBCode(jQuery(e.currentTarget))} title={$t("bbcode.underline")}><u>U</u></button>
    </div>

    <div class="btn-group btn-group-sm me-2" role="group">
        <div class="dropdown me-2">
            <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {$t("bbcode.font_size")}
            </button>
            <div class="dropdown-menu">
                {#each Array(7) as _, index (index)}
                    <a class="dropdown-item" href="#" data-selector={selector} onclick={(e) => { e.preventDefault(); lhinst.handleBBCode(jQuery(e.currentTarget)); }} data-bbcode-end="fs" data-bbcode="fs{10+index}" style="font-size: {10+index}pt">{$t("bbcode.font_size")} {10+index}pt</a>
                {/each}
            </div>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {$t("bbcode.color")}
            </button>
            <div class="dropdown-menu keepopen downdown-menu-color-{hashCode(selector)}" style="width: 128px;">
                <div id="color-picker-chat-{hashCode(selector)}"></div>
                    <div class="row">
                        <div class="col-12 text-center ms-2 pb-0 pe-2">
                            {#each ['c00000','cf4c6d','ff0000','ffc000','ffff00','89c748','00b050','48c3c7','00b0f0','0070c0','002060','5c2585'] as colorItem, index}
                                <div class="float-start ms-1 mb-1 color-item" data-color={colorItem} style:background-color={'#'+ colorItem}></div>
                            {/each}
                        </div>
                    </div>
                <div class="pe-2 ps-2">
                    <button class="btn btn-outline-secondary w-100 btn-xs" type="button" id="color-apply-{hashCode(selector)}" data-bbcode="color=00FF00" data-selector={selector} onclick={(e) => lhinst.handleBBCode(jQuery(e.currentTarget))} data-bbcode-end="color">{$t("bbcode.apply")}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="btn-group btn-group-sm me-2" role="group">
        <button type="button" class="btn btn-outline-secondary" data-selector={selector} onclick={(e) => {window.lhcSelector = selector; lhc.revealModal({'hidecallback' : function(){jQuery('.embed-into').removeClass('embed-into');},'showcallback' : function(){ jQuery(window.lhcSelector).addClass('embed-into');},'title' : $t("bbcode.insert_image_or_file"),'iframe':true,'height':500,'url':WWW_DIR_JAVASCRIPT +'file/attatchfileimg'})}} title={$t("bbcode.insert_image_or_file")}><i class="material-icons me-0">attach_file</i></button>
        <button type="button" class="btn btn-outline-secondary" data-selector={selector} onclick={(e) => {window.lhcSelector = selector; lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'/chat/bbcodeinsert/0/(mode)/editor'})}}>
            <i class="material-icons me-0">&#xE24E;</i>
        </button>
        <button type="button" class="btn btn-outline-secondary" data-selector={selector} onclick={(e) => {return lhc.revealModal({'loadmethod':'post', 'datapost':{'msg':jQuery(selector).val()}, 'url':WWW_DIR_JAVASCRIPT +'chat/previewmessage'})}} title={$t("bbcode.preview")}><i class="material-icons me-0">visibility</i></button>
   </div>
</div>
