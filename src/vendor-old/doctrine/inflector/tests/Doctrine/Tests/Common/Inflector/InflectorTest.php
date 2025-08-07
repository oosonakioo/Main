<?php

namespace Doctrine\Tests\Common\Inflector;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\Tests\DoctrineTestCase;

class InflectorTest extends DoctrineTestCase
{
    /**
     * Singular & Plural test data. Returns an array of sample words.
     *
     * @return array
     */
    public function dataSampleWords()
    {
        Inflector::reset();

        // In the format array('singular', 'plural')
        return [
            ['', ''],
            ['Alias', 'Aliases'],
            ['alumnus', 'alumni'],
            ['analysis', 'analyses'],
            ['aquarium', 'aquaria'],
            ['arch', 'arches'],
            ['atlas', 'atlases'],
            ['axe', 'axes'],
            ['baby', 'babies'],
            ['bacillus', 'bacilli'],
            ['bacterium', 'bacteria'],
            ['bureau', 'bureaus'],
            ['bus', 'buses'],
            ['Bus', 'Buses'],
            ['cactus', 'cacti'],
            ['cafe', 'cafes'],
            ['calf', 'calves'],
            ['categoria', 'categorias'],
            ['chateau', 'chateaux'],
            ['cherry', 'cherries'],
            ['child', 'children'],
            ['church', 'churches'],
            ['circus', 'circuses'],
            ['city', 'cities'],
            ['cod', 'cod'],
            ['cookie', 'cookies'],
            ['copy', 'copies'],
            ['crisis', 'crises'],
            ['criterion', 'criteria'],
            ['curriculum', 'curricula'],
            ['curve', 'curves'],
            ['deer', 'deer'],
            ['demo', 'demos'],
            ['dictionary', 'dictionaries'],
            ['domino', 'dominoes'],
            ['dwarf', 'dwarves'],
            ['echo', 'echoes'],
            ['elf', 'elves'],
            ['emphasis', 'emphases'],
            ['family', 'families'],
            ['fax', 'faxes'],
            ['fish', 'fish'],
            ['flush', 'flushes'],
            ['fly', 'flies'],
            ['focus', 'foci'],
            ['foe', 'foes'],
            ['food_menu', 'food_menus'],
            ['FoodMenu', 'FoodMenus'],
            ['foot', 'feet'],
            ['fungus', 'fungi'],
            ['glove', 'gloves'],
            ['half', 'halves'],
            ['hero', 'heroes'],
            ['hippopotamus', 'hippopotami'],
            ['hoax', 'hoaxes'],
            ['house', 'houses'],
            ['human', 'humans'],
            ['identity', 'identities'],
            ['index', 'indices'],
            ['iris', 'irises'],
            ['kiss', 'kisses'],
            ['knife', 'knives'],
            ['leaf', 'leaves'],
            ['life', 'lives'],
            ['loaf', 'loaves'],
            ['man', 'men'],
            ['matrix', 'matrices'],
            ['matrix_row', 'matrix_rows'],
            ['medium', 'media'],
            ['memorandum', 'memoranda'],
            ['menu', 'menus'],
            ['Menu', 'Menus'],
            ['mess', 'messes'],
            ['moose', 'moose'],
            ['motto', 'mottoes'],
            ['mouse', 'mice'],
            ['neurosis', 'neuroses'],
            ['news', 'news'],
            ['NodeMedia', 'NodeMedia'],
            ['nucleus', 'nuclei'],
            ['oasis', 'oases'],
            ['octopus', 'octopuses'],
            ['pass', 'passes'],
            ['person', 'people'],
            ['plateau', 'plateaux'],
            ['potato', 'potatoes'],
            ['powerhouse', 'powerhouses'],
            ['quiz', 'quizzes'],
            ['radius', 'radii'],
            ['reflex', 'reflexes'],
            ['roof', 'roofs'],
            ['runner-up', 'runners-up'],
            ['scarf', 'scarves'],
            ['scratch', 'scratches'],
            ['series', 'series'],
            ['sheep', 'sheep'],
            ['shelf', 'shelves'],
            ['shoe', 'shoes'],
            ['son-in-law', 'sons-in-law'],
            ['species', 'species'],
            ['splash', 'splashes'],
            ['spy', 'spies'],
            ['stimulus', 'stimuli'],
            ['stitch', 'stitches'],
            ['story', 'stories'],
            ['syllabus', 'syllabi'],
            ['tax', 'taxes'],
            ['terminus', 'termini'],
            ['thesis', 'theses'],
            ['thief', 'thieves'],
            ['tomato', 'tomatoes'],
            ['tooth', 'teeth'],
            ['tornado', 'tornadoes'],
            ['try', 'tries'],
            ['vertex', 'vertices'],
            ['virus', 'viri'],
            ['volcano', 'volcanoes'],
            ['wash', 'washes'],
            ['watch', 'watches'],
            ['wave', 'waves'],
            ['wharf', 'wharves'],
            ['wife', 'wives'],
            ['woman', 'women'],
        ];
    }

    /**
     * testInflectingSingulars method
     *
     * @dataProvider dataSampleWords
     *
     * @return void
     */
    public function test_inflecting_singulars($singular, $plural)
    {
        $this->assertEquals(
            $singular,
            Inflector::singularize($plural),
            "'$plural' should be singularized to '$singular'"
        );
    }

