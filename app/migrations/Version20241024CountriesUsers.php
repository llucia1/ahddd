<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241024CountriesUsers extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('countries')) {
            $this->addSql('CREATE TABLE IF NOT EXISTS countries (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                code VARCHAR(2) NOT NULL,
                code3 VARCHAR(3) NOT NULL
            )');

            $this->insertCountries();
        }

        if (!$schema->hasTable('user_countries')) {
            $this->addSql('CREATE TABLE IF NOT EXISTS user_countries (
                id BIGINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
                user_id BIGINT NOT NULL,
                country_id BIGINT NOT NULL,
                active BOOL NOT NULL DEFAULT TRUE,
                created_at DATETIME NOT NULL,
                updated_at DATETIME DEFAULT NULL,
                CONSTRAINT FK_USER_COUNTRIES_USER FOREIGN KEY (user_id) REFERENCES user(id),
                CONSTRAINT FK_USER_COUNTRIES_COUNTRY FOREIGN KEY (country_id) REFERENCES countries(id)
            )');
        }
    }

    public function down(Schema $schema) : void
    {
        if ($schema->hasTable('countries')) {
            $schema->dropTable('countries');
        }

    }

    private function insertCountries() : void
    {
        $countries = [
            1 =>   ['Afghanistan',  'AF',  'AFG'],2 =>   ['Albania',  'AL',  'ALB'],3 =>   ['Germany',  'DE',  'DEU'],4 =>   ['Andorra',  'AD',  'AND'],5 =>   ['Angola',  'AO',  'AGO'],6 =>   ['Anguilla',  'AI',  'AIA'],7 =>   ['Antarctica',  'AQ',  'ATA'],
            8 =>   ['Antigua and Barbuda',  'AG',  'ATG'],
            9 =>   ['Saudi Arabia',  'SA',  'SAU'],
            10 =>  ['Algeria',  'DZ',  'DZA'],
            11 =>  ['Argentina',  'AR',  'ARG'],
            12 =>  ['Armenia',  'AM',  'ARM'],
            13 =>  ['Aruba',  'AW',  'ABW'],
            14 =>  ['Australia',  'AU',  'AUS'],
            15 =>  ['Austria',  'AT',  'AUT'],
            16 =>  ['Azerbaijan',  'AZ',  'AZE'],
            17 =>  ['Belgium',  'BE',  'BEL'],18 =>  ['Bahamas',  'BS',  'BHS'],19 =>  ['Bahrain',  'BH',  'BHR'],20 =>  ['Bangladesh',  'BD',  'BGD'],21 =>  ['Barbados',  'BB',  'BRB'],22 =>  ['Belize',  'BZ',  'BLZ'],23 =>  ['Benin',  'BJ',  'BEN'],24 =>  ['Bhutan',  'BT',  'BTN'],25 =>  ['Belarus',  'BY',  'BLR'],26 =>  ['Myanmar',  'MM',  'MMR'],27 =>  ['Bolivia',  'BO',  'BOL'],28 =>  ['Bosnia and Herzegovina',  'BA',  'BIH'],29 =>  ['Botswana',  'BW',  'BWA'],30 =>  ['Brazil',  'BR',  'BRA'],31 =>  ['Brunei',  'BN',  'BRN'],32 =>  ['Bulgaria',  'BG',  'BGR'],33 =>  ['Burkina Faso',  'BF',  'BFA'],34 =>  ['Burundi',  'BI',  'BDI'],35 =>  ['Cape Verde',  'CV',  'CPV'],36 =>  ['Cambodia',  'KH',  'KHM'],37 =>  ['Cameroon',  'CM',  'CMR'],38 =>  ['Canada',  'CA',  'CAN'],39 =>  ['Chad',  'TD',  'TCD'],40 =>  ['Chile',  'CL',  'CHL'],41 =>  ['China',  'CN',  'CHN'],42 =>  ['Cyprus',  'CY',  'CYP'],43 =>  ['Vatican City State',  'VA',  'VAT'],44 =>  ['Colombia',  'CO',  'COL'],45 =>  ['Comoros',  'KM',  'COM'],46 =>  ['Republic of the Congo',  'CG',  'COG'],47 =>  ['Democratic Republic of the Congo',  'CD',  'COD'],48 =>  ['North Korea',  'KP',  'PRK'],49 =>  ['South Korea',  'KR',  'KOR'],50 =>  ['Ivory Coast',  'CI',  'CIV'],51 =>  ['Costa Rica',  'CR',  'CRI'],52 =>  ['Croatia',  'HR',  'HRV'],53 =>  ['Cuba',  'CU',  'CUB'],54 =>  ['Curaçao',  'CW',  'CWU'],55 =>  ['Denmark',  'DK',  'DNK'],56 =>  ['Dominica',  'DM',  'DMA'],57 =>  ['Ecuador',  'EC',  'ECU'],58 =>  ['Egypt',  'EG',  'EGY'],59 =>  ['El Salvador',  'SV',  'SLV'],60 =>  ['United Arab Emirates',  'AE',  'ARE'],61 =>  ['Eritrea',  'ER',  'ERI'],62 =>  ['Slovakia',  'SK',  'SVK'],63 =>  ['Slovenia',  'SI',  'SVN'],64 =>  ['Spain',  'ES',  'ESP'],65 =>  ['United States of America',  'US',  'USA'],66 =>  ['Estonia',  'EE',  'EST'],67 =>  ['Ethiopia',  'ET',  'ETH'],68 =>  ['Philippines',  'PH',  'PHL'],69 =>  ['Finland',  'FI',  'FIN'],70 =>  ['Fiji',  'FJ',  'FJI'],71 =>  ['France',  'FR',  'FRA'],72 =>  ['Gabon',  'GA',  'GAB'],73 =>  ['Gambia',  'GM',  'GMB'],74 =>  ['Georgia',  'GE',  'GEO'],75 =>  ['Ghana',  'GH',  'GHA'],76 =>  ['Gibraltar',  'GI',  'GIB'],77 =>  ['Grenada',  'GD',  'GRD'],78 =>  ['Greece',  'GR',  'GRC'],79 =>  ['Greenland',  'GL',  'GRL'],80 =>  ['Guadeloupe',  'GP',  'GLP'],81 =>  ['Guam',  'GU',  'GUM'],82 =>  ['Guatemala',  'GT',  'GTM'],83 =>  ['French Guiana',  'GF',  'GUF'],84 =>  ['Guernsey',  'GG',  'GGY'],85 =>  ['Guinea',  'GN',  'GIN'],86 =>  ['Equatorial Guinea',  'GQ',  'GNQ'],87 =>  ['Guinea-Bissau',  'GW',  'GNB'],88 =>  ['Guyana',  'GY',  'GUY'],89 =>  ['Haiti',  'HT',  'HTI'],90 =>  ['Honduras',  'HN',  'HND'],91 =>  ['Hong Kong',  'HK',  'HKG'],92 =>  ['Hungary',  'HU',  'HUN'],93 =>  ['India',  'IN',  'IND'],94 =>  ['Indonesia',  'ID',  'IDN'],95 =>  ['Iran',  'IR',  'IRN'],96 =>  ['Iraq',  'IQ',  'IRQ'],97 =>  ['Ireland',  'IE',  'IRL'],98 =>  ['Bouvet Island',  'BV',  'BVT'],99 =>  ['Isle of Man',  'IM',  'IMN'],100 => ['Christmas Island',  'CX',  'CXR'],101 => ['Norfolk Island',  'NF',  'NFK'],102 => ['Iceland',  'IS',  'ISL'],103 => ['Bermuda Islands',  'BM',  'BMU'],104 => ['Cayman Islands',  'KY',  'CYM'],105 => ['Cocos (Keeling) Islands',  'CC',  'CCK'],106 => ['Cook Islands',  'CK',  'COK'],107 => ['Åland Islands',  'AX',  'ALA'],108 => ['Faroe Islands',  'FO',  'FRO'],109 => ['South Georgia and the South Sandwich Islands',  'GS',  'SGS'],110 => ['Heard Island and McDonald Islands',  'HM',  'HMD'],111 => ['Maldives',  'MV',  'MDV'],112 => ['Falkland Islands (Malvinas)',  'FK',  'FLK'],113 => ['Northern Mariana Islands',  'MP',  'MNP'],114 => ['Marshall Islands',  'MH',  'MHL'],115 => ['Pitcairn Islands',  'PN',  'PCN'],116 => ['Solomon Islands',  'SB',  'SLB'],117 => ['Turks and Caicos Islands',  'TC',  'TCA'],118 => ['United States Minor Outlying Islands',  'UM',  'UMI'],119 => ['Virgin Islands',  'VG',  'VGB'],120 => ['United States Virgin Islands',  'VI',  'VIR'],121 => ['Israel',  'IL',  'ISR'],122 => ['Italy',  'IT',  'ITA'],123 => ['Jamaica',  'JM',  'JAM'],124 => ['Japan',  'JP',  'JPN'],125 => ['Jersey',  'JE',  'JEY'],126 => ['Jordan',  'JO',  'JOR'],127 => ['Kazakhstan',  'KZ',  'KAZ'],128 => ['Kenya',  'KE',  'KEN'],129 => ['Kyrgyzstan',  'KG',  'KGZ'],130 => ['Kiribati',  'KI',  'KIR'],131 => ['Kuwait',  'KW',  'KWT'],132 => ['Lebanon',  'LB',  'LBN'],133 => ['Laos',  'LA',  'LAO'],134 => ['Lesotho',  'LS',  'LSO'],135 => ['Latvia',  'LV',  'LVA'],136 => ['Liberia',  'LR',  'LBR'],137 => ['Libya',  'LY',  'LBY'],138 => ['Liechtenstein',  'LI',  'LIE'],139 => ['Lithuania',  'LT',  'LTU'],140 => ['Luxembourg',  'LU',  'LUX'],141 => ['Mexico',  'MX',  'MEX'],142 => ['Monaco',  'MC',  'MCO'],143 => ['Macao',  'MO',  'MAC'],144 => ['Macedonia',  'MK',  'MKD'],145 => ['Madagascar',  'MG',  'MDG'],146 => ['Malaysia',  'MY',  'MYS'],147 => ['Malawi',  'MW',  'MWI'],148 => ['Mali',  'ML',  'MLI'],149 => ['Malta',  'MT',  'MLT'],150 => ['Morocco',  'MA',  'MAR'],151 => ['Martinique',  'MQ',  'MTQ'],152 => ['Mauritius',  'MU',  'MUS'],153 => ['Mauritania',  'MR',  'MRT'],154 => ['Mayotte',  'YT',  'MYT'],155 => ['Estados Federados de',  'FM',  'FSM'],156 => ['Moldova',  'MD',  'MDA'],157 => ['Mongolia',  'MN',  'MNG'],158 => ['Montenegro',  'ME',  'MNE'],159 => ['Montserrat',  'MS',  'MSR'],160 => ['Mozambique',  'MZ',  'MOZ'],161 => ['Namibia',  'NA',  'NAM'],162 => ['Nauru',  'NR',  'NRU'],163 => ['Nepal',  'NP',  'NPL'],164 => ['Nicaragua',  'NI',  'NIC'],165 => ['Niger',  'NE',  'NER'],166 => ['Nigeria',  'NG',  'NGA'],167 => ['Niue',  'NU',  'NIU'],168 => ['Norway',  'NO',  'NOR'],169 => ['New Caledonia',  'NC',  'NCL'],170 => ['New Zealand',  'NZ',  'NZL'],171 => ['Oman',  'OM',  'OMN'],172 => ['Netherlands',  'NL',  'NLD'],173 => ['Pakistan',  'PK',  'PAK'],174 => ['Palau',  'PW',  'PLW'],175 => ['Palestine',  'PS',  'PSE'],176 => ['Panama',  'PA',  'PAN'],177 => ['Papua New Guinea',  'PG',  'PNG'],178 => ['Paraguay',  'PY',  'PRY'],179 => ['Peru',  'PE',  'PER'],180 => ['French Polynesia',  'PF',  'PYF'],181 => ['Poland',  'PL',  'POL'],182 => ['Portugal',  'PT',  'PRT'],183 => ['Puerto Rico',  'PR',  'PRI'],184 => ['Qatar',  'QA',  'QAT'],185 => ['United Kingdom',  'GB',  'GBR'],186 => ['Central African Republic',  'CF',  'CAF'],187 => ['Czech Republic',  'CZ',  'CZE'],188 => ['Dominican Republic',  'DO',  'DOM'],189 => ['South Sudan',  'SS',  'SSD'],190 => ['Réunion',  'RE',  'REU'],191 => ['Rwanda',  'RW',  'RWA'],192 => ['Romania',  'RO',  'ROU'],193 => ['Russia',  'RU',  'RUS'],194 => ['Western Sahara',  'EH',  'ESH'],195 => ['Samoa',  'WS',  'WSM'],196 => ['American Samoa',  'AS',  'ASM'],197 => ['Saint Barthélemy',  'BL',  'BLM'],198 => ['Saint Kitts and Nevis',  'KN',  'KNA'],199 => ['San Marino',  'SM',  'SMR'],200 => ['Saint Martin (French part)',  'MF',  'MAF'],201 => ['Saint Pierre and Miquelon',  'PM',  'SPM'],202 => ['Saint Vincent and the Grenadines',  'VC',  'VCT'],203 => ['Ascensión y Tristán de Acuña',  'SH',  'SHN'],204 => ['Saint Lucia',  'LC',  'LCA'],205 => ['Sao Tome and Principe',  'ST',  'STP'],206 => ['Senegal',  'SN',  'SEN'],207 => ['Serbia',  'RS',  'SRB'],208 => ['Seychelles',  'SC',  'SYC'],209 => ['Sierra Leone',  'SL',  'SLE'],210 => ['Singapore',  'SG',  'SGP'],211 => ['Sint Maarten',  'SX',  'SMX'],212 => ['Syria',  'SY',  'SYR'],213 => ['Somalia',  'SO',  'SOM'],214 => ['Sri Lanka',  'LK',  'LKA'],215 => ['South Africa',  'ZA',  'ZAF'],216 => ['Sudan',  'SD',  'SDN'],217 => ['Sweden',  'SE',  'SWE'],218 => ['Switzerland',  'CH',  'CHE'],219 => ['Suriname',  'SR',  'SUR'],220 => ['Svalbard and Jan Mayen',  'SJ',  'SJM'],221 => ['Swaziland',  'SZ',  'SWZ'],222 => ['Tajikistan',  'TJ',  'TJK'],223 => ['Thailand',  'TH',  'THA'],224 => ['Taiwan',  'TW',  'TWN'],
            225 => ['Tanzania',  'TZ',  'TZA'],
            226 => ['British Indian Ocean Territory',  'IO',  'IOT'],
            227 => ['French Southern Territories',  'TF',  'ATF'],
            228 => ['East Timor',  'TL',  'TLS'],
            229 => ['Togo',  'TG',  'TGO'],
            230 => ['Tokelau',  'TK',  'TKL'],
            231 => ['Tonga',  'TO',  'TON'],
            232 => ['Trinidad and Tobago',  'TT',  'TTO'],
            233 => ['Tunisia',  'TN',  'TUN'],
            234 => ['Turkmenistan',  'TM',  'TKM'],
            235 => ['Turkey',  'TR',  'TUR'],
            236 => ['Tuvalu',  'TV',  'TUV'],
            237 => ['Ukraine',  'UA',  'UKR'],
            238 => ['Uganda',  'UG',  'UGA'],
            239 => ['Uruguay',  'UY',  'URY'],
            240 => ['Uzbekistan',  'UZ',  'UZB'],
            241 => ['Vanuatu',  'VU',  'VUT'],
            242 => ['Venezuela',  'VE',  'VEN'],
            243 => ['Vietnam',  'VN',  'VNM'],
            244 => ['Wallis and Futuna',  'WF',  'WLF'],
            245 => ['Yemen',  'YE',  'YEM'],
            246 => ['Djibouti',  'DJ',  'DJI'],
            247 => ['Zambia',  'ZM',  'ZMB'],
            248 => ['Zimbabwe',  'ZW',  'ZWE'],
            
        ];

        foreach ($countries as $country) {
            $this->addSql('INSERT INTO countries (name, code, code3) VALUES (?, ?, ?)', [$country[0], $country[1], $country[2]]);
        }
    }







    
}
