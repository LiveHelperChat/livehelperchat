import { derived, writable } from "svelte/store";
import translations from "./translations";

export const locale = writable("lhcbo");

function translate(locale, key, vars) {
    // Let's throw some errors if we're trying to use keys/locales that don't exist.
    // We could improve this by using Typescript and/or fallback values.
    if (!key) throw new Error("no key provided to $t()");
    if (!locale) throw new Error(`no translation for key "${key}"`);

    // Grab the translation from the translations object.
    let text = translations[key];

    if (!text) {
        console.log(`no translation found for ${key}`);
        return key;
    };

    // Replace any passed in variables in the translation string.
    Object.keys(vars).map((k) => {
        const regex = new RegExp(`{{${k}}}`, "g");
        text = text.replace(regex, vars[k]);
    });

    return text;
}

export const t = derived(locale, ($locale) => (key, vars = {}) => {
        return translate($locale, key, vars)
    }
);

