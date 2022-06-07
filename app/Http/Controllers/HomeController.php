<?php

namespace App\Http\Controllers;

use App\Services\Mail\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PageBoundaries;

class HomeController extends Controller
{
    private $pageCount = 2;
    CONST SCORE_VALUES = [
        'STRUCTURE' => [
            'graph' => true,
            'key' => 'score_structurelehulpbronnen',
            'pageTitle' => 'Structurele hulpbronnen op je werk uitbreiden',
            'pageText' => [
                'Als professional ben je alleen in staat om je werk goed te doen wanneer je over de juiste',
                'vaardigheden beschikt.Vaardigheden ontwikkel je niet alleen met het vergaren van kennis of',
                'door het volgen van een cursus, maar ook door actief het contact op te zoeken met collega\'s.',
                'Brainstormen, het uitwisselen van feedback en het delen van ervaringen leveren inzichten op ',
                'die je direct kunt toepassen op je eigen werkwijze. Het inschakelen van collega\'s, mentoren',
                'of begeleiders noemen we in de psychologie ook wel structurele hulpbronnen: personen die ',
                'ervoor zorgen dat je niet alleen beter wordt in je vak, maar óók een belangrijke bijdrage ',
                'leveren aan jouw ontwikkeling en werkplezier. De scores bij dit onderdeel gaan over het ',
                'activeren van deze structurele hulpbronnen. Bekijk snel je resultaat!'
            ],
            'benchmark' => 21,
            'text' => [
                'equal' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score is gelijk aan de benchmark. Dit houdt in dat je voldoende variatie ervaart in je dagelijkse',
                        'werkzaamheden, maar dat er zeker nog ruimte is voor verbetering. Hoe zou je sommige taken',
                        'anders aan kunnen pakken? Is er een manier om de werkzaamheden effectiever of op een',
                        'leukere manier aan te pakken?  Zoek actief het contact met collega\'s of je begeleider op en vraag',
                        'hoe ze hiermee omgaan en of ze nog ideeën hebben. Onderzoek ook andere hulpbronnen binnen',
                        'de organisatie, bijvoorbeeld de mogelijkheid tot het volgen van (interne) trainingen of cursussen.',
                    ]
                ],
                'less' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score is lager dan de benchmark. Daaruit blijkt dat je voor je gevoel onvoldoende afwisseling',
                        'ervaart bij het uitvoeren van je werkzaamheden. Om je werkplezier te verhogen is het zaak om ',
                        'ervoor te zorgen dat je meer variatie aanbrengt en je werkzaamheden. Een goede manier om dit te ',
                        'doen is door actief het contact op te zoeken met collega\'s. Hoe zorgen ze voor voldoende ',
                        'afwisseling? Wat doen ze anders en wat kun jij daarvan leren? Kijk ook of er binnen de ',
                        'organisatie andere mogelijkheden zijn om de dagen afwisselender te maken, bijvoorbeeld met het',
                        'volgen van (interne) trainingen of cursussen om je manier van werken te verbeteren.'
                    ]
                ],
                'greater' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score is hoger dan de benchmark. Op basis van de door jou gegeven antwoorden stellen we ',
                        'vast dat je meer dan genoeg variatie in je werkzaamheden ervaart. Om jezelf extra uit te dagen',
                        'kun je je sterke en zwakke punten analyseren en bedenken of je bepaalde taken op een andere',
                        'manier aan zou kunnen pakken. Maak gebruik van (aanvullende) trainingsmogelijkheden om',
                        'vaardigheden te verbeteren en ga in gesprek met mensen die je andere inzichten kunnen geven.',
                        'Dit kunnen collega\'s zijn, maar bijvoorbeeld ook mentoren of mensen buiten de organisatie.',
                    ]
                ]
            ]
        ],
        'SOCIAL' => [
            'graph' => true,
            'key' => 'score_socialehulpbronnen',
            'pageTitle' => 'Sociale hulpbronnen op je werk uitbreiden',
            'pageText' => [
                'Wie zou jou binnen de organisatie kunnen helpen om meer werkplezier te ervaren? En wat',
                'heb je van die persoon nodig? In dit onderdeel verkennen we de mogelijkheden met',
                'betrekking tot het krijgen van feedback of steun van collega\'s, begeleiders of mentoren.',
                'Dit onderdeel hangt samen met twee belangrijke componenten van werkplezier:',
                'persoonlijke ontwikkeling en je verbonden voelen met andere mensen binnen de organisatie.',
                'Door actief die verbinding op te zoeken investeer je dus niet alleen in het beter worden',
                'in je functie, maar óók in het welzijn van jou als persoon.'
            ],
            'benchmark' => 16,
            'text' => [
                'equal' => [
                    'heading' => '',
                    'paragraph' => [
                        'Uit je score blijkt dat je regelmatig het gevoel hebt dat je er alleen voor staat. Het zorgt er',
                        'echter niet voor dat je je taken onvoldoende kunt uitvoeren. Bij taken die je zelfstandig',
                        'uitvoert twijfel je soms of je het wel goed doet. Doordat je wordt opgeslokt door de waan van',
                        'de dag voel je niet de ruimte om hulp in te schakelen van een collega of begeleider. Je vraagt',
                        'dus geen hulp wanneer het nodig is terwijl je wel weet dat er genoeg deskundigheid aanwezig is',
                        'om mee te sparren of advies te vragen. Om je vaardigheden als professional te verbeteren én',
                        'om meer werkplezier te ervaren adviseren we om juist wel het contact op te zoeken. Collega’s',
                        'of begeleiders willen vaak met alle plezier feedback en suggesties geven. Je moet er alleen',
                        'wel om vragen.'
                    ]
                ],
                'less' => [
                    'heading' => '',
                    'paragraph' => [
                        'Uit je score blijkt dat je regelmatig het gevoel hebt dat je er alleen voor staat. Bij taken die je',
                        'zelfstandig uitvoert twijfel je soms of je het wel goed doet. Doordat je wordt opgeslokt door de',
                        'waan van de dag voel je niet de ruimte om hulp in te schakelen van een collega of begeleider.',
                        'Ook kan het zijn dat niet duidelijk is aan wie je bijvoorbeeld advies of begeleiding zou kunnen',
                        'vragen. Het zorgt voor twijfel en onzekerheid, met een afname van werkplezier tot gevolg. Uit de',
                        'score blijkt dat je niet, of onvoldoende, tot actie overgaat. We adviseren om dit juist wél te doen.',
                        'Collega\'s willen vaak met alle plezier feedback en suggesties geven. Vraag het dus gerust!',
                    ]
                ],
                'greater' => [
                    'heading' => '',
                    'paragraph' => [
                        'Uit je score blijkt dat je regelmatig het gevoel hebt dat je er alleen voor staat. Dat realiseer je je',
                        'en ervaart daarbij geen negatieve effecten. Bij sommige taken die je zelfstandig uitvoert vraag je je',
                        'soms af of je het wel goed doet. Je maakt tijd vrij om feedback te vragen aan collega\'s over jouw',
                        'manier van werken. Je vraagt hulp wanneer je dat nodig acht en merkt dat je energie krijgt van de ',
                        'feedback en het contact met anderen. Je kunt jezelf nog meer uitdagen door kritische vragen voor te',
                        'leggen aan collega\'s. Een voorbeeld: \'wat zijn zaken die ik écht beter kan doen?\' Ook kun je',
                        'eventueel periodiek evaluaties inplannen met collega\'s, je begeleider of leidinggevende.'
                    ]
                ]
            ]
        ],
        'EXPANDING' => [
            'graph' => true,
            'key' => 'score_uitdagendeeisen',
            'pageTitle' => 'Uitdagende eisen op je werk uitbreiden',
            'pageText' => [
                'Uit onderzoek blijkt dat hoe meer uitdaging professionals in hun werk ervaren, hoe meer',
                'werkplezier ze ervaren. Het gevoel ergens helemaal in op te kunnen gaan, een gevoel dat',
                'ook wel bekend staat als \'flow\', zorgt voor voldoening én levert ook nog eens betere ',
                'prestaties op. Hoe zit het met jouw uitdaging binnen je huidige functie? De scores bij ',
                'dit onderdeel laten zien hoe het met de huidige uitdaging binnen jouw werkzaamheden is ',
                'gesteld en gaan over het actief op zoek gaan naar meer uitdaging in je werk.'
            ],
            'benchmark' => 17,
            'text' => [
                'equal' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je ervaart progressie en vooruitgang in je ontwikkeling en werkzaamheden, maar voelt dat er nog ',
                        'ruimte is voor verbetering. Het risico bestaat dat je onvoldoende energie haalt uit je huidige ',
                        'werkzaamheden omdat je bij vlagen die uitdaging mist. We adviseren om actief de uitdaging op te ',
                        'zoeken. Neem bijvoorbeeld deel aan nieuwe, uitdagende projecten en neem daarbij het voortouw.',
                        'Daag jezelf uit door de diepte in te gaan en nieuwe ervaringen op te doen waarbij je je ',
                        'vaardigheden aan moet spreken. Analyseer ook je huidige werkzaamheden en zoek naar manieren',
                        'om die eventueel afwisselender of uitdagender te maken. Zoek de grenzen van de werkdruk op en',
                        'onderzoek wat er voor jou nodig is om \'flow\' te ervaren in je werk.'
                    ]
                ],
                'less' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je hebt het idee dat je ontwikkeling tot stilstand komt en je op dit moment onvoldoende uitdaging',
                        'ervaart in je werk. Uit de score blijkt dat je hier wel behoefte aan hebt, maar onvoldoende actie',
                        'onderneemt om meer uitdaging te realiseren. Je bent wellicht wat terughoudend met het nemen van ',
                        'initiatieven en het aangaan van nieuwe project omdat dit kan leiden tot een hogere werkdruk. Het ',
                        'risico bestaat echter dat je onvoldoende energie haalt uit je huidige werkzaamheden, juist omdat je',
                        'die uitdaging en een zekere mate van urgentie mist. We adviseren om spelenderwijs de grens van',
                        'wat voor jou werkt op te zoeken. Onderzoek taken en projecten, en bepaal voor jezelf waar voor jou',
                        'de uitdaging ligt en welk werktempo daarbij past. Daag jezelf uit door meer uitdaging aan te gaan.',
                        'Daar ligt de sleutel naar meer voldoening en werkplezier. '
                    ]
                ],
                'greater' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je ervaart progressie en vooruitgang in je ontwikkeling en werkzaamheden, maar voelt dat er nog ',
                        'ruimte is voor verbetering. Je neemt initiatief om te zorgen voor meer uitdaging, maar vind dat',
                        'er nog meer uit te halen valt. Doe je dit niet, bestaat het risico dat het werkplezier zal',
                        'afnemen. Op basis van je score adviseren we om meer initiatief te tonen en actief te zoeken naar',
                        'meer uitdaging. Meld je bijvoorbeeld aan voor nieuwe en uitdagende projecten, analyseer of je je',
                        'huidige werkzaamheden uitdagender kunt maken en schroom daarbij ook niet om de grenzen op te ',
                        'zoeken van wat voor jou werkt. Welke werkzaamheden geven energie en wat is een fijn',
                        'werktempo? Dat is waardevol omdat je op die manier je meest ideale werkritme ofwel \'flow\' ontdekt.'
                    ]
                ]
            ]
        ],
        'IMPEDING' => [
            'graph' => true,
            'key' => 'score_verwerk',
            'pageTitle' => 'Belemmerende eisen op je werk verminderen',
            'pageText' => [
                'Je minder betrokken voelen bij werkzaamheden of projecten, steeds vaker pauzeren of ander',
                'werkontwijkend gedrag vertonen: het kan erop duiden dat je mate van werkplezier sterk aan',
                'het afnemen is. Wat zijn de belemmerende factoren die tussen jou en die uitdagende baan ',
                'instaan? De scores bij dit onderdeel geven aan hoe het met jouw motivatie, betrokkenheid',
                'en werkplezier is gesteld.'
            ],
            'benchmark' => 14,
            'text' => [
                'equal' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score voor belemmerende eisen op het werk verminderen is gelijk aan de benchmark. Dit houdt',
                        'in dat er ruimte is voor verbetering. Je score laat zien dat je het contact met collega\'s soms',
                        'uit de weg gaat, sommige werkzaamheden ontwijkt of snel bent afgeleid. Je voelt dat het ',
                        'werkplezier bij vlagen afneemt en je soms moeite ervaart om je ergens toe te zetten. Je hebt',
                        'het gevoel dat je veel op de automatische piloot werkt en dat de dagen wel erg veel op elkaar ',
                        'lijken. Het zorgt ervoor dat werkdagen gevoelsmatig lang duren. Een goed en inhoudelijk gesprek',
                        'met collega\'s geeft je energie, net zoals deelname aan een uitdagend project waarbij je ',
                        'cognitieve vermogen wordt aangesproken. Dit komt echter onvoldoende voor. Blijf je bewust van',
                        'deze zaken die je energie geven en probeer daar ook naar te handelen.'
                    ]
                ],
                'less' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score voor belemmerende eisen op het werk is lager dan de benchmark. Dat is, in tegenstelling',
                        'tot de scores bij de andere vragen, goed nieuws! Je score laat zien dat je zelfstandig op zoek',
                        'gaat naar contact met collega\'s en actief de uitdaging opzoekt. Taken en mensen die juist ',
                        'energie kosten, vermijd je zoveel mogelijk. Je hebt het gevoel dat je je in je huidige baan kunt',
                        'ontwikkelen als professional en krijgt energie van de werkzaamheden. Het zorgt ervoor dat de',
                        'dagen gevoelsmatig voorbij vliegen. Een inspirerend gesprek met een collega, een uitdagende taak',
                        'en een dynamische werkdag: het zijn zaken die je veel voldoening en werkplezier geven.'
                    ]
                ],
                'greater' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score voor belemmerende eisen op het werk verminderen is hoog vergeleken met de benchmark.',
                        'Een hoge score geeft aan dat je mogelijk mensen en taken bewust of onbewust uit de weg gaat.',
                        'Je hebt het idee dat je op de automatische piloot werkt en dat er weinig gebeurt. Wellicht valt',
                        'het je op dat je snel wordt afgeleid, langer pauzes neemt of dat je je stoort aan collega’s. ',
                        'Als mensen hun plezier en energie voelen wegvloeien dan zien we dat ze (moeilijke) taken niet',
                        'meer aanpakken, taken uitstellen of zelfs op het beloop laten en gesprekken met collega’s uit',
                        'de weg gaan. Dit is mogelijk bij jou ook het geval. Herkenbaar? Op dit vlak valt voor jou nog',
                        'de nodige winst te behalen.'
                    ]
                ]
            ]
        ],
        'ENJOY' => [
            'graph' => true,
            'key' => 'score_werkplezier',
            'pageTitle' => 'Plezier in je werk',
            'pageText' => [
                'Plezier in je werk: wat is dat eigenlijk? Wanneer we het hebben over werkplezier bedoelen',
                'we volgende: een positieve psychologische toestand van opperste voldoening die',
                'gekenmerkt wordt door bruisen van energie, je sterk en fit voelen en het beschikken over',
                'mentale veerkracht en doorzettingsvermogen. Het stelt je in staat om hard te werken, goede',
                'prestaties te leveren en daar energie, plezier en voldoening aan te ontleden. Hoe is het',
                'gesteld met jouw werkplezier? De scores bij dit onderdeel brengen jouw huidige staat van',
                'werkplezier in kaart.'
            ],
            'benchmark' => 51,
            'text' => [
                'equal' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score laat zien dat je weliswaar plezier ervaart in je baan, maar dat dit gepaard gaat met',
                        'golfbewegingen. Er zijn periodes waarin je je fit voelt en werkt met energie en uitdaging, maar die',
                        'fases worden afgewisseld met periodes waarin de motivatie ver te zoeken is, je het werk niet als ',
                        'zinvol ervaart en je het werk en het contact met collega\'s liever uit de weg gaat. Ook betrap je ',
                        'jezelf erop dat je vaker op je telefoon zit, sneller afgeleid bent en je minder betrokken voelt bij',
                        'de organisatie. In zo\'n periode kost het je moeite om daar weer \'uit\' te komen. Je voelt de energie',
                        'en daarmee ook het werkplezier afnemen. Herkenbaar? Dan is het zaak om te onderzoeken hoe je',
                        'op een regelmatige manier het plezier weer terugbrengt in je werk. Dit kun je zelfstandig doen,',
                        'met behulp van collega\'s of bijvoorbeeld met behulp van de training De Psychologie van',
                        'Werkplezier, waarbij je tot de kern komt van waarom jij doet wat je doet.'
                    ]
                ],
                'less' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score laat zien dat je momenteel onvoldoende plezier ervaart in je baan. De definitie van',
                        'werkplezier, het krijgen van energie van je werk en voldoende uitdaging ervaren, is bij jou ',
                        'onvoldoende het geval. Je voelt je regelmatig vermoeid en niet fit. Het kost je moeite om je',
                        'werk goed en geconcentreerd te doen en ook het contact met collega\'s gaat niet vanzelf. Je ',
                        'ervaar je werk niet als zinvol, inspirerend of uitdagend. Dat zorgt ervoor dat je weinig ',
                        'energie krijgt van je werkzaamheden, waardoor de motivatie afneemt. Het enthousiasme dat je',
                        'voelde toen je aan deze baan begon, is nu even niet aanwezig. Verandering is nodig. Ga het ',
                        'gesprek aan met je leidinggevende of onderzoek wat de verdiepende training De Psychologie van',
                        'Werkplezier je op dit vlak kan bieden.'
                    ]
                ],
                'greater' => [
                    'heading' => '',
                    'paragraph' => [
                        'Gefeliciteerd! Je score laat zien dat je momenteel een grote mate van werkplezier ervaart in ',
                        'je huidige baan. Natuurlijk heb je bij vlagen last van \'mindere\' dagen, maar voor het overgrote',
                        'deel ervaar je werkdagen waarin je je fit voelt en voldoende uitdaging en ontwikkeling ervaart.',
                        'Je haalt veel plezier uit je werk, maar uit met het contact met collega\'s. Je bent toegewijd en',
                        'betrokken en hebt het gevoel dat je een wezenlijke bijdrage levert aan het bedrijfsresultaat. Je',
                        'ervaart je werk als nuttig, zinvol en inspirerend en uitdagend. Ook ervaar je voldoende variatie.',
                        'Het woord \'flow\', waarbij je helemaal opgaat en je werk en de tijd voorbij vliegt, is helemaal',
                        'op jou van toepassing.'
                    ]
                ]
            ]
        ],
        'YOU' => [
            'graph' => false,
            'key' => null,
            'pageTitle' => 'Nu jij',
            'pageText' => [
                'Hoe heb je het invullen van de vragenlijst en het bekijken van de scores ervaren? Viel',
                'het mee of tegen? Heeft het je al wat nieuwe inzichten opgeleverd of roept het juist',
                'aanvullende vragen op? Wat de situatie ook is: met het invullen van deze vragenlijst heb',
                'je een belangrijke eerste stap gezet naar meer werkplezier. Het is het begin van jouw',
                'persoonlijke zoektocht naar meer plezier, voldoening, uitdaging en geluk. Met dit traject',
                'gun je jezelf de tijd om te ontdekken wat er voor jou nodig is om meer werkplezier te',
                'ervaren én wat je zelf kunt doen om dit te bereiken. Je ontdekt waar jouw professionele',
                'hart sneller van gaat kloppen. ',
                '',
                'Bekijk de achtendertig vragen nog eens stuk voor stuk en wees daarbij eerlijk naar jezelf',
                'toe. Heb je de vragen naar waarheid beantwoordt? Of heb je bij sommige vragen misschien',
                'het meest wenselijke antwoord gegeven?  Waar zitten je zwakste scores? En welke stappen',
                'zou je kunnen nemen om je baan zelf vorm te geven en daardoor meer plezier te hebben?',
                '',
                'Deze vragenlijst was bedoeld om je inzicht te geven in de huidige situatie en richting te',
                'geven aan het verdere traject. Inzichten worden echte pas écht waardevol wanneer je ze',
                'omzet in concrete acties. Dit traject biedt de handvatten om dit te doen.',
                '',
                'Ik kijk ernaar uit om samen met jou het werkplezier weer terug te brengen in je dagelijkse',
                'werkzaamheden. En realiseer je: het is een proces. Wees dus mild voor jezelf. Voel je vrij',
                'om op je gemak verschillende paden af te lopen, zo nu en dan te struikelen en negeer',
                'goedbedoelde adviezen die uitsluitend gebaseerd zijn hoe zij het zouden aanpakken. Het is',
                'jouw carrière.'
            ],
            'benchmark' => 0,
            'text' => [
            ]
        ],
        'SOLUTION' => [
            'graph' => false,
            'key' => null,
            'pageTitle' => 'Toelichting en andere bronnen',
            'pageText' => [
                'Toelichting bronnen hulp en andere bronnen Bij de vragenlijst en bijbehorende score',
                'uitslag spreken we veelvuldig over wat we in de psychologie ook wel bronnen noemen.',
                'Hulpbronnen, werkgerelateerde hulpbronnen, sociale hulpbronnen en persoonlijke',
                'hulpbronnen: het zijn zaken die je inzicht geven in je situatie met als doel om meer',
                'werkplezier te realiseren. Omdat de term niet voor iedereen even duidelijk is, volgt',
                'hieronder een korte toelichting.',
                '',
                'Hulpbronnen zijn bronnen waar je je als professional toe kunt wenden. Bijvoorbeeld',
                'omdat je behoefte hebt aan extra energie, inspiratie, motivatie of uitdaging. Hulpbronnen',
                'beschermen je als het ware tegen de (hoge) eisen die aan jou als professional worden',
                'gesteld. Hulpbronnen leiden tot motivatie, werkplezier en persoonlijke groei en ontwikkeling.',
                '',
                'We kunnen twee verschillende soorten hulpbronnen onderscheiden: Werkgerelateerde',
                'hulpbronnen en persoonlijke hulpbronnen.',
                '',
                [
                    'bold' => 'Werkgerelateerde hulpbronnen ',
                    'text' => 'zijn, zoals de naam al doet vermoeden, te relateren aan'
                ],
                'de werksituatie. Zaken als autonomie, feedback, sociale steun van collega\'s, coaching van',
                'de leidinggevende, waardering en ontwikkelmogelijkheden zijn hier voorbeelden van.',
                '',
                [
                    'bold' => 'Persoonlijke hulpbronnen ',
                    'text' => 'zijn positief ingestoken zelfevaluaties die gerelateerd zijn'
                ],
                'aan veerkracht van een individu en verwijzen naar het gevoel van vermogen om de',
                'omgeving te controleren. Met andere woorden: hulpbronnen die je kunt aanspreken',
                'én waar je direct zelf invloed op hebt. Voorbeelden hiervan zijn optimisme, persoonlijk',
                'effectiviteit, hoop, weerbaarheid, zelfsturing en interne beheersingsoriëntatie. Door',
                'jezelf op deze vlakken te trainen heb je direct invloed op hoe je je werk ervaart.'
            ],
            'benchmark' => 0,
            'text' => [
            ]
        ]
    ];

    CONST SCORE_VALUES_2 = [
        'STRUCTURE' => [
            'graph' => true,
            'key' => 'score_structurelehulpbronnen',
            'pageTitle' => 'Inzichten in de structurele hulpbronnen van je medewerker',
            'pageText' => [
                'Een medewerker is enkel in staat om zijn werk goed te kunnen doen wanneer hij over de juiste',
                'vaardigheden beschikt. Sommige vaardigheden zijn te verkrijgen met het opdoen van kennis of ',
                'het volgen van een training of cursus, maar andere vaardigheden leer je van anderen, van de',
                'mensen met wie je werkt. Door met collega\'s of leidinggevenden te brainstormen, feedback uit',
                'te wisselen en inzichten op te doen die vervolgens kunnen worden geïntegreerd in de eigen ',
                'werkwijze. Dit leren van anderen noemen we ook wel het inschakelen van structurele',
                'hulpbronnen.',
                'De vragen bij dit onderdeel gaan over het benutten van deze zogeheten hulpbronnen: het ',
                'inschakelen van collega\'s, mentoren of begeleiders. Personen die ervoor zorgen dat de ',
                'medewerker niet alleen beter wordt in zijn vak, maar die óók een belangrijke bijdrage ',
                'leveren aan zijn persoonlijke ontwikkeling en werkplezier. De scores bij dit onderdeel ',
                'gaan over het activeren van deze structurele hulpbronnen in relatie tot de uitdaging die ',
                'je medewerker in zijn werk ervaart.'
            ],
            'benchmark' => 21,
            'text' => [
                'equal' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score is gelijk aan de benchmark. Dit houdt in dat je voldoende variatie ervaart in je dagelijkse',
                        'werkzaamheden, maar dat er zeker nog ruimte is voor verbetering. Hoe zou je sommige taken',
                        'anders aan kunnen pakken? Is er een manier om de werkzaamheden effectiever of op een',
                        'leukere manier aan te pakken?  Zoek actief het contact met collega\'s of je begeleider op en vraag',
                        'hoe ze hiermee omgaan en of ze nog ideeën hebben. Onderzoek ook andere hulpbronnen binnen',
                        'de organisatie, bijvoorbeeld de mogelijkheid tot het volgen van (interne) trainingen of cursussen.',
                    ]
                ],
                'less' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je geeft een lagere score dan de benchmark. Volgens jou ervaart je medeweker onvoldoende uitdaging',
                        'binnen zijn huidige werkzaamheden. Om zijn werkplezier te verhogen is het zaak om ervoor te zorgen',
                        'dat hij meer variatie weet aan te brengen. Een goede manier om dit te doen is door hierover in ',
                        'gesprek te gaan met collega\'s. Hoe zorgen zij voor voldoende afwisseling? Wat doen ze anders en ',
                        'wat kan hij daarvan leren? Stimuleer je medewerker om te onderzoeken of er binnen de organisatie ',
                        'andere mogelijkheden zijn om de dagen afwisselender te maken, bijvoorbeeld met het volgen van ',
                        '(interne) trainingen of cursussen om meer uitdaging te creëren.'
                    ]
                ],
                'greater' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score is hoger dan de benchmark. Je vindt dat je professional genoeg variatie in zijn',
                        'werkzaamheden ervaart. Om hem extra uit te dagen kun je met elkaar zijn sterke en zwakke',
                        'punten analyseren en bedenken of hij bepaalde taken op een andere manier aan zou kunnen',
                        'pakken. Maak gebruik van (aanvullende) trainingsmogelijkheden om vaardigheden te verbeteren',
                        'en laat hem in gesprek gaan met mensen die hem andere inzichten kunnen geven. Dit kunnen ',
                        'collega\'s zijn, maar bijvoorbeeld ook mentoren of mensen buiten de organisatie.',
                    ]
                ]
            ]
        ],
        'SOCIAL' => [
            'graph' => true,
            'key' => 'score_socialehulpbronnen',
            'pageTitle' => 'Inzichten in sociale hulpbronnen op het werk uitbreiden van je medewerker',
            'pageText' => [
                'Wie binnen de organisatie zou je medewerker kunnen helpen om meer werkplezier te ',
                'ervaren? En wat heeft hij van die persoon nodig? In dit onderdeel verkennen we de ',
                'mogelijkheden met betrekking tot het krijgen van feedback of steun van collega\'s,',
                'begeleiders of mentoren. Dit onderdeel hangt samen met twee belangrijke componenten',
                'van werkplezier: persoonlijke ontwikkeling en je verbonden voelen met andere mensen ',
                'binnen de organisatie. Want door actief de verbinding op te zoeken investeert hij niet',
                'alleen in het beter worden in zijn functie, maar óók in het welzijn van hem als persoon.'
            ],
            'benchmark' => 16,
            'text' => [
                'equal' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je hebt het gevoel dat je medewerker meer gebruik zou moeten maken van sociale hulpbronnen binnen',
                        'de organisatie. Hij voert zijn werkzaamheden naar behoren uit, maar je merkt dat het hem soms ',
                        'moeite kost. Bij taken die hij zelfstandig uitvoert twijfelt hij soms of hij het wel goed doet.',
                        'Doordat hij wordt opgeslokt door de waan van de dag voelt hij niet altijd de ruimte om hulp in te ',
                        'schakelen van een collega of begeleider. Hij vraagt dus geen hulp wanneer het nodig is terwijl hij ',
                        'wel weet dat er genoeg deskundigheid aanwezig is om mee te sparren of advies te vragen. Om zijn ',
                        'vaardigheden als professional te verbeteren én om meer werkplezier te ervaren adviseren we om juist',
                        'wel het contact met anderen op te zoeken. Collega’s of begeleiders willen vaak met alle plezier ',
                        'feedback en suggesties geven. Hij moet er alleen wel om vragen. Denk na over hoe jij hem als manager',
                        'kunt stimuleren om dit te doen.'
                    ]
                ],
                'less' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je hebt het gevoel dat je medewerker meer gebruik zou moeten maken van sociale hulpbronnen binnen',
                        'de organisatie, maar dat hij dit niet uit zichzelf doet. Bij taken die hij zelfstandig uitvoert twijfelt',
                        'hij soms aan zichzelf of hij het wel goed doet. Hij wordt opgeslokt door de waan van de dag waarin hij',
                        'niet de ruimte neemt om hulp in te schakelen van een collega of begeleider. Het kan zijn dat hij een ',
                        'drempel ervaart om dit te doen, of dat hij niet goed weet bij wie hij terecht kan. Het zorgt voor twijfel',
                        'en onzekerheid, met minder werkplezier tot gevolg. Als manager is het zaak om hem te stimuleren om',
                        'juist wél dit contact meer op te zoeken. Collega\'s willen vaak met alle plezier feedback en suggesties',
                        'geven. Denk na over hoe jij hem hierbij kunt helpen.',
                    ]
                ],
                'greater' => [
                    'heading' => '',
                    'paragraph' => [
                        'Volgens jouw antwoorden is je medewerker zich zeer bewust van de aanwezigheid van sociale',
                        'hulpbronnen. Wanneer hij twijfelt maakt hij tijd vrij  om feedback te vragen aan collega\'s over zijn manier',
                        'van werken. Hij vraagt hulp wanneer hij dat nodig acht en merkt dat hij energie krijgt van de feedback en ',
                        'het contact met anderen. Als manager of leidinggevende zou je hem nog meer kunnen uitdagen door',
                        'kritische vragen te stellen. Een voorbeeld: \'wat zijn zaken die je écht nog kunt verbeteren?\' Ook kun',
                        'je eventueel periodiek evaluaties inplannen om dit nog verder uit te diepen.'
                    ]
                ]
            ]
        ],
        'EXPANDING' => [
            'graph' => true,
            'key' => 'score_uitdagendeeisen',
            'pageTitle' => 'Inzichten in uitdagende eisen op zijn werk uitbreiden',
            'pageText' => [
                'Uit onderzoek blijkt dat hoe meer uitdaging professionals in hun werk ervaren, hoe hoger',
                'de mate van werkplezier is. Het gevoel ergens helemaal in op te kunnen gaan, een gevoel',
                'dat ook wel bekend staat als \'flow\' zorgt voor voldoening én levert ook nog eens betere',
                'prestaties op. Hoe zit het met de uitdaging van je medewerker binnen zijn huidige functie?',
                'De scores bij dit onderdeel laten zien hoe het met de huidige uitdaging binnen zijn ',
                'werkzaamheden is gesteld en wat de kansen en mogelijkheden zijn.'
            ],
            'benchmark' => 17,
            'text' => [
                'equal' => [
                    'heading' => '',
                    'paragraph' => [
                        'Uit je score blijkt dat je medewerker zich nog altijd weet te ontwikkelen binnen zijn huidige',
                        'functie, maar dat je vindt dat er nog ruimte is voor verbetering. Wanneer de ontwikkeling ',
                        'stagneert, bestaat het risico dat ook het werkplezier afneemt omdat hij uitdaging mist. Het',
                        'is zaak om dit voor te blijven. We adviseren je om samen met je medeweker actief de uitdaging',
                        'op te zoeken. Stimuleer hem bijvoorbeeld om deel te nemen aan nieuwe, uitdagende projecten en',
                        'daarbij het initiatief te nemen. Daag hem uit door de diepte in te gaan en nieuwe ervaringen op',
                        'te doen waarbij hij zijn vaardigheden aan moet spreken. Analyseer samen zijn huidige werkzaamheden',
                        'en zoek naar manieren om die afwisselender of uitdagender te maken. Zoek de grenzen van de werkdruk',
                        'op en onderzoek wat er voor hem nodig is om \'flow\' te ervaren in zijn werk.'
                    ]
                ],
                'less' => [
                    'heading' => '',
                    'paragraph' => [
                        'Uit de score komt naar voren dat je het idee hebt dat de ontwikkeling van je medewerker stagneert',
                        'en hij op dit moment onvoldoende uitdaging ervaart in zijn werk. Volgens jouw perceptie blijkt dat',
                        'hij hier wel behoefte aan heeft, maar onvoldoende actie onderneemt om dit te realiseren. Hij is',
                        'terughoudend met het nemen van initiatieven en het aangaan van nieuwe projecten omdat dit kan',
                        'leiden tot een hogere werkdruk. Het risico hierbij is dat hij onvoldoende energie haalt uit zijn huidige',
                        'werkzaamheden, juist omdat hij die uitdaging en een zekere mate van urgentie mist. We adviseren om',
                        'spelenderwijs de grens van wat voor hem werkt op te zoeken. Onderzoek taken en projecten, en bepaal',
                        'samen waar voor hem de uitdaging ligt en welk werktempo daarbij past. Daag hem uit door zichzelf ',
                        'meer uit te (laten) dagen. Daar ligt de sleutel naar meer voldoening en werkplezier.'
                    ]
                ],
                'greater' => [
                    'heading' => '',
                    'paragraph' => [
                        'Uit je scores komt naar voren dat je medewerker progressie boekt in zijn ontwikkeling en ',
                        'werkzaamheden, maar dat er volgens jou nog ruimte is voor verbetering. Met name om ervoor te ',
                        'zorgen dat je medewerker voldoende uitdaging blijft ervaren, ook in de toekomst. Als leidinggevende',
                        'kun je hem ondersteunen in zijn zoektocht naar meer uitdaging. Attendeer hem bijvoorbeeld op nieuwe',
                        'en uitdagende projecten, analyseer of hij zijn huidige werkzaamheden uitdagender kan maken en ',
                        'schroom daarbij ook niet om de grenzen op te zoeken van wat voor hem werkt. Probeer je daarbij ',
                        'ook in de professional te verplaatsen. Welke werkzaamheden geven energie en wat is een fijn ',
                        'werktempo? Dat is waardevol omdat samen op die manier het meest ideale werkritme ofwel',
                        '\'flow\' ontdekt. Een van de belangrijkste pijlers van werkplezier.'
                    ]
                ]
            ]
        ],
        'IMPEDING' => [
            'graph' => true,
            'key' => 'score_belemmerendeeisen',
            'pageTitle' => 'Inzichten in belemmerende eisen op zijn werk verminderen',
            'pageText' => [
                'Zich minder betrokken voelen bij werkzaamheden of projecten, steeds vaker pauzeren of',
                'ander werkontwijkend gedrag vertonen: het kan erop duiden dat je professional momenteel',
                'met minder plezier naar zijn werk gaat. Dat werpt voor jou als manager een interessante vraag',
                'op: wat zijn de belemmerende factoren die tussen hem en die uitdagende baan instaan? De ',
                'scores bij dit onderdeel geven aan hoe met de motivatie, betrokkenheid en het werkplezier',
                'van je medewerker is gesteld.'
            ],
            'benchmark' => 14,
            'text' => [
                'equal' => [
                    'heading' => '',
                    'paragraph' => [
                        'Uit deze score blijkt dat je medewerker over het algemeen met plezier naar zijn werk gaat, maar',
                        'dat is zaak is om waakzaam te blijven. Je score laat zien dat je medewerker het contact met',
                        'collega\'s soms uit de weg gaat, sommige werkzaamheden ontwijkt of snel is afgeleid. Je merkt ',
                        'aan hem dat het werkplezier soms lijkt af te nemen. Je hebt het gevoel dat hij veel op de ',
                        'automatische piloot werkt. Soms laat hij je weten dat de dagen wel erg veel op elkaar lijken.',
                        'Een goed en inhoudelijk gesprek met collega\'s geeft hem energie, net zoals deelname aan een',
                        'uitdagend project waarbij hij zijn cognitieve vermogen wordt aangesproken. Dit komt echter',
                        'onvoldoende voor. Bespreek dit met je professional. Blijf bewust van deze zaken die hem ',
                        'energie geven en probeer daar ook naar te handelen. Denk actief mee over hoe je deze',
                        'belemmerende eisen weg kunt nemen.'
                    ]
                ],
                'less' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score voor belemmerende eisen op het werk is lager dan de benchmark. Dat is, in tegenstelling',
                        'De score bij dit onderdeel is lager dan de benchmark. Dat is, in tegenstelling tot de scores bij',
                        'de andere vragen, goed nieuws! Je score toont aan dat je medewerker helemaal op zijn plek zit.',
                        'Hij zoekt actief het contact op met collega\'s en ervaart voldoende uitdaging. Daar zorgt hij',
                        'ook voor. Taken en mensen die juist energie kosten, vermijd hij zoveel mogelijk. Hij geeft je',
                        'de indruk dat hij zich in zijn huidige baan kan ontwikkelen als professional en energie krijgt',
                        'van de werkzaamheden. Een inspirerend gesprek met een collega, een uitdagende taak en een',
                        'dynamische werkdag: het zijn zaken die hem veel voldoening en werkplezier geven. Dit zoekt',
                        'hij ook actief op.'
                    ]
                ],
                'greater' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je antwoorden laten zien dat je medewerker hoog scoort op het onderdeel belemmerende eisen op',
                        'het werk. Dit houdt in dat je medewerker wordt belemmerd in zijn werkplezier. De hoge score',
                        'geeft aan dat hij mogelijk mensen en taken bewust of onbewust uit de weg gaat. Je hebt het',
                        'idee dat hij op de automatische piloot werkt en dat zijn ontwikkeling stagneert. Wellicht',
                        'valt het je op dat hij snel is afgeleid, langer pauzes neemt of dat hij zich je stoort aan',
                        'collega’s. Als mensen hun plezier en energie voelen wegvloeien dan zien we dat ze (moeilijke)',
                        'taken niet meer aanpakken, taken uitstellen en zelfs gesprekken met collega\'s uit de weg',
                        'gaan. Dit is mogelijk bij hem ook het geval. Ga samen op zoek naar de oorzaak van deze',
                        'belemmeringen en probeer verbeteringen aan te brengen.'
                    ]
                ]
            ]
        ],
        'ENJOY' => [
            'graph' => true,
            'key' => 'score_werkplezier',
            'pageTitle' => 'Plezier in zijn werk',
            'pageText' => [
                'Waarom is het werkplezier van je medewerker zo belangrijk? Wanneer we spreken over',
                'werkplezier van medewerkers, hebben we het over het volgende: werkplezier is een ',
                'positieve psychologische toestand van opperste voldoening die gekenmerkt wordt door',
                'bruisen van energie, je sterk en fit voelen en het beschikken over mentale veerkracht',
                'en doorzettingsvermogen. Het stelt je in staat om hard te werken, goede prestaties te',
                'leveren en daar energie, plezier en voldoening aan te ontleden. Hoe is het gesteld met',
                'het werkplezier van jouw professional? De scores bij dit onderdeel brengen de huidige ',
                'situatie in kaart.'
            ],
            'benchmark' => 51,
            'text' => [
                'equal' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score laat zien dat je medewerker plezier ervaart in zijn baan, maar dat dit gepaard gaat',
                        'met golfbewegingen. Er zijn periodes waarin hij fit oogt en werkt met energie en uitdaging, ',
                        'maar die fases worden afgewisseld met periodes waarin de motivatie soms ver te zoeken is. Uit',
                        'je resultaten van je vragen komt naar voren dat hij zijn werk niet altijd als zinvol ervaart ',
                        'en het werk en het contact met collega\'s liever uit de weg gaat. Ook zie je dat hij ',
                        'bijvoorbeeld vaker op zijn telefoon zit, sneller is afgeleid en minder betrokken is bij de ',
                        'organisatie. In zo\'n periode kost het je medewerker moeite om daar weer \'uit\' te komen. In',
                        'je gesprekken constateer je dat zijn energie en daarmee ook het werkplezier aan het afnemen is.',
                        'Herkenbaar? Dan is het zaak om samen te onderzoeken hoe hij op een regelmatige manier het plezier',
                        'weer terugbrengt in zijn werk. Vraag hem wat hij van jou nodig heeft of schakel de hulp in van ',
                        'collega\'s. Ook de training De Psychologie van Werkplezier is in dit geval passend, waarbij je ',
                        'samen tot de kern komt en verbeteringen kunt aanbrengen.'
                    ]
                ],
                'less' => [
                    'heading' => '',
                    'paragraph' => [
                        'Je score laat zien dat je medeweker momenteel onvoldoende plezier ervaart in zijn baan. De',
                        'definitie van werkplezier, het krijgen van energie van je werk en voldoende uitdaging ervaren,',
                        'is bij hem onvoldoende het geval. Je merkt aan hem dat hij regelmatig vermoeid is of zich niet',
                        'fit voelt. Het kost hem moeite om zijn werk goed en geconcentreerd te doen en ook het contact',
                        'met collega\'s gaat niet vanzelf. Uit je antwoorden blijkt dat hij zijn werk niet als zinvol,',
                        'inspirerend of uitdagend ervaart. Dat zorgt ervoor dat hij weinig energie krijgt van werkzaamheden,',
                        'waardoor de motivatie afneemt. Het enthousiasme waarmee hij ooit begon in deze functie is nu',
                        'even niet aanwezig. Dit kan tijdelijk zijn, maar wellicht is er meer aan de hand. Hoe dan ook: ',
                        'verandering is nodig. Ga het gesprek aan met hem aan of onderzoek wat de verdiepende training de ',
                        'Psychologie van Werkplezier hem op dit vlak kan bieden.'
                    ]
                ],
                'greater' => [
                    'heading' => '',
                    'paragraph' => [
                        'Gefeliciteerd! Je score laat zien dat je momenteel een professional hebt die veel werkplezier',
                        'ervaart in zijn huidige baan. Natuurlijk heeft hij bij vlagen last van \'mindere\' dagen, maar',
                        'voor het overgrote heb je de indruk dat hij zich fit voelt en voldoende uitdaging en ontwikkeling',
                        'ervaart. Hij haalt veel plezier uit zijn werk en uit het contact met collega\'s. Uit je antwoorden',
                        'blijkt dat hij toegewijd en betrokken is en het gevoel heeft dat hij een wezenlijke bijdrage levert',
                        'aan het bedrijfsresultaat. Hij ervaart zijn werk als nuttig, zinvol, inspirerend en uitdagend. Ook',
                        'ervaart hij voldoende variatie. Het woord \'flow\', waarbij je helemaal opgaat en je werk en de ',
                        'tijd voorbij vliegt, is helemaal op hem van toepassing.'
                    ]
                ]
            ]
        ],
        'YOU' => [
            'graph' => false,
            'key' => null,
            'pageTitle' => 'Nu jij',
            'pageText' => [
                'Hoe heb je het invullen van de vragenlijst en het bekijken van de scores ervaren? Viel',
                'het mee of tegen? Heeft het je al wat nieuwe inzichten opgeleverd of roept het juist',
                'aanvullende vragen op? Wat de situatie ook is: met het invullen van deze vragenlijst',
                'heb je een belangrijke eerste stap gezet naar meer werkplezier voor je medewerker.',
                '',
                'Het is het begin van een gezamenlijk traject om meer inzichten te krijgen in het ',
                'plezier, voldoening, uitdaging en geluk. Met dit traject krijg je een goed beeld wat',
                'er voor jouw professional nodig is om meer werkplezier te bewerkstelligen én wat je',
                'zelf kunt doen om dit te bereiken. Je ontdekt waar zijn professionele hart sneller ',
                'van gaat kloppen.',
                '',
                'Bekijk de achtendertig vragen nog eens stuk voor stuk en wees daarbij eerlijk naar',
                'jezelf toe. Heb je de vragen naar waarheid beantwoordt? Of heb je bij sommige vragen',
                'misschien het meest wenselijke antwoord gegeven?  Waar zitten je opvallende scores?',
                'En welke stappen zou je kunnen nemen om hem te helpen om een inkijk te geven in de',
                'vijf dimensies?',
                '',
                'Deze vragenlijst was bedoeld om je inzicht te geven in de huidige situatie en ',
                'richting te geven aan het traject voor je medewerker. Inzichten worden pas écht ',
                'waardevol wanneer je het samen doet en omzet in concrete acties. De module de ',
                'Psychologie van Werkplezier biedt de handvatten om dit te doen.',
                '',
                'Ik kijk ernaar uit om samen met jou het werkplezier weer terug te brengen. En',
                'realiseer je: het is een proces. Wees dus mild voor jezelf maar ook voor je ',
                'professional. Voel je vrij om op je gemak verschillende paden af te lopen, zo',
                'nu en dan te struikelen.',
                '',
                'Laten we werk maken van werkplezier!',
                '',
                '',
                'Cees Braber | organisatiepsycholoog'
            ],
            'benchmark' => 0,
            'text' => [
            ]
        ],
        'SOLUTION' => [
            'graph' => false,
            'key' => null,
            'pageTitle' => 'Toelichting bronnen hulp en andere bronnen',
            'pageText' => [
                'Bij de vragenlijst en bijbehorende score uitslag spreken we veelvuldig over wat we',
                'in de psychologie ook wel bronnen noemen. Hulpbronnen, werkgerelateerde hulpbronnen,',
                'sociale hulpbronnen en persoonlijke hulpbronnen: het zijn zaken die je inzicht geven',
                'in je situatie met als doel om meer werkplezier te realiseren. Omdat de term niet voor',
                'iedereen even duidelijk is, volgt hieronder een korte toelichting.',
                '',
                'Hulpbronnen zijn bronnen waar je je als professional toe kunt wenden. Bijvoorbeeld omdat',
                'je behoefte hebt aan extra energie, inspiratie, motivatie of uitdaging. Hulpbronnen ',
                'beschermen je als het ware tegen de (hoge) eisen die aan jou als professional worden ',
                'gesteld. Hulpbronnen leiden tot motivatie, werkplezier en persoonlijke groei en ontwikkeling.',
                '',
                'We kunnen twee verschillende soorten hulpbronnen onderscheiden: werkgerelateerde ',
                'hulpbronnen en persoonlijke hulpbronnen.',
                '',
                [
                    'bold' => 'Werkgerelateerde hulpbronnen ',
                    'text' => 'zijn, zoals de naam al doet vermoeden, te relateren '
                ],
                'aan de werksituatie. Zaken als autonomie, feedback, sociale steun van collega\'s, coaching',
                'van de leidinggevende, waardering en ontwikkelmogelijkheden zijn hier voorbeelden van.',
                '',
                [
                    'bold' => 'Persoonlijke hulpbronnen ',
                    'text' => 'zijn positief ingestoken zelfevaluaties die gerelateerd zijn'
                ],
                'aan veerkracht van een individu en verwijzen naar het gevoel van vermogen om de omgeving',
                'te controleren. Met andere woorden: hulpbronnen die je kunt aanspreken én waar je direct',
                'zelf invloed op hebt. Voorbeelden hiervan zijn optimisme, persoonlijk effectiviteit, hoop,',
                'weerbaarheid, zelfsturing en interne beheersingsoriëntatie. Door jezelf op deze vlakken te',
                'trainen heb je direct invloed op hoe je je werk ervaart.'
            ],
            'benchmark' => 0,
            'text' => [
            ]
        ]
    ];

    public function __construct()
    {
        if (!defined('FPDF_FONTPATH')) define('FPDF_FONTPATH', public_path('fonts/fpdf'));
    }

    /**
     * @param Request $request
     * @return bool
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function index(Request $request)
    {
        /*$data = '{"event_id":"01FWKQVJT7D9XX6KXHT87CTD3F","event_type":"form_response","form_response":{"form_id":"r31HTB0r","token":"u1en18oq7zuvrxbhxtu1en188a3t3ff8","landed_at":"2022-02-23T16:49:08Z","submitted_at":"2022-02-23T16:50:29Z","calculated":{"score":135},"variables":[{"key":"score","type":"number","number":135},{"key":"score_socialehulpbronnen","type":"number","number":20},{"key":"score_structurelehulpbronnen","type":"number","number":15},{"key":"score_uitdagendeeisen","type":"number","number":5},{"key":"score_verwerk","type":"number","number":10},{"key":"score_werkplezier","type":"number","number":85}],"definition":{"id":"r31HTB0r","title":"Psychologie van Werkplezier extended versie 2022-1-1","fields":[{"id":"BmZ4cYk9FfRb","title":"Hallo! Leuk dat je ge\u00efnteresseerd bent in jouw persoonlijke psychologie van werkplezier. Ik heet Cees, hoe mag ik jou noemen?","type":"short_text","ref":"d7d7ec58-c9b0-48d2-b654-288d9da8e310","properties":{}},{"id":"sL565N85nDuG","title":"Ik probeer mijn capaciteiten te ontwikkelen","type":"opinion_scale","ref":"0b907e33-11e0-4e86-bc38-58ef7984c59a","properties":{}},{"id":"Y30TktSVytpD","title":"Ik probeer mezelf in professioneel opzicht te ontwikkelen","type":"opinion_scale","ref":"1d957eba-c64b-45f0-860d-c4a442abea6a","properties":{}},{"id":"3SJcR4yCGGXG","title":"Ik zorg ervoor dat ik mijn capaciteiten optimaal benut","type":"opinion_scale","ref":"e1eded90-a8e2-433e-a258-dde171844d96","properties":{}},{"id":"SpOCJ8auSkUh","title":"Ik probeer op mijn werk nieuwe dingen te leren","type":"opinion_scale","ref":"231f568c-23e0-4eb0-80d0-e61d21758dda","properties":{}},{"id":"eBJ2FD7v1Uhb","title":"Ik besluit zelf hoe ik zaken aanpak","type":"opinion_scale","ref":"c436f849-24bd-4035-91f1-16bb5394356e","properties":{}},{"id":"ZdCJz2B4Cp0U","title":"Ik zorg ervoor dat mijn werk geestelijk minder belastend is","type":"opinion_scale","ref":"0b3438ac-d8a6-4648-a9b8-660afa75cc9e","properties":{}},{"id":"BGFxMG66GEyw","title":"Ik probeer ervoor te zorgen dat mijn werk geestelijk minder belastend is","type":"opinion_scale","ref":"0056c049-b9fb-48ec-abb7-244ba054e17d","properties":{}},{"id":"8DiOWkVJtuGk","title":"Ik deel mijn werk zo in dat ik zo weinig mogelijk contact heb met collega\'s wiens problemen me emotioneel raken","type":"opinion_scale","ref":"1f41e372-49cd-4793-8f8a-570795ea9015","properties":{}},{"id":"b0dAIlWYhTUp","title":"Ik organiseer mijn werk zo dat ik zo weinig mogelijk contact heb met de mensen wier verwachtingen niet realistisch zijn","type":"opinion_scale","ref":"7955de94-fc09-45d5-8c6b-797ba1cdb7fb","properties":{}},{"id":"Pby5cRknhppW","title":"ik probeer ervoor te zorgen dat ik op  mijn werk niet veel moeilijke beslissingen hoef te nemen.","type":"opinion_scale","ref":"288a082f-683b-4168-a5f8-b359879b5744","properties":{}},{"id":"7WscGASOEBRG","title":"Ik vraag mijn leidinggevende om me te coachen","type":"opinion_scale","ref":"d686d225-bc1d-4e68-8273-85d143ffa74c","properties":{}},{"id":"Gir0tnyYCy5P","title":"Ik vraag collega\'s om advies","type":"opinion_scale","ref":"ecf8200b-552f-4674-a427-f5eaf61fde2c","properties":{}},{"id":"qeVixlkctjdD","title":"Ik haal inspiratie uit mijn leidinggevende","type":"opinion_scale","ref":"e5c1937e-0529-4620-a4f1-6cd7e398a3ef","properties":{}},{"id":"bfs3QUqJf1Hv","title":"Ik vraag anderen om feedback over mijn werkprestaties","type":"opinion_scale","ref":"96e9bc1d-5419-400f-a882-c1a8bed30697","properties":{}},{"id":"LCTvMrkWExrG","title":"Als er een interessant project langskomt, bied ik mezelf proactief aan als projectmedewerker","type":"opinion_scale","ref":"b60947dd-eab4-4251-be03-42ede6b4d44e","properties":{}},{"id":"GLqyPC2ZPVz5","title":"Als er nieuwe ontwikkelingen zijn, ben ik een van de eerste om daar kennis van te nemen en ze uit te proberen","type":"opinion_scale","ref":"6b95da10-7811-4b08-a623-3ec809696150","properties":{}},{"id":"HWd0FV7gA5K2","title":"Als het op mijn werk niet druk is, zie ik dat als een kans om nieuwe projecten te starten","type":"opinion_scale","ref":"cf5f416d-eb3a-4047-a161-4873b38edad0","properties":{}},{"id":"0jCZ6vEidKSG","title":"ik probeer mijn werk wat zwaarder te maken door de onderliggende verbanden van mijn werkzaamheden in kaart te brengen","type":"opinion_scale","ref":"1de9a3cc-4d2d-4c16-8a2b-b79c37606368","properties":{}},{"id":"YHxBxyAHDVNa","title":"Ik voer regelmatig extra taken uit, ook al krijg ik daar niet extra voor betaald","type":"opinion_scale","ref":"3c942911-7368-480d-8f43-3860bf60af52","properties":{}},{"id":"DUa0J7G3Eurf","title":"Op mijn werk bruis ik van de energie","type":"opinion_scale","ref":"dac1b855-59b6-4823-9e6d-316f94996c4b","properties":{}},{"id":"IryU2kh2Cbug","title":"Als ik werk dan voel ik me fit en sterk","type":"opinion_scale","ref":"adc2fcb6-6e4a-4fb1-ac30-fd71154339b4","properties":{}},{"id":"71PZOuijeq9E","title":"Als ik \'s morgens opsta heb ik zin om aan het werk te gaan","type":"opinion_scale","ref":"a6fd7846-d6c4-4a6c-bde1-3bf334af19a9","properties":{}},{"id":"s75BL1nrNPBG","title":"Als ik aan het werk ben, dan kan ik heel lang doorgaan","type":"opinion_scale","ref":"48db6512-cb46-4b33-a8a9-3495b4894a97","properties":{}},{"id":"ugHStbPL1ku1","title":"Op mijn werk beschik ik over een grote mentale (geestelijke) veerkracht","type":"opinion_scale","ref":"690df361-9d6e-4061-a90e-44d4e3670518","properties":{}},{"id":"YQqMTNlFAOGH","title":"Op mijn werk zet ik altijd door, ook als het tegenzit","type":"opinion_scale","ref":"daa011d6-6e69-4b6b-9c7b-7c00761c0d8e","properties":{}},{"id":"qZ7uyOamGA5F","title":"Ik vind het werk wat ik doe nuttig en zinvol","type":"opinion_scale","ref":"50178a9f-cf9e-45a1-a4db-f47a4638d4a6","properties":{}},{"id":"oQTPn9B88KWq","title":"Ik ben enthousiast over mijn baan","type":"opinion_scale","ref":"6efece45-9e5a-404b-bcc8-a4d6443fd46d","properties":{}},{"id":"aoM9qD2JTPB3","title":"Mijn werk inspireert mij","type":"opinion_scale","ref":"39ae0614-90df-45df-894a-fa586fea0fd9","properties":{}},{"id":"plo93rxRPnb8","title":"Ik ben trots op het werk dat ik doe","type":"opinion_scale","ref":"f24765d6-596c-4e6f-85e9-0c81e30efdd8","properties":{}},{"id":"UuaSaw6ZU4hy","title":"Mijn werk is voor mij een uitdaging","type":"opinion_scale","ref":"fc3a0d8d-ae74-4634-89c1-253b8f96b640","properties":{}},{"id":"26ns63KrHNTz","title":"Als ik aan het werk ben, dan vliegt de tijd voorbij","type":"opinion_scale","ref":"f4463d3f-a6eb-4b22-9c62-e951c7cdcbb8","properties":{}},{"id":"ifMFXkLUQKph","title":"Als ik werk vergeet ik alle andere dingen om me heen","type":"opinion_scale","ref":"74d09450-4ec2-442e-b701-885872f60a4e","properties":{}},{"id":"9b8dakJ6Rt7l","title":"Wanneer ik heel intensief aan het werk ben, voel ik mij gelukkig","type":"opinion_scale","ref":"dbaa568a-e6b4-4877-9053-b2e62248c1df","properties":{}},{"id":"P9Ha1fIk3x8g","title":"Ik ga helemaal op in mijn werk","type":"opinion_scale","ref":"54321947-2add-4751-8be9-3d821b72c613","properties":{}},{"id":"Jh3OYiT2zOwd","title":"Mijn werk brengt mij in vervoering","type":"opinion_scale","ref":"77f7b914-2a73-4d8c-86e0-6e793f3210b4","properties":{}},{"id":"zhdYMxud4yk3","title":"Ik kan me moeilijk van mijn werk losmaken","type":"opinion_scale","ref":"a47a7dcf-194c-4abb-99bf-b29a9f9fec24","properties":{}},{"id":"balGWBsPTh0i","title":"Ik stuur je graag jouw persoonlijke score in werkplezier op! Wat is jouw e-mail adres?","type":"email","ref":"bf1f8455-9384-4145-82fd-638373e4835d","properties":{}}]},"answers":[{"type":"text","text":"sendgrid","field":{"id":"BmZ4cYk9FfRb","type":"short_text","ref":"d7d7ec58-c9b0-48d2-b654-288d9da8e310"}},{"type":"number","number":3,"field":{"id":"sL565N85nDuG","type":"opinion_scale","ref":"0b907e33-11e0-4e86-bc38-58ef7984c59a"}},{"type":"number","number":3,"field":{"id":"Y30TktSVytpD","type":"opinion_scale","ref":"1d957eba-c64b-45f0-860d-c4a442abea6a"}},{"type":"number","number":3,"field":{"id":"3SJcR4yCGGXG","type":"opinion_scale","ref":"e1eded90-a8e2-433e-a258-dde171844d96"}},{"type":"number","number":3,"field":{"id":"SpOCJ8auSkUh","type":"opinion_scale","ref":"231f568c-23e0-4eb0-80d0-e61d21758dda"}},{"type":"number","number":3,"field":{"id":"eBJ2FD7v1Uhb","type":"opinion_scale","ref":"c436f849-24bd-4035-91f1-16bb5394356e"}},{"type":"number","number":2,"field":{"id":"ZdCJz2B4Cp0U","type":"opinion_scale","ref":"0b3438ac-d8a6-4648-a9b8-660afa75cc9e"}},{"type":"number","number":2,"field":{"id":"BGFxMG66GEyw","type":"opinion_scale","ref":"0056c049-b9fb-48ec-abb7-244ba054e17d"}},{"type":"number","number":2,"field":{"id":"8DiOWkVJtuGk","type":"opinion_scale","ref":"1f41e372-49cd-4793-8f8a-570795ea9015"}},{"type":"number","number":2,"field":{"id":"b0dAIlWYhTUp","type":"opinion_scale","ref":"7955de94-fc09-45d5-8c6b-797ba1cdb7fb"}},{"type":"number","number":2,"field":{"id":"Pby5cRknhppW","type":"opinion_scale","ref":"288a082f-683b-4168-a5f8-b359879b5744"}},{"type":"number","number":5,"field":{"id":"7WscGASOEBRG","type":"opinion_scale","ref":"d686d225-bc1d-4e68-8273-85d143ffa74c"}},{"type":"number","number":5,"field":{"id":"Gir0tnyYCy5P","type":"opinion_scale","ref":"ecf8200b-552f-4674-a427-f5eaf61fde2c"}},{"type":"number","number":5,"field":{"id":"qeVixlkctjdD","type":"opinion_scale","ref":"e5c1937e-0529-4620-a4f1-6cd7e398a3ef"}},{"type":"number","number":5,"field":{"id":"bfs3QUqJf1Hv","type":"opinion_scale","ref":"96e9bc1d-5419-400f-a882-c1a8bed30697"}},{"type":"number","number":1,"field":{"id":"LCTvMrkWExrG","type":"opinion_scale","ref":"b60947dd-eab4-4251-be03-42ede6b4d44e"}},{"type":"number","number":1,"field":{"id":"GLqyPC2ZPVz5","type":"opinion_scale","ref":"6b95da10-7811-4b08-a623-3ec809696150"}},{"type":"number","number":1,"field":{"id":"HWd0FV7gA5K2","type":"opinion_scale","ref":"cf5f416d-eb3a-4047-a161-4873b38edad0"}},{"type":"number","number":1,"field":{"id":"0jCZ6vEidKSG","type":"opinion_scale","ref":"1de9a3cc-4d2d-4c16-8a2b-b79c37606368"}},{"type":"number","number":1,"field":{"id":"YHxBxyAHDVNa","type":"opinion_scale","ref":"3c942911-7368-480d-8f43-3860bf60af52"}},{"type":"number","number":5,"field":{"id":"DUa0J7G3Eurf","type":"opinion_scale","ref":"dac1b855-59b6-4823-9e6d-316f94996c4b"}},{"type":"number","number":5,"field":{"id":"IryU2kh2Cbug","type":"opinion_scale","ref":"adc2fcb6-6e4a-4fb1-ac30-fd71154339b4"}},{"type":"number","number":5,"field":{"id":"71PZOuijeq9E","type":"opinion_scale","ref":"a6fd7846-d6c4-4a6c-bde1-3bf334af19a9"}},{"type":"number","number":5,"field":{"id":"s75BL1nrNPBG","type":"opinion_scale","ref":"48db6512-cb46-4b33-a8a9-3495b4894a97"}},{"type":"number","number":5,"field":{"id":"ugHStbPL1ku1","type":"opinion_scale","ref":"690df361-9d6e-4061-a90e-44d4e3670518"}},{"type":"number","number":5,"field":{"id":"YQqMTNlFAOGH","type":"opinion_scale","ref":"daa011d6-6e69-4b6b-9c7b-7c00761c0d8e"}},{"type":"number","number":5,"field":{"id":"qZ7uyOamGA5F","type":"opinion_scale","ref":"50178a9f-cf9e-45a1-a4db-f47a4638d4a6"}},{"type":"number","number":5,"field":{"id":"oQTPn9B88KWq","type":"opinion_scale","ref":"6efece45-9e5a-404b-bcc8-a4d6443fd46d"}},{"type":"number","number":5,"field":{"id":"aoM9qD2JTPB3","type":"opinion_scale","ref":"39ae0614-90df-45df-894a-fa586fea0fd9"}},{"type":"number","number":5,"field":{"id":"plo93rxRPnb8","type":"opinion_scale","ref":"f24765d6-596c-4e6f-85e9-0c81e30efdd8"}},{"type":"number","number":5,"field":{"id":"UuaSaw6ZU4hy","type":"opinion_scale","ref":"fc3a0d8d-ae74-4634-89c1-253b8f96b640"}},{"type":"number","number":5,"field":{"id":"26ns63KrHNTz","type":"opinion_scale","ref":"f4463d3f-a6eb-4b22-9c62-e951c7cdcbb8"}},{"type":"number","number":5,"field":{"id":"ifMFXkLUQKph","type":"opinion_scale","ref":"74d09450-4ec2-442e-b701-885872f60a4e"}},{"type":"number","number":5,"field":{"id":"9b8dakJ6Rt7l","type":"opinion_scale","ref":"dbaa568a-e6b4-4877-9053-b2e62248c1df"}},{"type":"number","number":5,"field":{"id":"P9Ha1fIk3x8g","type":"opinion_scale","ref":"54321947-2add-4751-8be9-3d821b72c613"}},{"type":"number","number":5,"field":{"id":"Jh3OYiT2zOwd","type":"opinion_scale","ref":"77f7b914-2a73-4d8c-86e0-6e793f3210b4"}},{"type":"number","number":5,"field":{"id":"zhdYMxud4yk3","type":"opinion_scale","ref":"a47a7dcf-194c-4abb-99bf-b29a9f9fec24"}},{"type":"email","email":"remco.tevreden+sendgrid@gmail.com","field":{"id":"balGWBsPTh0i","type":"email","ref":"bf1f8455-9384-4145-82fd-638373e4835d"}}]}}';
        $data = json_decode($data, true);*/
        $data = $request->all();

        $samplePrint = public_path('assets/report.pdf');
        $pdf = new Fpdi();
        $pdf->setSourceFile($samplePrint);

        $pdf->AddFont('Montserrat','','Montserrat-Regular.php');
        $pdf->AddFont('Montserrat','B','Montserrat-Bold.php');
        $pdf->SetFont('Montserrat','', 10);

        $currentPage = 1;
        for ($i = 1; $i <= 3; $i++) {
            $page = $pdf->importPage($i, PageBoundaries::MEDIA_BOX);
            $pdf->AddPage();
            $pdf->useTemplate($page);
            $currentPage = $i;
        }
        $currentPage++;

        if(isset($data['form_response']) && isset($data['form_response']['variables'])){
            $sortedVariables = [];
            foreach ($data['form_response']['variables'] as $variable){
                if ($variable['key'] == self::SCORE_VALUES['STRUCTURE']['key']) {
                    $sortedVariables[0] = $variable;
                }
                if ($variable['key'] == self::SCORE_VALUES['SOCIAL']['key']) {
                    $sortedVariables[1] = $variable;
                }
                if ($variable['key'] == self::SCORE_VALUES['EXPANDING']['key']) {
                    $sortedVariables[2] = $variable;
                }
                if ($variable['key'] == self::SCORE_VALUES['IMPEDING']['key']) {
                    $sortedVariables[3] = $variable;
                }
                if ($variable['key'] == self::SCORE_VALUES['ENJOY']['key']) {
                    $sortedVariables[4] = $variable;
                }
            }
            ksort($sortedVariables);
            foreach ($sortedVariables as $variable){
                $scoreArray = ['STRUCTURE', 'SOCIAL', 'EXPANDING', 'IMPEDING', 'ENJOY'];
                foreach ($scoreArray as $score) {
                    if ($variable['key'] == self::SCORE_VALUES[$score]['key']) {
                        $this->createGraph($pdf, $currentPage, $variable, $score);
                    }
                }
            }
            $this->createGraph($pdf, $currentPage,  null, 'YOU');
            $this->addQuestions($pdf, $currentPage, $data);
            $this->createGraph($pdf, $currentPage,  null, 'SOLUTION');
            for ($j = 5; $j<= 5; $j++){
                $page = $pdf->importPage($j, PageBoundaries::MEDIA_BOX);
                $pdf->AddPage();
                $pdf->useTemplate($page);
            }
        }
        /*return $pdf->Output('I','report.pdf');
        exit;*/
        $fileName = 'uploads/report-'.uniqid().'.pdf';
        $filePath = public_path($fileName);
        $pdf->Output($filePath,'F');
        $email = $this->getEmail($data['form_response']['answers']);
        if(!is_null($email)) {
            return MailService::make()->sendMail($email, __('emails.report.subject'), __('emails.report.content'), $data['form_response']['answers'][0]['text'], $filePath, 'report.pdf');
        }else{
            Log::info('No email found: '.json_encode($data));
        }
    }

    public function form2(Request $request){
        /*$data = '{"event_id":"01G0DRBTTEMX8GEVG5H2TYJ5E1","event_type":"form_response","form_response":{"form_id":"Q6FV35f8","token":"v8qrw1dg0ypd1pplav8qrwsjj72da8zc","landed_at":"2022-04-12T02:05:11Z","submitted_at":"2022-04-12T02:06:42Z","calculated":{"score":108},"variables":[{"key":"score","type":"number","number":108},{"key":"score_belemmerendeeisen","type":"number","number":0},{"key":"score_socialehulpbronnen","type":"number","number":15},{"key":"score_structurelehulpbronnen","type":"number","number":15},{"key":"score_uitdagendeeisen","type":"number","number":15},{"key":"score_werkplezier","type":"number","number":50}],"definition":{"id":"Q6FV35f8","title":"2 Psychologie van Werkplezier extended version Management 2022-3-1","fields":[{"id":"YaZjMHAiDjGa","ref":"d7d7ec58-c9b0-48d2-b654-288d9da8e310","type":"short_text","title":"*Inleiding* *(dit is de juiste versie)* De vragenlijst geeft inzicht in het werkplezier van je professional. Het begint namelijk allemaal met het verkrijgen van een beeld van de huidige situatie. Wat gaat er goed en waar hij nog meer uithalen? Het rapport dat je gaat ontvangen bied je waardevolle inzichten om meer plezier \u00e9n betrokkenheid te initi\u00ebren en je medewerker te helpen om in beweging te komen. Disclaimer: Om de tekst leesbaar te houden, duid ik \'de professional\' of \'de medewerker\' aan met \'hij\'. Dit is slechts een taalkundige keuze, zodat je tijdens het lezen niet struikelt over zinsneden als \'hij\/zij\', \'hem\/haar\', enz. _","properties":[]},{"id":"jzdc0yvXUdnF","ref":"0b907e33-11e0-4e86-bc38-58ef7984c59a","type":"opinion_scale","title":"Hij probeert zijn capaciteiten te ontwikkelen","properties":[]},{"id":"8yEZA42wZaq9","ref":"1d957eba-c64b-45f0-860d-c4a442abea6a","type":"opinion_scale","title":"Mijn professional probeert zichzelf in professioneel opzicht te ontwikkelen","properties":[]},{"id":"Z0jV4mhLC7wx","ref":"e1eded90-a8e2-433e-a258-dde171844d96","type":"opinion_scale","title":"Je professional zorgt ervoor dat hij zijn capaciteiten optimaal benut.","properties":[]},{"id":"Ff6SBmrTnuBW","ref":"231f568c-23e0-4eb0-80d0-e61d21758dda","type":"opinion_scale","title":"Mijn professional probeert nieuwe dingen te leren","properties":[]},{"id":"SAMBsmQB9UsP","ref":"c436f849-24bd-4035-91f1-16bb5394356e","type":"opinion_scale","title":"Mijn professional besluit zelf hoe hij zaken aanpakt.","properties":[]},{"id":"0azJ9PwuLedF","ref":"0b3438ac-d8a6-4648-a9b8-660afa75cc9e","type":"opinion_scale","title":"Mijn professional  zorgt ervoor dat zijn werk geestelijk minder belastend is","properties":[]},{"id":"kG8G3FUt6GuE","ref":"0056c049-b9fb-48ec-abb7-244ba054e17d","type":"opinion_scale","title":"Mijn professional  probeert ervoor te zorgen dat zijn werk geestelijk minder belastend is","properties":[]},{"id":"lpCD0GmSDp80","ref":"1f41e372-49cd-4793-8f8a-570795ea9015","type":"opinion_scale","title":"Mijn professional  deelt zijn\/haar werk zo in dat hij zo weinig mogelijk contact heeft met collega\'s wiens problemen hem emotioneel raken","properties":[]},{"id":"vCGBlNa25PqZ","ref":"7955de94-fc09-45d5-8c6b-797ba1cdb7fb","type":"opinion_scale","title":"Mijn professional  organiseert zijn werk zo dat hij zo weinig mogelijk contact heeft met de mensen wier verwachtingen niet realistisch zijn","properties":[]},{"id":"6Swb7x1UnDY6","ref":"288a082f-683b-4168-a5f8-b359879b5744","type":"opinion_scale","title":"Mijn professional probeert ervoor te zorgen dat hij op het werk niet veel moeilijke beslissingen hoeft te nemen.","properties":[]},{"id":"Y6sHPJnceEvZ","ref":"67b03632-0479-4cc4-b278-9f4f42629927","type":"opinion_scale","title":"Mijn professional  organiseert zijn werk op zo\'n manier dat hij zich niet lang achter elkaar hoeft te concentreren","properties":[]},{"id":"PF73QILRkpnD","ref":"d686d225-bc1d-4e68-8273-85d143ffa74c","type":"opinion_scale","title":"Mijn professional vraagt mij om hem te coachen","properties":[]},{"id":"inBpJ5s9VpGO","ref":"ecf8200b-552f-4674-a427-f5eaf61fde2c","type":"opinion_scale","title":"Mijn professional  vraagt collega\'s om advies","properties":[]},{"id":"8sgy7dg0UpbX","ref":"e5c1937e-0529-4620-a4f1-6cd7e398a3ef","type":"opinion_scale","title":"Mijn professional haalt inspiratie uit mij als leidinggevende","properties":[]},{"id":"vr8JB1FoRqzQ","ref":"96e9bc1d-5419-400f-a882-c1a8bed30697","type":"opinion_scale","title":"Mijn professional vraagt anderen om feedback over zijn werkprestaties","properties":[]},{"id":"uDLpu8AIqb0t","ref":"50b2ec35-ae9d-49d1-9842-8d648dbd46d7","type":"opinion_scale","title":"Mijn professional vraagt mij geregeld of ik tevreden ben over zijn werk","properties":[]},{"id":"9GGnUaDtzjUH","ref":"b60947dd-eab4-4251-be03-42ede6b4d44e","type":"opinion_scale","title":"Als er een interessant project langskomt, biedt hij zichzelf proactief aan als projectmedewerker","properties":[]},{"id":"4C5cz4o4RL7a","ref":"6b95da10-7811-4b08-a623-3ec809696150","type":"opinion_scale","title":"Als er nieuwe ontwikkelingen zijn, is hij een van de eerste om daar kennis van te nemen en ze uit te proberen","properties":[]},{"id":"FUdHpnyka3j7","ref":"cf5f416d-eb3a-4047-a161-4873b38edad0","type":"opinion_scale","title":"Als het op zijn werk niet druk is, ziet hij dat als een kans om nieuwe projecten te starten","properties":[]},{"id":"hG1yMctTOMl5","ref":"1de9a3cc-4d2d-4c16-8a2b-b79c37606368","type":"opinion_scale","title":"Mijn professional  probeert zijn werk wat zwaarder te maken door de onderliggende verbanden van zijn werkzaamheden in kaart te brengen","properties":[]},{"id":"6BthXDhiMSQJ","ref":"3c942911-7368-480d-8f43-3860bf60af52","type":"opinion_scale","title":"Mijn professional voert regelmatig extra taken uit, ook al krijgt hij daar niet extra voor betaald","properties":[]},{"id":"R47AgW9nSDye","ref":"dac1b855-59b6-4823-9e6d-316f94996c4b","type":"opinion_scale","title":"Op zijn werk bruist hij van energie","properties":[]},{"id":"ju9epL8dEoE8","ref":"adc2fcb6-6e4a-4fb1-ac30-fd71154339b4","type":"opinion_scale","title":"Als hij op werk is oogt hij fit en sterk","properties":[]},{"id":"eKaLlxKINgRW","ref":"a6fd7846-d6c4-4a6c-bde1-3bf334af19a9","type":"opinion_scale","title":"Als hij \'s morgens binnenkomt dan heeft hij zin om aan het werk te gaan","properties":[]},{"id":"1M2YDknkGXix","ref":"48db6512-cb46-4b33-a8a9-3495b4894a97","type":"opinion_scale","title":"Als hij aan het werk is, dan kan hij heel lang door gaan","properties":[]},{"id":"0VYwmq2fmnAe","ref":"690df361-9d6e-4061-a90e-44d4e3670518","type":"opinion_scale","title":"Mijn professional beschikt over een grote mentale (geestelijke) veerkracht","properties":[]},{"id":"gfjlVwXgICUm","ref":"daa011d6-6e69-4b6b-9c7b-7c00761c0d8e","type":"opinion_scale","title":"Op het werk zet hij altijd door, ook als het tegenzit","properties":[]},{"id":"uHNdVM34ymXP","ref":"50178a9f-cf9e-45a1-a4db-f47a4638d4a6","type":"opinion_scale","title":"Mijn professional vindt het werk wat hij doet nuttig en zinvol","properties":[]},{"id":"BkGbFtFZy48D","ref":"6efece45-9e5a-404b-bcc8-a4d6443fd46d","type":"opinion_scale","title":"Mijn professional  is enthousiast over zijn baan","properties":[]},{"id":"EHk2PcK08dOS","ref":"39ae0614-90df-45df-894a-fa586fea0fd9","type":"opinion_scale","title":"Zijl  baan vindt hij inspirerend","properties":[]},{"id":"1J1pWgLjtN5g","ref":"f24765d6-596c-4e6f-85e9-0c81e30efdd8","type":"opinion_scale","title":"Mijn professional  is trots op het werk dat hij doet","properties":[]},{"id":"63ugxxByswlK","ref":"fc3a0d8d-ae74-4634-89c1-253b8f96b640","type":"opinion_scale","title":"Mijn professional vindt zijn werk uitdagend","properties":[]},{"id":"5RFyxQGpjPDb","ref":"f4463d3f-a6eb-4b22-9c62-e951c7cdcbb8","type":"opinion_scale","title":"Als hij aan het werk is, dan vliegt de tijd voor hem voorbij","properties":[]},{"id":"WFTQnExsxOKL","ref":"74d09450-4ec2-442e-b701-885872f60a4e","type":"opinion_scale","title":"Als hij aan het werk is vergeet hij alle andere dingen om hem heen","properties":[]},{"id":"KCpJY1Kh6Uiw","ref":"dbaa568a-e6b4-4877-9053-b2e62248c1df","type":"opinion_scale","title":"Wanneer hij heel intensief aan het werk is, voelt hij zich gelukkig","properties":[]},{"id":"6tfcEh5qjs5X","ref":"54321947-2add-4751-8be9-3d821b72c613","type":"opinion_scale","title":"Mijn professional  gaat helemaal op in zijn werk","properties":[]},{"id":"4U78KECDJqWU","ref":"77f7b914-2a73-4d8c-86e0-6e793f3210b4","type":"opinion_scale","title":"Het werk brengt hem in vervoering","properties":[]},{"id":"SHLP1h65r7gQ","ref":"a47a7dcf-194c-4abb-99bf-b29a9f9fec24","type":"opinion_scale","title":"Mijn professional kan zich moeilijk van het werk losmaken","properties":[]},{"id":"DlXN5R43in4M","ref":"bf1f8455-9384-4145-82fd-638373e4835d","type":"email","title":"Het resultaat van de vragen wordt je via de mail toegestuurd en persoonlijk met je besproken. Graag hieronder e-mailadres opgeven","properties":[]}]},"answers":[{"type":"text","text":"remco","field":{"id":"YaZjMHAiDjGa","type":"short_text","ref":"d7d7ec58-c9b0-48d2-b654-288d9da8e310"}},{"type":"number","number":1,"field":{"id":"jzdc0yvXUdnF","type":"opinion_scale","ref":"0b907e33-11e0-4e86-bc38-58ef7984c59a"}},{"type":"number","number":2,"field":{"id":"8yEZA42wZaq9","type":"opinion_scale","ref":"1d957eba-c64b-45f0-860d-c4a442abea6a"}},{"type":"number","number":3,"field":{"id":"Z0jV4mhLC7wx","type":"opinion_scale","ref":"e1eded90-a8e2-433e-a258-dde171844d96"}},{"type":"number","number":4,"field":{"id":"Ff6SBmrTnuBW","type":"opinion_scale","ref":"231f568c-23e0-4eb0-80d0-e61d21758dda"}},{"type":"number","number":5,"field":{"id":"SAMBsmQB9UsP","type":"opinion_scale","ref":"c436f849-24bd-4035-91f1-16bb5394356e"}},{"type":"number","number":1,"field":{"id":"0azJ9PwuLedF","type":"opinion_scale","ref":"0b3438ac-d8a6-4648-a9b8-660afa75cc9e"}},{"type":"number","number":2,"field":{"id":"kG8G3FUt6GuE","type":"opinion_scale","ref":"0056c049-b9fb-48ec-abb7-244ba054e17d"}},{"type":"number","number":3,"field":{"id":"lpCD0GmSDp80","type":"opinion_scale","ref":"1f41e372-49cd-4793-8f8a-570795ea9015"}},{"type":"number","number":4,"field":{"id":"vCGBlNa25PqZ","type":"opinion_scale","ref":"7955de94-fc09-45d5-8c6b-797ba1cdb7fb"}},{"type":"number","number":2,"field":{"id":"6Swb7x1UnDY6","type":"opinion_scale","ref":"288a082f-683b-4168-a5f8-b359879b5744"}},{"type":"number","number":1,"field":{"id":"Y6sHPJnceEvZ","type":"opinion_scale","ref":"67b03632-0479-4cc4-b278-9f4f42629927"}},{"type":"number","number":2,"field":{"id":"PF73QILRkpnD","type":"opinion_scale","ref":"d686d225-bc1d-4e68-8273-85d143ffa74c"}},{"type":"number","number":3,"field":{"id":"inBpJ5s9VpGO","type":"opinion_scale","ref":"ecf8200b-552f-4674-a427-f5eaf61fde2c"}},{"type":"number","number":4,"field":{"id":"8sgy7dg0UpbX","type":"opinion_scale","ref":"e5c1937e-0529-4620-a4f1-6cd7e398a3ef"}},{"type":"number","number":5,"field":{"id":"vr8JB1FoRqzQ","type":"opinion_scale","ref":"96e9bc1d-5419-400f-a882-c1a8bed30697"}},{"type":"number","number":1,"field":{"id":"uDLpu8AIqb0t","type":"opinion_scale","ref":"50b2ec35-ae9d-49d1-9842-8d648dbd46d7"}},{"type":"number","number":2,"field":{"id":"9GGnUaDtzjUH","type":"opinion_scale","ref":"b60947dd-eab4-4251-be03-42ede6b4d44e"}},{"type":"number","number":3,"field":{"id":"4C5cz4o4RL7a","type":"opinion_scale","ref":"6b95da10-7811-4b08-a623-3ec809696150"}},{"type":"number","number":4,"field":{"id":"FUdHpnyka3j7","type":"opinion_scale","ref":"cf5f416d-eb3a-4047-a161-4873b38edad0"}},{"type":"number","number":5,"field":{"id":"hG1yMctTOMl5","type":"opinion_scale","ref":"1de9a3cc-4d2d-4c16-8a2b-b79c37606368"}},{"type":"number","number":1,"field":{"id":"6BthXDhiMSQJ","type":"opinion_scale","ref":"3c942911-7368-480d-8f43-3860bf60af52"}},{"type":"number","number":2,"field":{"id":"R47AgW9nSDye","type":"opinion_scale","ref":"dac1b855-59b6-4823-9e6d-316f94996c4b"}},{"type":"number","number":3,"field":{"id":"ju9epL8dEoE8","type":"opinion_scale","ref":"adc2fcb6-6e4a-4fb1-ac30-fd71154339b4"}},{"type":"number","number":4,"field":{"id":"eKaLlxKINgRW","type":"opinion_scale","ref":"a6fd7846-d6c4-4a6c-bde1-3bf334af19a9"}},{"type":"number","number":5,"field":{"id":"1M2YDknkGXix","type":"opinion_scale","ref":"48db6512-cb46-4b33-a8a9-3495b4894a97"}},{"type":"number","number":1,"field":{"id":"0VYwmq2fmnAe","type":"opinion_scale","ref":"690df361-9d6e-4061-a90e-44d4e3670518"}},{"type":"number","number":2,"field":{"id":"gfjlVwXgICUm","type":"opinion_scale","ref":"daa011d6-6e69-4b6b-9c7b-7c00761c0d8e"}},{"type":"number","number":3,"field":{"id":"uHNdVM34ymXP","type":"opinion_scale","ref":"50178a9f-cf9e-45a1-a4db-f47a4638d4a6"}},{"type":"number","number":4,"field":{"id":"BkGbFtFZy48D","type":"opinion_scale","ref":"6efece45-9e5a-404b-bcc8-a4d6443fd46d"}},{"type":"number","number":5,"field":{"id":"EHk2PcK08dOS","type":"opinion_scale","ref":"39ae0614-90df-45df-894a-fa586fea0fd9"}},{"type":"number","number":1,"field":{"id":"1J1pWgLjtN5g","type":"opinion_scale","ref":"f24765d6-596c-4e6f-85e9-0c81e30efdd8"}},{"type":"number","number":2,"field":{"id":"63ugxxByswlK","type":"opinion_scale","ref":"fc3a0d8d-ae74-4634-89c1-253b8f96b640"}},{"type":"number","number":3,"field":{"id":"5RFyxQGpjPDb","type":"opinion_scale","ref":"f4463d3f-a6eb-4b22-9c62-e951c7cdcbb8"}},{"type":"number","number":4,"field":{"id":"WFTQnExsxOKL","type":"opinion_scale","ref":"74d09450-4ec2-442e-b701-885872f60a4e"}},{"type":"number","number":5,"field":{"id":"KCpJY1Kh6Uiw","type":"opinion_scale","ref":"dbaa568a-e6b4-4877-9053-b2e62248c1df"}},{"type":"number","number":1,"field":{"id":"6tfcEh5qjs5X","type":"opinion_scale","ref":"54321947-2add-4751-8be9-3d821b72c613"}},{"type":"number","number":2,"field":{"id":"4U78KECDJqWU","type":"opinion_scale","ref":"77f7b914-2a73-4d8c-86e0-6e793f3210b4"}},{"type":"number","number":3,"field":{"id":"SHLP1h65r7gQ","type":"opinion_scale","ref":"a47a7dcf-194c-4abb-99bf-b29a9f9fec24"}},{"type":"email","email":"remco.tevreden@gmail.com","field":{"id":"DlXN5R43in4M","type":"email","ref":"bf1f8455-9384-4145-82fd-638373e4835d"}}]}}';
        $data = json_decode($data, true);*/
        $data = $request->all();
        $samplePrint = public_path('assets/report-2.pdf');
        $pdf = new Fpdi();
        $pdf->setSourceFile($samplePrint);

        $pdf->AddFont('Montserrat','','Montserrat-Regular.php');
        $pdf->AddFont('Montserrat','B','Montserrat-Bold.php');
        $pdf->SetFont('Montserrat','', 10);

        $currentPage = 1;
        for ($i = 1; $i <= 3; $i++) {
            $page = $pdf->importPage($i, PageBoundaries::MEDIA_BOX);
            $pdf->AddPage();
            $pdf->useTemplate($page);
            $currentPage = $i;
        }
        $currentPage++;

        if(isset($data['form_response']) && isset($data['form_response']['variables'])){
            $sortedVariables = [];
            foreach ($data['form_response']['variables'] as $variable){
                if ($variable['key'] == self::SCORE_VALUES_2['STRUCTURE']['key']) {
                    $sortedVariables[0] = $variable;
                }
                if ($variable['key'] == self::SCORE_VALUES_2['SOCIAL']['key']) {
                    $sortedVariables[1] = $variable;
                }
                if ($variable['key'] == self::SCORE_VALUES_2['EXPANDING']['key']) {
                    $sortedVariables[2] = $variable;
                }
                if ($variable['key'] == self::SCORE_VALUES_2['IMPEDING']['key']) {
                    $sortedVariables[3] = $variable;
                }
                if ($variable['key'] == self::SCORE_VALUES_2['ENJOY']['key']) {
                    $sortedVariables[4] = $variable;
                }
            }
            ksort($sortedVariables);
            foreach ($sortedVariables as $variable){
                $scoreArray = ['STRUCTURE', 'SOCIAL', 'EXPANDING', 'IMPEDING', 'ENJOY'];
                foreach ($scoreArray as $score) {
                    if ($variable['key'] == self::SCORE_VALUES_2[$score]['key']) {
                        $this->createGraph2($pdf, $currentPage, $variable, $score);
                    }
                }
            }
            $this->createGraph2($pdf, $currentPage,  null, 'YOU');
            $this->addQuestions($pdf, $currentPage, $data);
            $this->createGraph2($pdf, $currentPage,  null, 'SOLUTION');
            for ($j = 5; $j<= 5; $j++){
                $page = $pdf->importPage($j, PageBoundaries::MEDIA_BOX);
                $pdf->AddPage();
                $pdf->useTemplate($page);
            }
        }
        /*return $pdf->Output('I','report.pdf');
        exit;*/
        $fileName = 'uploads/report-'.uniqid().'.pdf';
        $filePath = public_path($fileName);
        $pdf->Output($filePath,'F');
        $email = $this->getEmail($data['form_response']['answers']);

        if(!is_null($email)) {
            return MailService::make()->sendMail($email, __('emails.report2.subject'), __('emails.report2.content'), $data['form_response']['answers'][0]['text'], $filePath, 'report.pdf');
        }else{
            Log::info('No email found: '.json_encode($data));
        }
    }

    /**
     * create a chart with provided variables
     *
     * @param $pdf
     * @param $pageNumber
     * @param $variable
     * @param $scoreType
     */
    public function createGraph($pdf, $pageNumber, $variable = null,$scoreType = null)
    {
        $page = $pdf->importPage($pageNumber, PageBoundaries::MEDIA_BOX);
        $pdf->AddPage();
        $pdf->useTemplate($page);

        $pdf->SetXY(10, 15);
        $pdf->SetFont('', 'B', 14);
        $pdf->SetTextColor(42,80,46);
        $pdf->Write(0, sprintf("%02d",$this->pageCount).' '.self::SCORE_VALUES[$scoreType]['pageTitle']);
        $this->pageCount++;
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetFont('', '', 11);
        $type = 'You';
        $chartUrl = "";

        if(self::SCORE_VALUES[$scoreType]['graph']) {
            if ($variable['number'] < self::SCORE_VALUES[$scoreType]['benchmark']) {
                $type = 'less';
            } elseif ($variable['number'] > self::SCORE_VALUES[$scoreType]['benchmark']) {
                $type = 'greater';
            } elseif ($variable['number'] = self::SCORE_VALUES[$scoreType]['benchmark']) {
                $type = 'equal';
            }

            $chartConfig = '{
                      "type": "doughnut",
                      "data": {
                            datasets: [{
                                data: [' . $variable['number'] . ', ' . self::SCORE_VALUES[$scoreType]['benchmark'] . '],
                                backgroundColor: [
                                    "#848686",
                                    "#2E5C2D",
                                ]
                            }],

                            // These labels appear in the legend and in the tooltips when hovering different arcs
                            labels: [
                                "Score",
                                "Benchmark",
                            ]
                        },
                        options: {
                            plugins: {
                              datalabels: {
                                color: "#fff",
                                font: {
                                  weight: "bold",
                                  size: 18,
                                }
                              }
                            }
                        }
                    }';

            $chartUrl = 'https://quickchart.io/chart?w=500&h=300&c=' . urlencode($chartConfig);
        }

        if($type != "") {

            $yCoordinate = 25;
            $pdf->SetFontSize(11);
            foreach (self::SCORE_VALUES[$scoreType]['pageText'] as $line){
                $pdf->SetXY(10, $yCoordinate);
                if(is_array($line)){
                    $pdf->SetFont('', 'B', 12);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', $line['bold']));
                    $pdf->SetFont('', '', 11);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', $line['text']));
                }else {
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', $line));
                }
                $yCoordinate += 5;
            }

            if(self::SCORE_VALUES[$scoreType]['graph']) {
                $pdf->Image($chartUrl, 60, 90, 90, 0, 'PNG');

                $pdf->SetFont('', 'B', 12);
                $text = self::SCORE_VALUES[$scoreType]['text'][$type]['heading'];
                $pdf->SetXY(10, 155);
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', $text));

                $pdf->SetFont('', '', 10);
                foreach (self::SCORE_VALUES[$scoreType]['text'][$type]['paragraph'] as $index => $paraText) {
                    $pdf->SetXY(10, (165 + ($index * 5)));
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', $paraText));
                }
            }
        }
    }

    public function createGraph2($pdf, $pageNumber, $variable = null,$scoreType = null)
    {
        $page = $pdf->importPage($pageNumber, PageBoundaries::MEDIA_BOX);
        $pdf->AddPage();
        $pdf->useTemplate($page);

        $pdf->SetXY(10, 15);
        $pdf->SetFont('', 'B', 12);
        $pdf->SetTextColor(42,80,46);
        $pdf->Write(0, sprintf("%02d",$this->pageCount).' '.self::SCORE_VALUES_2[$scoreType]['pageTitle']);
        $this->pageCount++;
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetFont('', '', 11);
        $type = 'You';
        $chartUrl = "";

        if(self::SCORE_VALUES_2[$scoreType]['graph']) {
            if ($variable['number'] < self::SCORE_VALUES_2[$scoreType]['benchmark']) {
                $type = 'less';
            } elseif ($variable['number'] > self::SCORE_VALUES_2[$scoreType]['benchmark']) {
                $type = 'greater';
            } elseif ($variable['number'] = self::SCORE_VALUES_2[$scoreType]['benchmark']) {
                $type = 'equal';
            }

            $chartConfig = '{
                      "type": "doughnut",
                      "data": {
                            datasets: [{
                                data: [' . $variable['number'] . ', ' . self::SCORE_VALUES_2[$scoreType]['benchmark'] . '],
                                backgroundColor: [
                                    "#848686",
                                    "#2E5C2D",
                                ]
                            }],

                            // These labels appear in the legend and in the tooltips when hovering different arcs
                            labels: [
                                "Score",
                                "Benchmark",
                            ]
                        },
                        options: {
                            plugins: {
                              datalabels: {
                                color: "#fff",
                                font: {
                                  weight: "bold",
                                  size: 18,
                                }
                              }
                            }
                        }
                    }';

            $chartUrl = 'https://quickchart.io/chart?w=500&h=300&c=' . urlencode($chartConfig);
        }

        if($type != "") {

            $yCoordinate = 25;
            $pdf->SetFontSize(11);
            foreach (self::SCORE_VALUES_2[$scoreType]['pageText'] as $line){
                $pdf->SetXY(10, $yCoordinate);
                if(is_array($line)){
                    $pdf->SetFont('', 'B', 12);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', $line['bold']));
                    $pdf->SetFont('', '', 11);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', $line['text']));
                }else {
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', $line));
                }
                $yCoordinate += 5;
            }

            if(self::SCORE_VALUES_2[$scoreType]['graph']) {
                $pdf->Image($chartUrl, 60, 90, 90, 0, 'PNG');

                /*$pdf->SetFont('', 'B', 12);
                $text = self::SCORE_VALUES_2[$scoreType]['text'][$type]['heading'];
                $pdf->SetXY(10, 155);
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', $text));*/

                $pdf->SetFont('', '', 10);
                foreach (self::SCORE_VALUES_2[$scoreType]['text'][$type]['paragraph'] as $index => $paraText) {
                    $pdf->SetXY(10, (165 + ($index * 5)));
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', $paraText));
                }
            }
        }
    }

    /**
     * add questions
     *
     * @param $pdf
     * @param $pageNumber
     * @param $data
     */
    public function addQuestions($pdf, $pageNumber, $data)
    {
        $page = $pdf->importPage($pageNumber, PageBoundaries::MEDIA_BOX);
        $pdf->AddPage();
        $pdf->useTemplate($page);
        $yPosition = 25;
        $xPosition = 10;
        $pdf->SetFont('', '', 10);

        $pdf->SetXY(10, 15);
        $pdf->SetFont('', 'B', 14);
        $pdf->SetTextColor(42,80,46);
        $pdf->Write(0, sprintf("%02d",$this->pageCount).' '.'Bijlage: je antwoorden');
        $this->pageCount++;
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetFont('', '', 11);

        foreach ($data['form_response']['definition']['fields'] as $key => $question){
            if($key == 0 || $key == (count($data['form_response']['definition']['fields']) - 1))
                continue;

            $dataType = $data['form_response']['answers'][$key]['type'];
            if($dataType == 'number') {
                if (strlen($question['title']) > 70) {
                    $result = $this->splitString($question['title']);
                    $xPosition = 10;
                    foreach ($result as $index => $newText) {
                        $numbering = "";
                        if ($index == 0) {
                            $numbering = sprintf("%02d", $key);
                            $pdf->SetXY(180, $yPosition);
                            $pdf->Write(0, iconv('UTF-8', 'windows-1252', $data['form_response']['answers'][$key][$dataType]));
                            $pdf->SetXY(10, $yPosition);
                        }else{
                            $xPosition = 15;
                        }
                        $pdf->SetXY($xPosition, $yPosition);
                        $pdf->Write(0, iconv('UTF-8', 'windows-1252', trim($numbering . ' ' . $newText)));
                        if($index != count($result) - 1) {
                            $yPosition += 4;
                        }
                    }
                } else {
                    $pdf->SetXY(10, $yPosition);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', sprintf("%02d", $key) . ' ' . $question['title']));
                    $pdf->SetXY(180, $yPosition);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', $data['form_response']['answers'][$key][$dataType]));
                    $pdf->SetXY(10, $yPosition);
                }
            }
            $yPosition += 5;
        }
    }

    /**
     * @param $string
     * @param int $chars
     * @return false|string[]
     */
    public function splitString($string, $chars = 80)
    {
        $string = wordwrap($string, $chars, '\n', true);
        return explode('\n', $string);
    }

    /**
     * get email from answers
     *
     * @param $data
     * @return null
     */
    public function getEmail($data)
    {
        foreach ($data as $answer){
            if($answer['type'] == 'email'){
                return $answer[$answer['type']];
            }
        }
        return null;
    }
}
