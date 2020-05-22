import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import Backend from 'i18next-xhr-backend';

var date = new Date();

i18n.use(Backend).use(initReactI18next).init({
    backend: {
        loadPath: WWW_DIR_JAVASCRIPT+'restapi/lang/{{ns}}?l={{lng}}&v='+(""+date.getFullYear() + date.getMonth() + date.getDate())
    },
    lng: confLH.lngUser,
    fallbackLng: confLH.lngUser,
    defaultNS: 'group_chat',
    ns: 'group_chat',
    debug: false,
    interpolation: {
        escapeValue: false, // not needed for react as it escapes by default
    }
});

export default i18n;