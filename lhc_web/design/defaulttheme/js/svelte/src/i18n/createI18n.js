import { derived, writable } from "svelte/store";

/**
 * Creates an i18n instance for a specific scope/namespace
 * @param {string} scope - The translation namespace (e.g., 'lhcbo', 'masking', 'group_chat')
 * @returns {Object} - { t, translations, initTranslations }
 */
export function createI18n(scope) {
    const translations = writable({});
    let loaded = false;

    async function loadTranslations() {
        if (loaded) return;
        
        try {
            const response = await fetch(WWW_DIR_JAVASCRIPT + "restapi/lang/" + scope + '/v13', {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRFToken": confLH.csrf_token
                }
            });
            const data = await response.json();
            translations.set(data);
            loaded = true;
        } catch (error) {
            console.log(`Translations for scope "${scope}" could not be loaded!`);
        }
    }

    function translate($translations, key, vars) {
        if (!key) throw new Error("no key provided to $t()");

        let text = $translations[key];

        if (!text) {
            return key;
        }

        // Replace any passed in variables in the translation string.
        Object.keys(vars).map((k) => {
            const regex = new RegExp(`{{${k}}}`, "g");
            text = text.replace(regex, vars[k]);
        });

        return text;
    }

    const t = derived(translations, ($translations) => (key, vars = {}) => {
        return translate($translations, key, vars);
    });

    return {
        t,
        translations,
        initTranslations: loadTranslations
    };
}
