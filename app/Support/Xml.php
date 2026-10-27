<?php

namespace App\Support;

use DOMDocument;
use DOMElement;

/**
 * Helpers for the XML documents the site generates by hand (sitemap, RSS feed).
 */
final class Xml
{
    /**
     * Create an element whose text content is escaped exactly once.
     *
     * DOMDocument::createElement() runs its second argument through the entity
     * parser, so a bare "&" raises "unterminated entity reference" and silently
     * drops the whole value. Appending a text node escapes it correctly instead.
     */
    public static function element(DOMDocument $dom, string $name, string $value): DOMElement
    {
        $element = $dom->createElement($name);
        $element->appendChild($dom->createTextNode($value));

        return $element;
    }
}
