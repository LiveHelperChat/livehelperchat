#!/bin/sh

echo "Updating LT translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/lt?file=1 > lhc_lt.ts

echo "Updating fr_FR translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/fr?file=1 > lhc_fr.ts

echo "Updating zh_ZH translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/zh?file=1 > lhc_zh.ts

echo "Updating ru_RU translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/ru?file=1 > lhc_ru.ts

echo "Updating pt_BR translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/pt_BR?file=1 > lhc_pt.ts

echo "Updating pl_PL translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/pl_PL?file=1 > lhc_pl.ts

echo "Updating no_NO translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/nb?file=1 > lhc_nb.ts

echo "Updating it_IT translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/it?file=1 > lhc_it.ts

echo "Updating es_MX translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/es?file=1 > lhc_es.ts

echo "Updating de_DE translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/de?file=1 > lhc_de.ts

echo "Updating cs_CS translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/cs?file=1 > lhc_cs.ts

echo "Updating ar_EG translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/ar_EG?file=1 > lhc_ar.ts

echo "Updating tr_TR translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/tr?file=1 > lhc_tr.ts

echo "Updating hr_HR translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/hr?file=1 > lhc_hr.ts

echo "Updating nl_NL translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/nl?file=1 > lhc_nl.ts

echo "Updating vi_VN translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/vi?file=1 > lhc_vi.ts

echo "Updating id_ID translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/id?file=1 > lhc_id.ts

echo "Updating sv_SV translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/sv?file=1 > lhc_sv.ts

echo "Updating fa_FA translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/fa?file=1 > lhc_fa.ts

echo "Updating el_EL translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/el?file=1 > lhc_el.ts

echo "Updating th_TH translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/th_TH?file=1 > lhc_th.ts

echo "Updating da_DA translations"
curl -L --user $1:$2 -X GET https://www.transifex.com/api/2/project/live-helper-chat/resource/translation_desktopts/translation/da?file=1 > lhc_da.ts

echo "Generating translations"
/usr/lib64/qt4/bin/lrelease lhc_ar.ts
/usr/lib64/qt4/bin/lrelease lhc_br.ts
/usr/lib64/qt4/bin/lrelease lhc_cs.ts
/usr/lib64/qt4/bin/lrelease lhc_de.ts
/usr/lib64/qt4/bin/lrelease lhc_en.ts
/usr/lib64/qt4/bin/lrelease lhc_es.ts
/usr/lib64/qt4/bin/lrelease lhc_fr.ts
/usr/lib64/qt4/bin/lrelease lhc_hr.ts
/usr/lib64/qt4/bin/lrelease lhc_id.ts
/usr/lib64/qt4/bin/lrelease lhc_it.ts
/usr/lib64/qt4/bin/lrelease lhc_lt.ts
/usr/lib64/qt4/bin/lrelease lhc_nb.ts
/usr/lib64/qt4/bin/lrelease lhc_nl.ts
/usr/lib64/qt4/bin/lrelease lhc_pl.ts
/usr/lib64/qt4/bin/lrelease lhc_pt.ts
/usr/lib64/qt4/bin/lrelease lhc_ru.ts
/usr/lib64/qt4/bin/lrelease lhc_tr.ts
/usr/lib64/qt4/bin/lrelease lhc_vi.ts
/usr/lib64/qt4/bin/lrelease lhc_zh.ts
/usr/lib64/qt4/bin/lrelease lhc_sv.ts
/usr/lib64/qt4/bin/lrelease lhc_fa.ts
/usr/lib64/qt4/bin/lrelease lhc_el.ts
/usr/lib64/qt4/bin/lrelease lhc_th.ts
/usr/lib64/qt4/bin/lrelease lhc_da.ts

echo "Copying files"
/bin/cp -rf ./*.qm ../../lhc_dc/windows_qt_4x/translations/
/bin/cp -rf ./*.qm ../../lhc_dc/mac_qt_4x/lhc.app/Contents/MacOS/translations/