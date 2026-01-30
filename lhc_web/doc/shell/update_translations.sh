#!/bin/sh

if [ ! -f "index.php" -a \
     ! -d "design" -a \
     ! -d "lib" -a \
     ! -d "pos" -a \
     ! -d "modules" ] ; then
     echo "You seem to be in the wrong directory"
     echo "Place yourself in the LHC root directory and run ./doc/shell/update_translations.sh"
     exit 1
fi

echo "Updating lt_LT | Lithuanian (lt)"
cat translations/trans/translation_webts-lt.ts > translations/lt_LT/translation.ts

echo "Updating fr_FR | French (fr)"
cat translations/trans/translation_webts-fr.ts > translations/fr_FR/translation.ts

echo "Updating zh_ZH | Chinese (zh)"
cat translations/trans/translation_webts-zh.ts > translations/zh_ZH/translation.ts

echo "Updating ru_RU | Russian (Russia)"
cat translations/trans/translation_webts-ru.ts > translations/ru_RU/translation.ts

echo "Updating pt_BR | Portuguese (Brazil)"
cat translations/trans/translation_webts-pt_br.ts > translations/pt_BR/translation.ts

echo "Updating pl_PL | Polish (Poland)"
cat translations/trans/translation_webts-pl_pl.ts > translations/pl_PL/translation.ts

echo "Updating no_NO translations"
cat translations/trans/translation_webts-nb.ts > translations/no_NO/translation.ts

echo "Updating it_IT translations"
cat translations/trans/translation_webts-it.ts > translations/it_IT/translation.ts

echo "Updating es_MX translations"
cat translations/trans/translation_webts-es_mx.ts > translations/es_MX/translation.ts

echo "Updating de_DE translations"
cat translations/trans/translation_webts-de.ts > translations/de_DE/translation.ts

echo "Updating cs_CS translations"
cat translations/trans/translation_webts-cs.ts > translations/cs_CS/translation.ts

echo "Updating ar_EG translations"
cat translations/trans/translation_webts-ar_eg.ts > translations/ar_EG/translation.ts

echo "Updating tr_TR translations"
cat translations/trans/translation_webts-tr.ts > translations/tr_TR/translation.ts

echo "Updating hr_HR translations"
cat translations/trans/translation_webts-hr.ts > translations/hr_HR/translation.ts

echo "Updating nl_NL translations"
cat translations/trans/translation_webts-nl.ts > translations/nl_NL/translation.ts

echo "Updating vi_VN translations"
cat translations/trans/translation_webts-vi.ts > translations/vi_VN/translation.ts

echo "Updating id_ID translations"
cat translations/trans/translation_webts-id.ts > translations/id_ID/translation.ts

echo "Updating sv_SV translations"
cat translations/trans/translation_webts-sv.ts > translations/sv_SV/translation.ts

echo "Updating fa_IR translations"
cat translations/trans/translation_webts-fa_ir.ts > translations/fa_FA/translation.ts

echo "Updating el_EL translations"
cat translations/trans/translation_webts-el.ts > translations/el_EL/translation.ts

echo "Updating he_HE translations"
cat translations/trans/translation_webts-he.ts > translations/he_HE/translation.ts

echo "Updating th_TH translations"
cat translations/trans/translation_webts-th_th.ts > translations/th_TH/translation.ts

echo "Updating da_DA translations"
cat translations/trans/translation_webts-da.ts > translations/da_DA/translation.ts

echo "Updating bg_BG | Bulgarian (Bulgaria)"
cat translations/trans/translation_webts-bg_bg.ts > translations/bg_BG/translation.ts

echo "Updating ro_RO translations"
cat translations/trans/translation_webts-ro_ro.ts > translations/ro_RO/translation.ts

echo "Updating ka_KA (Georgian (ka) translations"
cat translations/trans/translation_webts-ka.ts > translations/ka_KA/translation.ts

echo "Updating fi_FI translations"
cat translations/trans/translation_webts-fi_fi.ts > translations/fi_FI/translation.ts

echo "Updating sq_AL translations"
cat translations/trans/translation_webts-sq_al.ts > translations/sq_AL/translation.ts

echo "Updating ca_ES translations"
cat translations/trans/translation_webts-ca_es.ts > translations/ca_ES/translation.ts

echo "Updating sk_SK translations"
cat translations/trans/translation_webts-sk_sk.ts > translations/sk_SK/translation.ts

echo "Updating hu_HU translations"
cat translations/trans/translation_webts-hu_hu.ts > translations/hu_HU/translation.ts

echo "Updating zh_CN.GB2312 translations"
cat translations/trans/translation_webts-zh_cn.gb2312.ts > translations/zh_CN/translation.ts

echo "Updating zh_HK translations"
cat translations/trans/translation_webts-zh_hk.ts > translations/zh_HK/translation.ts

echo "Updating zh_TW.Big5 translations"
cat translations/trans/translation_webts-zh_tw.big5.ts > translations/zh_TW/translation.ts

echo "Updating es_CO translations"
cat translations/trans/translation_webts-es_co.ts > translations/es_CO/translation.ts

echo "Updating pt_PT translations"
cat translations/trans/translation_webts-pt_pt.ts > translations/pt_PT/translation.ts

echo "Updating uk_UK translations"
cat translations/trans/translation_webts-uk.ts > translations/uk_UK/translation.ts

echo "Updating ja_JP translations"
cat translations/trans/translation_webts-ja_jp.ts > translations/ja_JP/translation.ts
