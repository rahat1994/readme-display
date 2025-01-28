<?php

namespace ReadmeDisplay\App\Parser;

use ReadmeDisplay\App\Models\Model;
use ReadmeDisplay\Parsedown;
class PluginReadmeParser extends Parser
{
    public $parseDownParser;
    public function __construct(Parsedown $parser)
    {
        $this->parseDownParser = $parser;
    }
    public function parse($content)
    {

        // $this->parseDownParser->setBreaksEnabled(true);
        // $content = $this->parseDownParser->text($content);

        // return $body;
        $html = '';
        // return $content;
        $html = $this->parseTitle($content);
        $html .= $this->parseDescription($content);
        $html .= $this->parseInstallation($content);
        $html .= $this->parseScreenshots($content);
        $html .= $this->parseChangelog($content);
        $html .= $this->parseFaq($content);
        $html .= $this->parseContibutors($content);
        $html .= $this->parseTags($content);


        // return $this->parseDownParser $html;



        return $html;

    }

    public function parseTitle($content)
    {
        // Define the regular expression to find the Title
        $pattern = '/^===\s*(.*?)\s*===/m';

        // Perform a regular expression match
        if (preg_match($pattern, $content, $matches)) {
            // Extract the Title
            $title = trim($matches[1]);

            // Wrap the title in a div with the class "plugin-title"
            return '<div class="plugin-title">' . htmlspecialchars($title) . '</div>';
        }
        return '';

    }

    public static function parseDescription($content)
    {
        // Define the regular expression to find the Description section
        $pattern = '/==\s*Description\s*==\s*(.*?)(?=\n==|\z)/is';

        // Perform a regular expression match
        if (preg_match($pattern, $content, $matches)) {
            // Extract the Description section
            $description_section = trim($matches[1]);

            // Wrap the description in a div with the class "plugin-description"
            return '<div class="plugin-description">' . nl2br(htmlspecialchars($description_section)) . '</div>';
        }
        return '';
    }

    public static function parseInstallation($content)
    {
        // Define the regular expression to find the Installation section
        $pattern = '/==\s*Installation\s*==\s*(.*?)(?=\n==|\z)/is';

        // Perform a regular expression match
        if (preg_match($pattern, $content, $matches)) {
            // Extract the Installation section
            $installation_section = trim($matches[1]);

            // Wrap the installation instructions in a div with the class "plugin-installation"
            return '<div class="plugin-installation">' . nl2br(htmlspecialchars($installation_section)) . '</div>';
        }
        return '';

    }

    public static function parseScreenshots($content)
    {
        // Define the regular expression to find the Screenshots section
        $pattern = '/==\s*Screenshots\s*==\s*(.*?)(?=\n==|\z)/is';

        // Perform a regular expression match
        if (preg_match($pattern, $content, $matches)) {
            // Extract the Screenshots section
            $screenshots_section = trim($matches[1]);

            // Define the regular expression to find each screenshot entry
            $screenshot_pattern = '/\d+\.\s*\*\*(.*?)\*\*/';

            // Perform a regular expression match for each screenshot entry
            preg_match_all($screenshot_pattern, $screenshots_section, $screenshot_matches);

            $result = '';
            foreach ($screenshot_matches[1] as $screenshot) {
                $result .= '<div class="plugin-screenshot">' . htmlspecialchars($screenshot) . '</div>';
            }

            return $result;
        }
        return '';
    }

    public function parseChangelog($content)
    {
        // Define the regular expression to find the Changelog section
        $pattern = '/==\s*Changelog\s*==\s*(.*?)(?=\n==|\z)/is';

        // Perform a regular expression match
        if (preg_match($pattern, $content, $matches)) {
            // Extract the Changelog section
            $changelog_section = trim($matches[1]);

            // Define the regular expression to find each changelog entry
            $entry_pattern = '/=\s*(\d+\.\d+\.\d+)\s*=\s*(.*?)(?=\n=\s*\d+\.\d+\.\d+\s*=|\z)/is';

            // Perform a regular expression match for each changelog entry
            preg_match_all($entry_pattern, $changelog_section, $entry_matches, PREG_SET_ORDER);

            $result = '';
            foreach ($entry_matches as $entry) {
                $version = trim($entry[1]);
                $description = trim($entry[2]);
                $result .= '<div class="changelog-entry">';
                $result .= '<div class="changelog-version">' . htmlspecialchars($version) . '</div>';
                $result .= '<div class="changelog-description">' . nl2br(htmlspecialchars($description)) . '</div>';
                $result .= '</div>';
            }

            return $result;
        }
        return '';

    }

    public static function parseFaq($content)
    {
        // Define the regular expression to find the FAQ section
        $pattern = '/==\s*Frequently Asked Questions\s*==\s*(.*?)(?=\n==|\z)/is';

        // Perform a regular expression match
        if (preg_match($pattern, $content, $matches)) {
            // Extract the FAQ section
            $faq_section = trim($matches[1]);

            // Define the regular expression to find questions and answers
            $qa_pattern = '/=\s*(.*?)\s*=\s*(.*?)(?=\n=|\z)/is';

            // Perform a regular expression match for each question and answer
            preg_match_all($qa_pattern, $faq_section, $qa_matches, PREG_SET_ORDER);

            $result = '';
            foreach ($qa_matches as $qa) {
                $question = trim($qa[1]);
                $answer = trim($qa[2]);
                $result .= '<div class="faq-question">' . htmlspecialchars($question) . '</div>';
                $result .= '<div class="faq-answer">' . nl2br(htmlspecialchars($answer)) . '</div>';
            }

            return $result;
        }
        return '';

    }

    public static function parseContibutors($content)
    {
        // take the first line starting with contibutors and seprate them by comma and return html with span and class named `plugin-contributors`
        // Define the regular expression to find the contributors line
        $pattern = '/^Contributors:\s*(.+)$/m';

        // Perform a regular expression match
        if (preg_match($pattern, $content, $matches)) {
            // Extract the contributors line
            $contributors_line = $matches[1];

            // Split contributors by comma
            $contributors = explode(',', $contributors_line);

            // Trim whitespace from each contributor and wrap in a div
            $contributors_divs = array_map(function ($contributor) {
                return '<div class="plugin-contributor">' . trim($contributor) . '</div>';
            }, $contributors);

            // Join all divs into a single string
            return implode("", $contributors_divs);
        }
        return '';
    }

    public static function parseTags($content)
    {
        // take the first line starting with tags and seprate them by comma and return html with span and class named `plugin-tags`
        // Define the regular expression to find the tags line
        $pattern = '/^Tags:\s*(.+)$/m';

        // Perform a regular expression match
        if (preg_match($pattern, $content, $matches)) {
            // Extract the tags line
            $tags_line = $matches[1];

            // Split tags by comma
            $tags = explode(',', $tags_line);

            // Trim whitespace from each tag and wrap in a div
            $tags_divs = array_map(function ($tag) {
                return '<div class="plugin-tag">' . trim($tag) . '</div>';
            }, $tags);

            // Join all divs into a single string
            return implode("", $tags_divs);
        }
        return '';
    }
}
