<?php

namespace App\Services;

class States
{
    const LIST = [
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'DC' => 'District of Columbia',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'PR' => 'Puerto Rico',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'VI' => 'U.S. Virgin Islands',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
    ];

    const ABBREVIATIONS = [
        'AL' => [
            "al",
            "ala",
            "alabama"
        ],
        'AK' => [
            "ak",
            "alas",
            "alaska"
        ],
        'AZ' => [
            "az",
            "ari",
            "ariz",
            "arizona"
        ],
        'AR' => [
            "ar",
            "ark",
            "arkansas",
        ],
        'CA' => [
            "ca",
            "cf",
            "cal",
            "calif",
        ],
        'CO' => [
            "co",
            "cl",
            "col",
            "colo",
            "colorado",
        ],
        'CT' => [
            "ct",
            "con",
            "conn",
            "connecticut",
        ],
        'DE' => [
            "de",
            "dl",
            "del",
            "delaware",
        ],
        'DC' => [
            "dc",
            "washdc",
            "washingtondc",
            "washingtondistrictofcolumbia",
        ],
        'FL' => [
            "fl",
            "fla",
            "flo",
            "flor",
            "florida",
        ],
        'GA' => [
            "ga",
            "geo",
            "georgia",
        ],
        'HI' => [
            "ha",
            "hi",
            "haw",
            "hawaii",
        ],
        'ID' => [
            "id",
            "ida",
            "idaho",
        ],
        'IL' => [
            "il",
            "ill",
            "illi",
            "ills",
            "illinois",
        ],
        'IN' => [
            "in",
            "ind",
            "indiana",
        ],
        'IA' => [
            "ia",
            "io",
            "ioa",
            "iow",
            "iowa",
        ],
        'KS' => [
            "ka",
            "ks",
            "kan",
            "kansas",
        ],
        'KY' => [
            "ke",
            "ken",
            "kent",
            "ky",
            "kentucky",
        ],
        'LA' => [
            "la",
            "lo",
            "lou",
            "louisiana",
        ],
        'ME' => [
            "me",
            "mai",
            "maine",
        ],
        'MD' => [
            "md",
            "mar",
            "mary",
            "maryland",
        ],
        'MA' => [
            "ma",
            "mas",
            "mass",
            "ms",
            "massachusetts",
        ],
        'MI' => [
            "mi",
            "mc",
            "mic",
            "mich",
            "michigan",
        ],
        'MN' => [
            "mn",
            "min",
            "minn",
            "minnesota",
        ],
        'MS' => [
            "ms",
            "mi",
            "mis",
            "miss",
            "mississippi",
        ],
        'MO' => [
            "mo",
            "missouri",
        ],
        'MT' => [
            "mt",
            "mon",
            "mont",
            "montana",
        ],
        'NE' => [
            "ne",
            "nb",
            "neb",
            "nebr",
            "nebraska",
        ],
        'NV' => [
            "nv",
            "nev",
            "nevada",
        ],
        'NH' => [
            "nh",
            "nham",
            "nhampshire",
            "newhampshire",
        ],
        'NJ' => [
            "nj",
            "newj",
            "njersey",
            "newjersey",
        ],
        'NM' => [
            "nm",
            "newm",
            "newmex",
            "newmexico",
        ],
        'NY' => [
            "ny",
            "newyork",
        ],
        'NC' => [
            "nc",
            "ncar",
            "ncarolina",
            "northcarolina",
        ],
        'ND' => [
            "nd",
            "ndak",
            "ndakota",
            "northdakota",
        ],
        'OH' => [
            "oh",
            "ohio",
        ],
        'OK' => [
            "ok",
            "okl",
            "okla",
            "oklahoma",
        ],
        'OR' => [
            "or",
            "ore",
            "oreg",
            "oregon",
        ],
        'PA' => [
            "pa",
            "pe",
            "pen",
            "penn",
            "penna",
            "pennsylvania",
        ],
        'PR' => [
            "pr",
            "puertorico",
        ],
        'RI' => [
            "ri",
            "rhodeis",
            "rhodeisland",
        ],
        'SC' => [
            "sc",
            "scar",
            "scarolina",
            "southcarolina",
        ],
        'SD' => [
            "sd",
            "sdak",
            "sdakota",
            "southdakota",
        ],
        'TN' => [
            "tn",
            "ten",
            "tenn",
            "tennessee",
        ],
        'TX' => [
            "tx",
            "tex",
            "texas",
        ],
        'VI' => [
            "vi",
            "usvi",
            "usvirgins",
            "usvirginis",
            "usvirginislands",
        ],
        'UT' => [
            "ut",
            "uta",
            "utah",
        ],
        'VT' => [
            "vt",
            "ve",
            "ver",
            "vermont",
        ],
        'VA' => [
            "va",
            "vir",
            "virg",
            "virginia",
        ],
        'WA' => [
            "wa",
            "was",
            "wash",
            "wn",
            "washington",
        ],
        'WV' => [
            "wv",
            "wva",
            "wvirg",
            "wvirginia",
            "westvirginia",
        ],
        'WI' => [
            "wi",
            "wis",
            "ws",
            "wisc",
            "wisconsin",
        ],
        'WY' => [
            "wy",
            "wyo",
            "wyoming",
        ],
    ];

    public static function getState($state)
    {
        $uppercase_state = strtoupper(static::stripNonAlphabets($state));
        if (array_key_exists($uppercase_state, self::LIST)) {
            return $uppercase_state;
        }
        $names = array_map(function ($item) {
            return static::stripNonAlphabets(strtoupper($item));
        }, array_values(self::LIST));
        if (($index = array_search($uppercase_state, $names)) > -1) {
            return array_keys(self::LIST)[$index];
        }
        $found = array_first(self::ABBREVIATIONS, function ($abbreviations, $key) use ($uppercase_state) {
            return !!array_first($abbreviations, function ($abbr) use ($uppercase_state) {
                return strtoupper($abbr) === $uppercase_state;
            });
        });
        if ($found) {
            return static::stripNonAlphabets(strtoupper($found[0]));
        }
        return $state;
    }

    public static function stripNonAlphabets($str)
    {
        return str_strip_non_alphabets($str);
    }

    public static function validValues()
    {
        $abbreviations = [];
        foreach (self::ABBREVIATIONS as $item) {
            $abbreviations = array_merge($abbreviations, array_map(function ($i) {
                return strtoupper($i);
            }, $item));
        }
        return array_merge(array_keys(self::LIST), array_map(function ($item) {
            return strtoupper($item);
        }, array_values(self::LIST)), $abbreviations);
    }
}
