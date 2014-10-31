<?php

require_once 'abstract.php';

class Mage_Shell_Translations extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        if ($filename = $this->getArg('file')) {
            $filename = Mage::getBaseDir() . '/var/translations/' . $filename;
            if(file_exists($filename)) {
                $rowCounter = 1;
                $headers = array();
                $csvData = preg_replace('/\r\n?/', "\n", file_get_contents($filename));
                $data = str_getcsv($csvData, "\n"); //parse the rows
                foreach($data as &$row) {
                    $rowData= str_getcsv($row, ",");
                    if($rowCounter == 1) {
                        $headers = $rowData;
                    } else {
                        $rowData = array_combine($headers,$rowData);
                        // Translated!
                        if(!empty($rowData['Translation'])) {
                            $this->appendToCsv($rowData['Language'],$rowData['Module'],$rowData['Untranslated String'],$rowData['Translation']);
                        } else {
                            echo 'No translation in CSV for ' . $rowData['Translation Code'] . PHP_EOL;
                        }
                    }
                    $rowCounter++;
                }
            } else {
                echo 'File ' . $filename . ' not found';
            }
        } else {
            echo $this->usageHelp();
        }
    }

    private function appendToCsv($language,$module,$translate,$translation) {
        $languageFile = Mage::getBaseDir() . '/app/locale/' . $language . '/' . $module . '.csv';
        if(file_exists($languageFile)) {
            // Loop through file, looking for translation
            $languageFileLines = array();
            $rows = explode("\n", file_get_contents($languageFile));
            foreach($rows as $row) {
                if(empty($row) || substr($row,0,1) == '#') continue;
                $parts = explode("\",\"", $row); // Hackish, this could be done better. Maybe using Magento's own models to interpret the locale CSV files?
                foreach($parts as &$part) {
                    $part = trim($part,'"');
                }
                if($parts[0] != $translate) { // Only put back the lines that are not in this translation
                    $languageFileLines[] = $row;
                }
            }
            file_put_contents($languageFile, implode("\n",$languageFileLines));
            file_put_contents($languageFile, "\n" . '"' . $translate . '","' . $translation . '"', FILE_APPEND);
            echo 'Updated translation string for ' . $language . '/' . $module . PHP_EOL;
        } else {
            echo 'Created new translation file for ' . $language . '/' . $module . PHP_EOL;
            file_put_contents($languageFile, "\n" . '"' . $translate . '","' . $translation . '"');
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f translations.php -- [options]

  --run <filename.csv>          Run translation updater using <filename.csv> (which should be placed in var/translations)

USAGE;
    }
}

$shell = new Mage_Shell_Translations();
$shell->run();
