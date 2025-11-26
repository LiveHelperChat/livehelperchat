import { createI18n } from "./createI18n.js";

const { t, translations, initTranslations } = createI18n("masking");

// Initialize translations immediately for backward compatibility
initTranslations();

export { t, translations, initTranslations };
