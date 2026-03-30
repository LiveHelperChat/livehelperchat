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

update_translation() {
     key="$1"
     dest="$2"
     trans_dir="translations/trans"

     # Normalize target: remove non-alphanumeric and lowercase
     norm_key=$(echo "$key" | tr '[:upper:]' '[:lower:]' | sed 's/[^a-z0-9]//g')

     found=0

     # First try exact common candidates (fast path)
     for cand in "$trans_dir/translation_webts_$key.ts" "$trans_dir/translation_webts-$key.ts"; do
          if [ -f "$cand" ]; then
               cat "$cand" > "$dest"
               echo "Used source: $cand -> $dest"
               found=1
               break
          fi
     done

     # If not found, scan all translation files and match by normalized suffix
     if [ $found -ne 1 ]; then
          for f in "$trans_dir"/translation_webts*.ts; do
               [ -f "$f" ] || continue
               name=$(basename "$f")
               suffix=${name#translation_webts}
               suffix=${suffix%.ts}
               norm_suffix=$(echo "$suffix" | tr '[:upper:]' '[:lower:]' | sed 's/[^a-z0-9]//g')
               if [ "$norm_suffix" = "$norm_key" ]; then
                    cat "$f" > "$dest"
                    echo "Used source: $f -> $dest"
                    found=1
                    break
               fi
          done
     fi

     if [ $found -ne 1 ]; then
          echo "ERROR: translation file not found for key '$key'." >&2
          echo "Searched in: $trans_dir for variants of '$key'" >&2
     fi
}

echo "Updating lt_LT | Lithuanian (lt)"
update_translation "lt" "translations/lt_LT/translation.ts"

echo "Updating fr_FR | French (fr)"
update_translation "fr" "translations/fr_FR/translation.ts"

echo "Updating zh_ZH | Chinese (zh)"
update_translation "zh" "translations/zh_ZH/translation.ts"

echo "Updating ru_RU | Russian (Russia)"
update_translation "ru" "translations/ru_RU/translation.ts"

echo "Updating pt_BR | Portuguese (Brazil)"
update_translation "pt_br" "translations/pt_BR/translation.ts"

echo "Updating pl_PL | Polish (Poland)"
update_translation "pl_pl" "translations/pl_PL/translation.ts"

echo "Updating no_NO translations"
update_translation "nb" "translations/no_NO/translation.ts"

echo "Updating it_IT translations"
update_translation "it" "translations/it_IT/translation.ts"

echo "Updating es_MX translations"
update_translation "es_mx" "translations/es_MX/translation.ts"

echo "Updating de_DE translations"
update_translation "de" "translations/de_DE/translation.ts"

echo "Updating cs_CS translations"
update_translation "cs" "translations/cs_CS/translation.ts"

echo "Updating ar_EG translations"
update_translation "ar_eg" "translations/ar_EG/translation.ts"

echo "Updating tr_TR translations"
update_translation "tr" "translations/tr_TR/translation.ts"

echo "Updating hr_HR translations"
update_translation "hr" "translations/hr_HR/translation.ts"

echo "Updating nl_NL translations"
update_translation "nl" "translations/nl_NL/translation.ts"

echo "Updating vi_VN translations"
update_translation "vi" "translations/vi_VN/translation.ts"

echo "Updating id_ID translations"
update_translation "id" "translations/id_ID/translation.ts"

echo "Updating sv_SV translations"
update_translation "sv" "translations/sv_SV/translation.ts"

echo "Updating fa_IR translations"
update_translation "fa_ir" "translations/fa_FA/translation.ts"

echo "Updating el_EL translations"
update_translation "el" "translations/el_EL/translation.ts"

echo "Updating he_HE translations"
update_translation "he" "translations/he_HE/translation.ts"

echo "Updating th_TH translations"
update_translation "th_th" "translations/th_TH/translation.ts"

echo "Updating da_DA translations"
update_translation "da" "translations/da_DA/translation.ts"

echo "Updating bg_BG | Bulgarian (Bulgaria)"
update_translation "bg_bg" "translations/bg_BG/translation.ts"

echo "Updating ro_RO translations"
update_translation "ro_ro" "translations/ro_RO/translation.ts"

echo "Updating ka_KA (Georgian (ka) translations"
update_translation "ka" "translations/ka_KA/translation.ts"

echo "Updating fi_FI translations"
update_translation "fi_fi" "translations/fi_FI/translation.ts"

echo "Updating sq_AL translations"
update_translation "sq_al" "translations/sq_AL/translation.ts"

echo "Updating ca_ES translations"
update_translation "ca_es" "translations/ca_ES/translation.ts"

echo "Updating sk_SK translations"
update_translation "sk_sk" "translations/sk_SK/translation.ts"

echo "Updating hu_HU translations"
update_translation "hu_hu" "translations/hu_HU/translation.ts"

echo "Updating zh_CN.GB2312 translations"
update_translation "zh_cn.gb2312" "translations/zh_CN/translation.ts"

echo "Updating zh_HK translations"
update_translation "zh_hk" "translations/zh_HK/translation.ts"

echo "Updating zh_TW.Big5 translations"
update_translation "zh_tw.big5" "translations/zh_TW/translation.ts"

echo "Updating es_CO translations"
update_translation "es_co" "translations/es_CO/translation.ts"

echo "Updating pt_PT translations"
update_translation "pt_pt" "translations/pt_PT/translation.ts"

echo "Updating uk_UK translations"
update_translation "uk" "translations/uk_UK/translation.ts"

echo "Updating ja_JP translations"
update_translation "ja_jp" "translations/ja_JP/translation.ts"
