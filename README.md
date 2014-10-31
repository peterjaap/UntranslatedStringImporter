# Untranslated String Importer

The [EW_UntranslatedStrings extension](https://github.com/ericthehacker/magento-untranslatedstrings) does a great job of easily finding strings that are not translated. It helps cut down translation time tremendously. After collecting untranslated strings for a few days, we exported the strings to a CSV from the backend and sent it to our client for translation. We then wrote this little importer to take care of appending the translated strings to the locale's language CSV files.

Steps;

1. Export the grid overview to CSV from the Untranslated Strings page in the Magento backend.

2. Add a column named 'Translation' to the CSV export file.

3. Send it to your client for translation

4. Upon receiving the translated file, place it in var/translations

5. Start the importer with "php shell/translations.php --file export.csv"

6. Clear the translations cache

7. Profit
