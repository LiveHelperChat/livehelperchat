# You are an expert markdown software developer.
# Be concise!
# Take requests for writing code in an existing file.
# You must only write relevant lines.
# You must not recreate the entire file with the changes, write only necessary code that will get inserted.
# DO NOT repeat surrounding code, only generate the lines nessarary to directly insert into users code.
# Once you understand the request you MUST only return the corresponding code, not explanation.

# Application folders structure

* `lhc_web/cache` - Stores cached files
* `lhc_web/design` - Contains design categories
* `lhc_web/doc` - Release documentation
* `lhc_web/extension` - All extensions are placed here
* `lhc_web/ezcomponents` - eZ Components core components
* `lhc_web/lib` - Core of the application
* `lhc_web/autoloads` - application statically defined autoloads. Should not be used anymore to define new classes.
* `lhc_web/lhcore_autoload.php` - Main application autoload file
* `lhc_web/core` - Folder containing application logic modules
* `lhc_web/models` - Folder containing application model classes
* `lhc_web/modules` - Application modules are placed here
* `lhc_web/pos` - Represents eZ Components POS, persistent object tables definitions
* `lhc_web/settings` - Contains application settings files
* `lhc_web/translations ` - Contains application translations


# Main components

### Widget embeded in website.

* `lhc_web/design/defaulttheme/widget/react-app` - Build with react
* `lhc_web/design/defaulttheme/widget/wrapper` - Wrapper of the widget application

### Dashboard Svelte component

* `lhc_web/design/defaulttheme/js/svelte` - Svelte application for the dashboard

### Bot builder application

* `lhc_web/design/defaulttheme/js/react` - Written with React

### Back office related small JS apps (Canned messages suggester, Mail support, Dashboard chat tabs, Group Chats)

* `lhc_web/design/defaulttheme/js/admin` - Back office chat application