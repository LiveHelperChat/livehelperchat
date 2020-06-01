var lhc_domain = '';

if (typeof LHCChatOptions !== 'undefined' && typeof LHCChatOptions.opt !== 'undefined' && typeof LHCChatOptions.opt.domain !== 'undefined') {
lhc_domain = LHCChatOptions.opt.domain;
}

if (typeof LHCChatOptionsPage !== 'undefined' && typeof LHCChatOptionsPage.opt !== 'undefined' && typeof LHCChatOptionsPage.opt.domain !== 'undefined') {
lhc_domain = LHCChatOptionsPage.opt.domain;
}

if (lhc_domain === '') {
lhc_domain = document.location.hostname.replace(/^(?:[a-z0-9\-\.]+\.)??([a-z0-9\-]+)?(\.com|\.net|\.org|\.biz|\.ws|\.in|\.me|\.co\.uk|\.co|\.org\.uk|\.ltd\.uk|\.plc\.uk|\.me\.uk|\.edu|\.mil|\.br\.com|\.cn\.com|\.eu\.com|\.hu\.com|\.no\.com|\.qc\.com|\.sa\.com|\.se\.com|\.se\.net|\.us\.com|\.uy\.com|\.ac|\.co\.ac|\.gv\.ac|\.or\.ac|\.ac\.ac|\.af|\.am|\.as|\.at|\.ac\.at|\.co\.at|\.gv\.at|\.or\.at|\.asn\.au|\.com\.au|\.edu\.au|\.org\.au|\.net\.au|\.id\.au|\.be|\.ac\.be|\.adm\.br|\.adv\.br|\.am\.br|\.arq\.br|\.art\.br|\.bio\.br|\.cng\.br|\.cnt\.br|\.com\.br|\.ecn\.br|\.eng\.br|\.esp\.br|\.etc\.br|\.eti\.br|\.fm\.br|\.fot\.br|\.fst\.br|\.g12\.br|\.gov\.br|\.ind\.br|\.inf\.br|\.jor\.br|\.lel\.br|\.med\.br|\.mil\.br|\.net\.br|\.nom\.br|\.ntr\.br|\.odo\.br|\.org\.br|\.ppg\.br|\.pro\.br|\.psc\.br|\.psi\.br|\.rec\.br|\.slg\.br|\.tmp\.br|\.tur\.br|\.tv\.br|\.vet\.br|\.zlg\.br|\.br|\.ab\.ca|\.bc\.ca|\.mb\.ca|\.nb\.ca|\.nf\.ca|\.ns\.ca|\.nt\.ca|\.on\.ca|\.pe\.ca|\.qc\.ca|\.sk\.ca|\.yk\.ca|\.ca|\.cc|\.ac\.cn|\.com\.cn|\.edu\.cn|\.gov\.cn|\.org\.cn|\.bj\.cn|\.sh\.cn|\.tj\.cn|\.cq\.cn|\.he\.cn|\.nm\.cn|\.ln\.cn|\.jl\.cn|\.hl\.cn|\.js\.cn|\.zj\.cn|\.ah\.cn|\.gd\.cn|\.gx\.cn|\.hi\.cn|\.sc\.cn|\.gz\.cn|\.yn\.cn|\.xz\.cn|\.sn\.cn|\.gs\.cn|\.qh\.cn|\.nx\.cn|\.xj\.cn|\.tw\.cn|\.hk\.cn|\.mo\.cn|\.cn|\.cx|\.cz|\.de|\.dk|\.fo|\.com\.ec|\.tm\.fr|\.com\.fr|\.asso\.fr|\.presse\.fr|\.fr|\.gf|\.gs|\.co\.il|\.net\.il|\.ac\.il|\.k12\.il|\.gov\.il|\.muni\.il|\.ac\.in|\.co\.in|\.org\.in|\.ernet\.in|\.gov\.in|\.net\.in|\.res\.in|\.is|\.it|\.ac\.jp|\.co\.jp|\.go\.jp|\.or\.jp|\.ne\.jp|\.ac\.kr|\.co\.kr|\.go\.kr|\.ne\.kr|\.nm\.kr|\.or\.kr|\.li|\.lt|\.lu|\.asso\.mc|\.tm\.mc|\.com\.mm|\.org\.mm|\.net\.mm|\.edu\.mm|\.gov\.mm|\.ms|\.nl|\.no|\.nu|\.pl|\.ro|\.org\.ro|\.store\.ro|\.tm\.ro|\.firm\.ro|\.www\.ro|\.arts\.ro|\.rec\.ro|\.info\.ro|\.nom\.ro|\.nt\.ro|\.se|\.si|\.com\.sg|\.org\.sg|\.net\.sg|\.gov\.sg|\.sk|\.st|\.tf|\.ac\.th|\.co\.th|\.go\.th|\.mi\.th|\.net\.th|\.or\.th|\.tm|\.to|\.com\.tr|\.edu\.tr|\.gov\.tr|\.k12\.tr|\.net\.tr|\.org\.tr|\.com\.tw|\.org\.tw|\.net\.tw|\.ac\.uk|\.uk\.com|\.uk\.net|\.gb\.com|\.gb\.net|\.vg|\.sh|\.kz|\.ch|\.info|\.ua|\.gov|\.name|\.pro|\.ie|\.hk|\.com\.hk|\.org\.hk|\.net\.hk|\.edu\.hk|\.us|\.tk|\.cd|\.by|\.ad|\.lv|\.eu\.lv|\.bz|\.es|\.jp|\.cl|\.ag|\.mobi|\.eu|\.co\.nz|\.org\.nz|\.net\.nz|\.maori\.nz|\.iwi\.nz|\.io|\.la|\.md|\.sc|\.sg|\.vc|\.tw|\.travel|\.my|\.se|\.tv|\.pt|\.com\.pt|\.edu\.pt|\.asia|\.fi|\.com\.ve|\.net\.ve|\.fi|\.org\.ve|\.web\.ve|\.info\.ve|\.co\.ve|\.tel|\.im|\.gr|\.ru|\.net\.ru|\.org\.ru|\.hr|\.com\.hr)$/, '$1$2');
}

