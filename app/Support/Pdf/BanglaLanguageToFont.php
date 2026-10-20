<?php

namespace App\Support\Pdf;

use Mpdf\Language\LanguageToFont;

/**
 * mPDF language → font map that renders Bengali with Kalpurush (resources/fonts)
 * instead of the bundled FreeSerif, whose bold face has no Bengali glyphs.
 *
 * Modern Noto Sans Bengali builds are NOT usable here: mPDF's OpenType parser
 * rejects their GPOS tables. Kalpurush / SolaimanLipi / Nikosh all work.
 */
class BanglaLanguageToFont extends LanguageToFont
{
    public const FONT = 'kalpurush';

    public function getLanguageOptions($llcc, $adobeCJK)
    {
        $lang = strtolower(explode('-', (string) $llcc)[0]);

        if (in_array($lang, ['bn', 'ben', 'as', 'asm'], true)) {
            return [false, self::FONT];
        }

        return parent::getLanguageOptions($llcc, $adobeCJK);
    }
}
