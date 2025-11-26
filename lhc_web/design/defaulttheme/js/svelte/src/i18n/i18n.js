import { createI18n } from "./createI18n.js";

// Main lhcbo translations - automatically initialized on import
const { t, translations, initTranslations } = createI18n("lhcbo");

// Initialize translations immediately for backward compatibility
initTranslations();

export { t, translations, initTranslations };

