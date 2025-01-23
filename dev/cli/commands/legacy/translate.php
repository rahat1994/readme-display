<?php

ini_set('memory_limit', '512M');

return function($args) {
    $pluginDir = realpath(__DIR__ . '/../../../..');

    $config = require $pluginDir . '/config/app.php';

    $languagesDir = $pluginDir . '/language';

    $potFile = $languagesDir . "/{$config['slug']}.pot --exclude=tests,vendor";

    $command = 'which wp';

    $cliPath = trim(shell_exec($command));

    if (!$cliPath) {
        die("WP-CLI is not installed or not accessible in the system PATH.\n");
    }

    if (!file_exists($languagesDir)) {
        mkdir($languagesDir, 0755, true);
    }

    // Command to generate .pot file using WP-CLI with increased memory limit
    $headers = [
        'Report-Msgid-Bugs-To' => "https://github.com/wpfluent/{$config['slug']}/issues/new?title=Translation Bug Report&body=Please describe the translation issue here",
        'POT-Creation-Date' => date('Y-m-d H:m:s'),
    ];

    // Convert the headers array to a JSON string
    $headersJson = json_encode($headers);

    // Construct the makePot command
    $makePotCommand = "php -d memory_limit=512M ";
    $makePotCommand .= "$cliPath i18n make-pot ";
    $makePotCommand .= "$pluginDir --domain={$config['slug']} ";
    $makePotCommand .= "--headers='$headersJson' ";
    $makePotCommand .= "--allow-root --exclude=tests,vendor $potFile 2>&1";

    echo "Please wait while the translation file is being generated...\n";

    $output = shell_exec($makePotCommand);

    $file = explode(' ', $potFile);

    $file = realpath($file[0]);

    if (file_exists($file)) {
        echo "Translation file generated successfully at: $file\n";
    } else {
        echo "Failed to generate translation file. Output: $output\n";
    }

    // After generating the .pot file
    $languages = ["fr_FR", "de_DE", "bn_BD"];

    foreach ($languages as $lang) {
        $poFile = "{$languagesDir}/{$config['slug']}-{$lang}.po";
        $moFile = "{$languagesDir}/{$config['slug']}-{$lang}.mo";

        // Create .po file from .pot if it doesn't exist
        if (!file_exists($poFile)) {
            copy($file, $poFile);
        }

        // Compile .mo file from .po
        $cmd = "msgfmt -o $moFile $poFile";
        $res = shell_exec($cmd . ' 2>&1');

        if (file_exists($moFile)) {
            echo "Language file generated successfully for: $lang\n";
        } else {
            echo "Failed to generate language file for: $lang. Output: $res\n";
        }
    }

    // File generation is completed, now start the translation
    // from generated .po files by using any translation service

    // Function to extract msgid entries from a .po file
    $extractMsgids = function($file) {
        $msgids = [];
        $content = file_get_contents($file);

        // Match all msgid strings in the .po file
        preg_match_all('/msgid "(.*?)"/', $content, $matches);
        $msgids = $matches[1];
        return $msgids;
    };

    // Function to translate msgids using LibreTranslate API
    // Function to translate msgids using the trans.sh script and Ollama
    $translateMsgids = function($msgids, $targetLanguage) {
        $translations = [];

        foreach ($msgids as $msgid) {
            $url = 'http://localhost:11434/api/generate';

            $data = [
                'stream' => false,
                'model' => 'ALIENTELLIGENCE/translationandlocalizationspecialist',
                'prompt' => "Translate \"$msgid\" to \"$targetLanguage\". Provide only the translated text without anything else, like google translate.",
            ];

            $options = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            $response = curl_exec($ch);
            curl_close($ch);
            $responses = explode("\n", trim($response));
            foreach (array_splice($responses, 0) as $res) {
                $result = json_decode($res, true);
                if ($result && isset($result['response'])) {
                    $translations[$msgid] = $result['response'];
                }
            }
        }

        return $translations;
    };

    // Function to update the .po file with translations
    $updatePoFile = function($file, $translations) {
        $content = file_get_contents($file);
        
        foreach ($translations as $msgid => $translated) {
            $content = preg_replace(
                '/(msgid "' . preg_quote($msgid, '/') . '")\s*msgstr "(.*?)"/',
                'msgid "' . $msgid . '"' . PHP_EOL . 'msgstr "' . $translated . '"',
                $content
            );
        }

        file_put_contents($file, $content);
    };

    foreach (glob($languagesDir . '/*.po') as $file) {
        // Extract the language code from the filename
        // Example: wpfluent-de_DE.po -> de
        $filename = basename($file, '.po');
        $parts = explode('-', $filename);
        $targetLanguage = end($parts);

        // Extract msgid entries from the .po file
        $msgids = $extractMsgids($file);

        // Translate the msgid entries
        $translations = $translateMsgids($msgids, $targetLanguage);

        // Update the .po file with translations
        $updatePoFile($file, $translations);
    }

    echo "Translation completed and saved to $languagesDir\n";

    // Generate .json files
    // echo "Generating JSON files...\n";
    // $jsonCommand = "$cliPath i18n make-json $languagesDir --no-purge 2>&1";
    // $output = shell_exec($jsonCommand);
    // echo $output . PHP_EOL;
};