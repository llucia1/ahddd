<?php

namespace Fixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use GridCP\User\Infrastructure\DB\MySQL\Entity\CountryEntity;

class CountryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $countries = [
            ['Afghanistan', 'AF', 'AFG'], ['Albania', 'AL', 'ALB'], ['Germany', 'DE', 'DEU'], ['Andorra', 'AD', 'AND'],
            ['Angola', 'AO', 'AGO'], ['Anguilla', 'AI', 'AIA'], ['Antarctica', 'AQ', 'ATA'], ['Antigua and Barbuda', 'AG', 'ATG'],
            ['Saudi Arabia', 'SA', 'SAU'], ['Algeria', 'DZ', 'DZA'], ['Argentina', 'AR', 'ARG'], ['Armenia', 'AM', 'ARM'],
            ['Aruba', 'AW', 'ABW'], ['Australia', 'AU', 'AUS'], ['Austria', 'AT', 'AUT'], ['Azerbaijan', 'AZ', 'AZE'],
            ['Belgium', 'BE', 'BEL'], ['Bahamas', 'BS', 'BHS'], ['Bahrain', 'BH', 'BHR'], ['Bangladesh', 'BD', 'BGD'],
            ['Barbados', 'BB', 'BRB'], ['Belize', 'BZ', 'BLZ'], ['Benin', 'BJ', 'BEN'], ['Bhutan', 'BT', 'BTN'],
            ['Belarus', 'BY', 'BLR'], ['Myanmar', 'MM', 'MMR'], ['Bolivia', 'BO', 'BOL'], ['Bosnia and Herzegovina', 'BA', 'BIH'],
            ['Botswana', 'BW', 'BWA'], ['Brazil', 'BR', 'BRA'], ['Brunei', 'BN', 'BRN'], ['Bulgaria', 'BG', 'BGR'],
            ['Burkina Faso', 'BF', 'BFA'], ['Burundi', 'BI', 'BDI'], ['Cape Verde', 'CV', 'CPV'], ['Cambodia', 'KH', 'KHM'],
            ['Cameroon', 'CM', 'CMR'], ['Canada', 'CA', 'CAN'], ['Chad', 'TD', 'TCD'], ['Chile', 'CL', 'CHL'],
            ['China', 'CN', 'CHN'], ['Cyprus', 'CY', 'CYP'], ['Vatican City State', 'VA', 'VAT'], ['Colombia', 'CO', 'COL'],
            ['Comoros', 'KM', 'COM'], ['Republic of the Congo', 'CG', 'COG'], ['Democratic Republic of the Congo', 'CD', 'COD'],
            ['North Korea', 'KP', 'PRK'], ['South Korea', 'KR', 'KOR'], ['Ivory Coast', 'CI', 'CIV'], ['Costa Rica', 'CR', 'CRI'],
            ['Croatia', 'HR', 'HRV'], ['Cuba', 'CU', 'CUB'], ['Cura\u00e7ao', 'CW', 'CWU'], ['Denmark', 'DK', 'DNK'],
            ['Dominica', 'DM', 'DMA'], ['Ecuador', 'EC', 'ECU'], ['Egypt', 'EG', 'EGY'], ['El Salvador', 'SV', 'SLV'],
            ['United Arab Emirates', 'AE', 'ARE'], ['Eritrea', 'ER', 'ERI'], ['Slovakia', 'SK', 'SVK'], ['Slovenia', 'SI', 'SVN'],
            ['Spain', 'ES', 'ESP'], ['United States of America', 'US', 'USA'], ['Estonia', 'EE', 'EST'], ['Ethiopia', 'ET', 'ETH'],
            ['Philippines', 'PH', 'PHL'], ['Finland', 'FI', 'FIN'], ['Fiji', 'FJ', 'FJI'], ['France', 'FR', 'FRA'],
            ['Gabon', 'GA', 'GAB'], ['Gambia', 'GM', 'GMB'], ['Georgia', 'GE', 'GEO'], ['Ghana', 'GH', 'GHA'],
            ['Gibraltar', 'GI', 'GIB'], ['Grenada', 'GD', 'GRD'], ['Greece', 'GR', 'GRC'], ['Greenland', 'GL', 'GRL'],
            ['Guadeloupe', 'GP', 'GLP'], ['Guam', 'GU', 'GUM'], ['Guatemala', 'GT', 'GTM'], ['French Guiana', 'GF', 'GUF'],
            ['Guernsey', 'GG', 'GGY'], ['Guinea', 'GN', 'GIN'], ['Equatorial Guinea', 'GQ', 'GNQ'], ['Guinea-Bissau', 'GW', 'GNB'],
            ['Guyana', 'GY', 'GUY'], ['Haiti', 'HT', 'HTI'], ['Honduras', 'HN', 'HND'], ['Hong Kong', 'HK', 'HKG'],
            ['Hungary', 'HU', 'HUN'], ['India', 'IN', 'IND'], ['Indonesia', 'ID', 'IDN'], ['Iran', 'IR', 'IRN'],
            ['Iraq', 'IQ', 'IRQ'], ['Ireland', 'IE', 'IRL'], ['Bouvet Island', 'BV', 'BVT'], ['Isle of Man', 'IM', 'IMN'],
            ['Christmas Island', 'CX', 'CXR'], ['Norfolk Island', 'NF', 'NFK'], ['Iceland', 'IS', 'ISL'], ['Bermuda Islands', 'BM', 'BMU'],
            ['Cayman Islands', 'KY', 'CYM'], ['Cocos (Keeling) Islands', 'CC', 'CCK'], ['Cook Islands', 'CK', 'COK'],
            ['\u00c5land Islands', 'AX', 'ALA'], ['Faroe Islands', 'FO', 'FRO'], ['South Georgia and the South Sandwich Islands', 'GS', 'SGS'],
            ['Heard Island and McDonald Islands', 'HM', 'HMD'], ['Maldives', 'MV', 'MDV'], ['Falkland Islands (Malvinas)', 'FK', 'FLK'],
            ['Northern Mariana Islands', 'MP', 'MNP'], ['Marshall Islands', 'MH', 'MHL'], ['Pitcairn Islands', 'PN', 'PCN'],
            ['Solomon Islands', 'SB', 'SLB'], ['Turks and Caicos Islands', 'TC', 'TCA'], ['United States Minor Outlying Islands', 'UM', 'UMI'],
            ['Virgin Islands', 'VG', 'VGB'], ['United States Virgin Islands', 'VI', 'VIR'], ['Israel', 'IL', 'ISR'],
            ['Italy', 'IT', 'ITA'], ['Jamaica', 'JM', 'JAM'], ['Japan', 'JP', 'JPN'], ['Jersey', 'JE', 'JEY'],
            ['Jordan', 'JO', 'JOR'], ['Kazakhstan', 'KZ', 'KAZ'], ['Kenya', 'KE', 'KEN'], ['Kyrgyzstan', 'KG', 'KGZ'],
            ['Kiribati', 'KI', 'KIR'], ['Kuwait', 'KW', 'KWT'], ['Lebanon', 'LB', 'LBN'], ['Laos', 'LA', 'LAO'],
            ['Lesotho', 'LS', 'LSO'], ['Latvia', 'LV', 'LVA'], ['Liberia', 'LR', 'LBR'], ['Libya', 'LY', 'LBY'],
            ['Liechtenstein', 'LI', 'LIE'], ['Lithuania', 'LT', 'LTU'], ['Luxembourg', 'LU', 'LUX'], ['Mexico', 'MX', 'MEX'],
            ['Monaco', 'MC', 'MCO'], ['Macao', 'MO', 'MAC'], ['Macedonia', 'MK', 'MKD'], ['Madagascar', 'MG', 'MDG'],
            ['Malaysia', 'MY', 'MYS'], ['Malawi', 'MW', 'MWI'], ['Mali', 'ML', 'MLI'], ['Malta', 'MT', 'MLT'],
            ['Morocco', 'MA', 'MAR'], ['Martinique', 'MQ', 'MTQ'], ['Mauritius', 'MU', 'MUS'], ['Mauritania', 'MR', 'MRT'],
            ['Mayotte', 'YT', 'MYT'], ['Estados Federados de', 'FM', 'FSM'], ['Moldova', 'MD', 'MDA'], ['Mongolia', 'MN', 'MNG'],
            ['Montenegro', 'ME', 'MNE'], ['Montserrat', 'MS', 'MSR'], ['Mozambique', 'MZ', 'MOZ'], ['Namibia', 'NA', 'NAM'],
            ['Nauru', 'NR', 'NRU'], ['Nepal', 'NP', 'NPL'], ['Nicaragua', 'NI', 'NIC'], ['Niger', 'NE', 'NER'],
            ['Nigeria', 'NG', 'NGA'], ['Niue', 'NU', 'NIU'], ['Norway', 'NO', 'NOR'], ['New Caledonia', 'NC', 'NCL'],
            ['New Zealand', 'NZ', 'NZL'], ['Oman', 'OM', 'OMN'], ['Netherlands', 'NL', 'NLD'], ['Pakistan', 'PK', 'PAK'],
            ['Palau', 'PW', 'PLW'], ['Palestine', 'PS', 'PSE'], ['Panama', 'PA', 'PAN'], ['Papua New Guinea', 'PG', 'PNG'],
            ['Paraguay', 'PY', 'PRY'], ['Peru', 'PE', 'PER'], ['French Polynesia', 'PF', 'PYF'], ['Poland', 'PL', 'POL'],
            ['Portugal', 'PT', 'PRT'], ['Puerto Rico', 'PR', 'PRI'], ['Qatar', 'QA', 'QAT'], ['United Kingdom', 'GB', 'GBR'],
            ['Central African Republic', 'CF', 'CAF'], ['Czech Republic', 'CZ', 'CZE'], ['Dominican Republic', 'DO', 'DOM'],
            ['South Sudan', 'SS', 'SSD'], ['R\u00e9union', 'RE', 'REU'], ['Rwanda', 'RW', 'RWA'], ['Romania', 'RO', 'ROU'],
            ['Russia', 'RU', 'RUS'], ['Western Sahara', 'EH', 'ESH'], ['Samoa', 'WS', 'WSM'], ['American Samoa', 'AS', 'ASM'],
            ['Saint Barth\u00e9lemy', 'BL', 'BLM'], ['Saint Kitts and Nevis', 'KN', 'KNA'], ['San Marino', 'SM', 'SMR'],
            ['Saint Martin (French part)', 'MF', 'MAF'], ['Saint Pierre and Miquelon', 'PM', 'SPM'],
            ['Saint Vincent and the Grenadines', 'VC', 'VCT'], ['Ascensi\u00f3n y Trist\u00e1n de Acu\u00f1a', 'SH', 'SHN'],
            ['Saint Lucia', 'LC', 'LCA'], ['Sao Tome and Principe', 'ST', 'STP'], ['Senegal', 'SN', 'SEN'],
            ['Serbia', 'RS', 'SRB'], ['Seychelles', 'SC', 'SYC'], ['Sierra Leone', 'SL', 'SLE'], ['Singapore', 'SG', 'SGP'],
            ['Sint Maarten', 'SX', 'SMX'], ['Syria', 'SY', 'SYR'], ['Somalia', 'SO', 'SOM'], ['Sri Lanka', 'LK', 'LKA'],
            ['South Africa', 'ZA', 'ZAF'], ['Sudan', 'SD', 'SDN'], ['Sweden', 'SE', 'SWE'], ['Switzerland', 'CH', 'CHE'],
            ['Suriname', 'SR', 'SUR'], ['Svalbard and Jan Mayen', 'SJ', 'SJM'], ['Swaziland', 'SZ', 'SWZ'],
            ['Tajikistan', 'TJ', 'TJK'], ['Thailand', 'TH', 'THA'], ['Taiwan', 'TW', 'TWN'], ['Tanzania', 'TZ', 'TZA'],
            ['British Indian Ocean Territory', 'IO', 'IOT'], ['French Southern Territories', 'TF', 'ATF'],
            ['East Timor', 'TL', 'TLS'], ['Togo', 'TG', 'TGO'], ['Tokelau', 'TK', 'TKL'], ['Tonga', 'TO', 'TON'],
            ['Trinidad and Tobago', 'TT', 'TTO'], ['Tunisia', 'TN', 'TUN'], ['Turkmenistan', 'TM', 'TKM'],
            ['Turkey', 'TR', 'TUR'], ['Tuvalu', 'TV', 'TUV'], ['Ukraine', 'UA', 'UKR'], ['Uganda', 'UG', 'UGA'],
            ['Uruguay', 'UY', 'URY'], ['Uzbekistan', 'UZ', 'UZB'], ['Vanuatu', 'VU', 'VUT'], ['Venezuela', 'VE', 'VEN'],
            ['Vietnam', 'VN', 'VNM'], ['Wallis and Futuna', 'WF', 'WLF'], ['Yemen', 'YE', 'YEM'], ['Djibouti', 'DJ', 'DJI'],
            ['Zambia', 'ZM', 'ZMB'], ['Zimbabwe', 'ZW', 'ZWE']
        ];

        foreach ($countries as [$name, $code, $code3]) {
            $country = new CountryEntity();
            $country->setName($name);
            $country->setCode($code);
            $country->setCode3($code3);
            $manager->persist($country);
        }

        $manager->flush();
    }
}