if (typeof LHCChatOptionsPage !== 'undefined') {
var LHCChatOptions = LHCChatOptionsPage;
}

if (typeof lh_inst === 'undefined') {
window.lh_inst = {};
lh_inst.showStartWindow = function(){
window.$_LHC.eventListener.emitEvent('showWidget');
};

lh_inst.addTag = function(tag){
window.$_LHC.eventListener.emitEvent('addTag',[tag]);
};

lh_inst.addTag = function(tag){
window.$_LHC.eventListener.emitEvent('addTag',[tag]);
};

lh_inst.lh_openchatWindow = function(){
<?php if (isset($click) && $click == 'internal') : ?>
    window.$_LHC.eventListener.emitEvent('showWidget');
<?php else : ?>
    window.$_LHC.eventListener.emitEvent('openPopup');
<?php endif; ?>
};
}

var LHC_API = LHC_API||{};
LHC_API.args = {mode:'<?php echo $mode?>'<?php (isset($position) && $position == 'api') ? print ",position:'api'" : ''?>,lhc_base_url:'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('/')?>',domain: lhc_domain, wheight:450,wwidth:350,pheight:520,pwidth:500<?php if (is_array($depId) && !empty($depId)) : ?>,department:[<?php echo implode(',',$depId)?>]<?php endif;?><?php if ($leaveamessage == true) : ?>,leaveamessage:true<?php endif; ?><?php if ($disable_pro_active == true) : ?>,proactive:false<?php else : ?>,check_messages:true<?php endif;?><?php isset($uparams['theme']) & is_numeric($uparams['theme']) ? print ',theme:' . (int)$uparams['theme'] : ''?>};
(function() {
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
var date = new Date();po.src = '//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('js/widgetv2/index.js');?>?v1'+(""+date.getFullYear() + date.getMonth() + date.getDate());
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