    /**
     * testInflectingPlurals method
     *
     * @dataProvider dataSampleWords
     *
     * @return void
     */
    public function test_inflecting_plurals($singular, $plural)
    {
        $this->assertEquals(
            $plural,
            Inflector::pluralize($singular),
            "'$singular' should be pluralized to '$plural'"
        );
    }

    /**
     * testCustomPluralRule method
     *
     * @return void
     */
    public function test_custom_plural_rule()
    {
        Inflector::reset();
        Inflector::rules('plural', ['/^(custom)$/i' => '\1izables']);

        $this->assertEquals(Inflector::pluralize('custom'), 'customizables');

        Inflector::rules('plural', ['uninflected' => ['uninflectable']]);

        $this->assertEquals(Inflector::pluralize('uninflectable'), 'uninflectable');

        Inflector::rules('plural', [
            'rules' => ['/^(alert)$/i' => '\1ables'],
            'uninflected' => ['noflect', 'abtuse'],
            'irregular' => ['amaze' => 'amazable', 'phone' => 'phonezes'],
        ]);

        $this->assertEquals(Inflector::pluralize('noflect'), 'noflect');
        $this->assertEquals(Inflector::pluralize('abtuse'), 'abtuse');
        $this->assertEquals(Inflector::pluralize('alert'), 'alertables');
        $this->assertEquals(Inflector::pluralize('amaze'), 'amazable');
        $this->assertEquals(Inflector::pluralize('phone'), 'phonezes');
    }

    /**
     * testCustomSingularRule method
     *
     * @return void
     */
    public function test_custom_singular_rule()
    {
        Inflector::reset();
        Inflector::rules('singular', ['/(eple)r$/i' => '\1', '/(jente)r$/i' => '\1']);

        $this->assertEquals(Inflector::singularize('epler'), 'eple');
        $this->assertEquals(Inflector::singularize('jenter'), 'jente');

        Inflector::rules('singular', [
            'rules' => ['/^(bil)er$/i' => '\1', '/^(inflec|contribu)tors$/i' => '\1ta'],
            'uninflected' => ['singulars'],
            'irregular' => ['spins' => 'spinor'],
        ]);

        $this->assertEquals(Inflector::singularize('inflectors'), 'inflecta');
        $this->assertEquals(Inflector::singularize('contributors'), 'contributa');
        $this->assertEquals(Inflector::singularize('spins'), 'spinor');
        $this->assertEquals(Inflector::singularize('singulars'), 'singulars');
    }

    /**
     * test that setting new rules clears the inflector caches.
     *
     * @return void
     */
    public function test_rules_clears_caches()
    {
        Inflector::reset();

        $this->assertEquals(Inflector::singularize('Bananas'), 'Banana');
        $this->assertEquals(Inflector::pluralize('Banana'), 'Bananas');

        Inflector::rules('singular', [
            'rules' => ['/(.*)nas$/i' => '\1zzz'],
        ]);

        $this->assertEquals('Banazzz', Inflector::singularize('Bananas'), 'Was inflected with old rules.');

        Inflector::rules('plural', [
            'rules' => ['/(.*)na$/i' => '\1zzz'],
            'irregular' => ['corpus' => 'corpora'],
        ]);

        $this->assertEquals(Inflector::pluralize('Banana'), 'Banazzz', 'Was inflected with old rules.');
        $this->assertEquals(Inflector::pluralize('corpus'), 'corpora', 'Was inflected with old irregular form.');
    }

    /**
     * Test resetting inflection rules.
     *
     * @return void
     */
    public function test_custom_rule_with_reset()
    {
        Inflector::reset();

        $uninflected = ['atlas', 'lapis', 'onibus', 'pires', 'virus', '.*x'];
        $pluralIrregular = ['as' => 'ases'];

        Inflector::rules('singular', [
            'rules' => ['/^(.*)(a|e|o|u)is$/i' => '\1\2l'],
            'uninflected' => $uninflected,
        ], true);

        Inflector::rules('plural', [
            'rules' => [
                '/^(.*)(a|e|o|u)l$/i' => '\1\2is',
            ],
            'uninflected' => $uninflected,
            'irregular' => $pluralIrregular,
        ], true);

        $this->assertEquals(Inflector::pluralize('Alcool'), 'Alcoois');
        $this->assertEquals(Inflector::pluralize('Atlas'), 'Atlas');
        $this->assertEquals(Inflector::singularize('Alcoois'), 'Alcool');
        $this->assertEquals(Inflector::singularize('Atlas'), 'Atlas');
    }

    /**
     * Test basic ucwords functionality.
     *
     * @return void
     */
    public function test_ucwords()
    {
        $this->assertSame('Top-O-The-Morning To All_of_you!', Inflector::ucwords('top-o-the-morning to all_of_you!'));
    }

    /**
     * Test ucwords functionality with custom delimeters.
     *
     * @return void
     */
    public function test_ucwords_with_custom_delimeters()
    {
        $this->assertSame('Top-O-The-Morning To All_Of_You!', Inflector::ucwords('top-o-the-morning to all_of_you!', '-_ '));
    }
}
