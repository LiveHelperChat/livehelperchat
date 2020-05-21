import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import Backend from 'i18next-xhr-backend';

var date = new Date();

i18n.use(Backend).use(initReactI18next).init({
    backend: {
        loadPath: '{{lng}}restapi/lang/{{ns}}?v='+(""+date.getFullYear() + date.getMonth() + date.getDate())
    },
    lng: WWW_DIR_JAVASCRIPT,
    fallbackLng: WWW_DIR_JAVASCRIPT,
    defaultNS: 'group_chat',
    ns: 'group_chat',
    debug: false,
    interpolation: {
        escapeValue: false, // not needed for react as it escapes by default
    }
});

export default i18n;