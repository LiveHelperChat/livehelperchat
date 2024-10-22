<?php

namespace LiveHelperChat\Helpers;

use Michelf\Markdown;

class LHCMarkdown extends Markdown
{
    protected array $block_gamut = array(
        /*"doHeaders"         => 10,
        "doHorizontalRules" => 20,
        "doLists"           => 40,
        "doCodeBlocks"      => 50,
        "doBlockQuotes"     => 60,*/
    );

    protected array $span_gamut = array(
        // Process character escapes, code spans, and inline HTML
        // in one shot.
        //"parseSpan"           => -30,
        // Process anchor and image tags. Images must come first,
        // because ![foo][f] looks like an anchor.
         //"doImages"            =>  10,
         //"doAnchors"           =>  20,
        // Make links out of things like `<https://example.com/>`
        // Must come after doAnchors, because you can use < and >
        // delimiters in inline links like [this](<url>).
         //"doAutoLinks"         =>  30,
        // "encodeAmpsAndAngles" =>  40,
        "doItalicsAndBold"    =>  50,
        //"doHardBreaks"        =>  60,
    );

    protected function runBasicBlockGamut($text) {

        foreach ($this->block_gamut as $method => $priority) {
            $text = $this->$method($text);
        }

        // Finally form paragraph and restore hashed blocks.
        $text = $this->formParagraphs($text, false); // We do not want paragraphs

        return $text;
    }

    public static function defaultTransform(string $text): string {
        // Take parser class on which this function was called.
        $parser_class = static::class;

        // Try to take parser from the static parser list
        static $parser_list;
        $parser =& $parser_list[$parser_class];

        // Create the parser it not already set
        if (!$parser) {
            $parser = new $parser_class;
        }

        // Transform text using parser.
        return $parser->transform($text);
    }
}
 