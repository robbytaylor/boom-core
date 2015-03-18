<?php

namespace Boom\TextFilter\Filter;

use \Embera\Embera as Embera;

class OEmbed implements \Boom\TextFilter\Filter
{
    /**
     * Uses Embera to find any embeddable links within the text.
     *
     * Then replaces URLs with embedded content as long as:
     *      * It doesn't appear within double or single quotes (' or ").
     *      * Doesn't have a closing anchor tag after it (</a>).
     *
     * @param string $text
     * @return string
     */
    public function filterText($text)
    {
        $embera = new Embera();

        if ($data = $embera->getUrlInfo($text)) {
            $table = array();
            foreach ($data as $url => $service) {
                if ( ! empty($service['html'])) {
                    $table[$url] = $service['html'];
                }
            }

            foreach ($table as $url => $replacement) {
                $text = preg_replace('~(?<![\'\"])'. preg_quote($url) . '(?![\'\"])(?!\</a\>)~', $replacement, $text);
            }
        }

        return $text;
    }
}