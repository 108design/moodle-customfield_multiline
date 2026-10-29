<?php
/**
 * This file is part of Moodle - https://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle. If not, see <https://www.gnu.org/licenses/>.
 *
 */
// Copyright (C) 2026 Andreas Giesen. See LICENSE.md.
/**
 * @package customfield_multiline
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace customfield_multiline;
defined('MOODLE_INTERNAL') || die();

/** Semantic text only: no attributes, resources, styles or executable markup. */
final class limited_html {
    private const TAGS = ['strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'br', 'p'];
    private const DROP = ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'template',
        'form', 'input', 'button', 'textarea', 'select', 'video', 'audio', 'canvas', 'head'];

    public static function clean(string $html, bool $small = false): string {
        if ($html === '') {
            return '';
        }
        $document = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $loaded = $document->loadHTML('<?xml encoding="utf-8"?><html><body>' . $html . '</body></html>',
                LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            if (!$loaded) {
                return '';
            }
            $body = $document->getElementsByTagName('body')->item(0);
            return $body ? self::children($body, $small) : '';
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private static function children(\DOMNode $parent, bool $small, bool $insmall = false): string {
        $result = '';
        foreach ($parent->childNodes as $node) {
            if ($node instanceof \DOMText) {
                $result .= s($node->nodeValue);
            } else if ($node instanceof \DOMElement) {
                $tag = strtolower($node->tagName);
                if (in_array($tag, self::DROP, true)) {
                    continue;
                }
                $content = self::children($node, $small, $insmall || $tag === 'small');
                if ($tag === 'small' && $small && !$insmall) {
                    $result .= '<small>' . $content . '</small>';
                    continue;
                }
                if ($tag === 'li' && !in_array(strtolower($parent->nodeName), ['ul', 'ol'], true)) {
                    $tag = 'p';
                }
                if (in_array($tag, self::TAGS, true)) {
                    $result .= $tag === 'br' ? '<br>' : '<' . $tag . '>' . $content . '</' . $tag . '>';
                } else {
                    $result .= $content;
                }
            }
        }
        return $result;
    }
}
