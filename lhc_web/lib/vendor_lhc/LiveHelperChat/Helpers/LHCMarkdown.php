<?php

namespace LiveHelperChat\Helpers;

use Michelf\Markdown;

class LHCMarkdown extends Markdown
{
    protected array $block_gamut = array(
        "doHeaders"         => 10,
        /*"doHorizontalRules" => 20,
        /*"doLists"           => 40,
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

    protected function doHeaders($text) {
        /**
         * atx-style headers:
         *   # Header 1
         *   ## Header 2
         *   ## Header 2 with closing hashes ##
         *   ...
         *   ###### Header 6
         */
        $text = preg_replace_callback('{
				^(\#{2,6})	# $1 = string of #\'s
				[ ]*
				(.+?)		# $2 = Header text
				[ ]*
				\#*			# optional closing #\'s (not counted)
				\n+
			}xm',
            array($this, '_doHeaders_callback_atx'), $text);

        return $text;
    }

    protected function _doHeaders_callback_atx($matches) {
        // ID attribute generation
        $idAtt = $this->_generateIdFromHeaderValue($matches[2]);

        $level = strlen($matches[1]);
        $levelClass = [
            1 => 'fs25',
            2 => 'fs19',
            3 => 'fs18',
            4 => 'fs17',
            5 => 'fs16',
            6 => 'fs16',
        ];
        $block = "<h$level$idAtt class=\"". (isset($levelClass[$level]) ? $levelClass[$level] : 'fs16') ."\">".$this->runSpanGamut($matches[2])."</h$level>";
        return $this->hashBlock($block);
    }

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
 