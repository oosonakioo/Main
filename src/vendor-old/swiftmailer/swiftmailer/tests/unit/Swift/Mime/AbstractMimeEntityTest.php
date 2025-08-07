<?php

require_once dirname(dirname(dirname(__DIR__))).'/fixtures/MimeEntityFixture.php';

abstract class Swift_Mime_AbstractMimeEntityTest extends \SwiftMailerTestCase
{
    public function test_get_headers_returns_header_set()
    {
        $headers = $this->_createHeaderSet();
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $this->assertSame($headers, $entity->getHeaders());
    }

    public function test_content_type_is_returned_from_header()
    {
        $ctype = $this->_createHeader('Content-Type', 'image/jpeg-test');
        $headers = $this->_createHeaderSet(['Content-Type' => $ctype]);
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $this->assertEquals('image/jpeg-test', $entity->getContentType());
    }

    public function test_content_type_is_set_in_header()
    {
        $ctype = $this->_createHeader('Content-Type', 'text/plain', [], false);
        $headers = $this->_createHeaderSet(['Content-Type' => $ctype]);

        $ctype->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('image/jpeg');
        $ctype->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes()
            ->with(\Mockery::not('image/jpeg'));

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setContentType('image/jpeg');
    }

    public function test_content_type_header_is_added_if_none_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addParameterizedHeader')
            ->once()
            ->with('Content-Type', 'image/jpeg');
        $headers->shouldReceive('addParameterizedHeader')
            ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setContentType('image/jpeg');
    }

    public function test_content_type_can_be_set_via_set_body()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addParameterizedHeader')
            ->once()
            ->with('Content-Type', 'text/html');
        $headers->shouldReceive('addParameterizedHeader')
            ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setBody('<b>foo</b>', 'text/html');
    }

    public function test_get_encoder_from_constructor()
    {
        $encoder = $this->_createEncoder('base64');
        $entity = $this->_createEntity($this->_createHeaderSet(), $encoder,
            $this->_createCache()
        );
        $this->assertSame($encoder, $entity->getEncoder());
    }

    public function test_set_and_get_encoder()
    {
        $encoder = $this->_createEncoder('base64');
        $headers = $this->_createHeaderSet();
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setEncoder($encoder);
        $this->assertSame($encoder, $entity->getEncoder());
    }

    public function test_setting_encoder_updates_transfer_encoding()
    {
        $encoder = $this->_createEncoder('base64');
        $encoding = $this->_createHeader(
            'Content-Transfer-Encoding', '8bit', [], false
        );
        $headers = $this->_createHeaderSet([
            'Content-Transfer-Encoding' => $encoding,
        ]);
        $encoding->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('base64');
        $encoding->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setEncoder($encoder);
    }

    public function test_setting_encoder_adds_encoding_header_if_none_present()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addTextHeader')
            ->once()
            ->with('Content-Transfer-Encoding', 'something');
        $headers->shouldReceive('addTextHeader')
            ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setEncoder($this->_createEncoder('something'));
    }

    public function test_id_is_returned_from_header()
    {
        /* -- RFC 2045, 7.
        In constructing a high-level user agent, it may be desirable to allow
        one body to make reference to another.  Accordingly, bodies may be
        labelled using the "Content-ID" header field, which is syntactically
        identical to the "Message-ID" header field
        */

        $cid = $this->_createHeader('Content-ID', 'zip@button');
        $headers = $this->_createHeaderSet(['Content-ID' => $cid]);
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $this->assertEquals('zip@button', $entity->getId());
    }

    public function test_id_is_set_in_header()
    {
        $cid = $this->_createHeader('Content-ID', 'zip@button', [], false);
        $headers = $this->_createHeaderSet(['Content-ID' => $cid]);

        $cid->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('foo@bar');
        $cid->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setId('foo@bar');
    }

    public function test_id_is_auto_generated()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertRegExp('/^.*?@.*?$/D', $entity->getId());
    }

    public function test_generate_id_creates_new_id()
    {
        $headers = $this->_createHeaderSet();
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $id1 = $entity->generateId();
        $id2 = $entity->generateId();
        $this->assertNotEquals($id1, $id2);
    }

    public function test_generate_id_sets_new_id()
    {
        $headers = $this->_createHeaderSet();
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $id = $entity->generateId();
        $this->assertEquals($id, $entity->getId());
    }

    public function test_description_is_read_from_header()
    {
        /* -- RFC 2045, 8.
        The ability to associate some descriptive information with a given
        body is often desirable.  For example, it may be useful to mark an
        "image" body as "a picture of the Space Shuttle Endeavor."  Such text
        may be placed in the Content-Description header field.  This header
        field is always optional.
        */

        $desc = $this->_createHeader('Content-Description', 'something');
        $headers = $this->_createHeaderSet(['Content-Description' => $desc]);
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $this->assertEquals('something', $entity->getDescription());
    }

    public function test_description_is_set_in_header()
    {
        $desc = $this->_createHeader('Content-Description', '', [], false);
        $desc->shouldReceive('setFieldBodyModel')->once()->with('whatever');

        $headers = $this->_createHeaderSet(['Content-Description' => $desc]);

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setDescription('whatever');
    }

    public function test_description_header_is_added_if_not_present()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('addTextHeader')
            ->once()
            ->with('Content-Description', 'whatever');
        $headers->shouldReceive('addTextHeader')
            ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setDescription('whatever');
    }

    public function test_set_and_get_max_line_length()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $entity->setMaxLineLength(60);
        $this->assertEquals(60, $entity->getMaxLineLength());
    }

    public function test_encoder_is_used_for_string_generation()
    {
        $encoder = $this->_createEncoder('base64', false);
        $encoder->expects($this->once())
            ->method('encodeString')
            ->with('blah');

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $encoder, $this->_createCache()
        );
        $entity->setBody('blah');
        $entity->toString();
    }

    public function test_max_line_length_is_provided_when_encoding()
    {
        $encoder = $this->_createEncoder('base64', false);
        $encoder->expects($this->once())
            ->method('encodeString')
            ->with('blah', 0, 65);

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $encoder, $this->_createCache()
        );
        $entity->setBody('blah');
        $entity->setMaxLineLength(65);
        $entity->toString();
    }

    public function test_headers_appear_in_string()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('toString')
            ->once()
            ->andReturn(
                "Content-Type: text/plain; charset=utf-8\r\n".
                "X-MyHeader: foobar\r\n"
            );

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $this->assertEquals(
            "Content-Type: text/plain; charset=utf-8\r\n".
            "X-MyHeader: foobar\r\n",
            $entity->toString()
        );
    }

    public function test_set_and_get_body()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $entity->setBody("blah\r\nblah!");
        $this->assertEquals("blah\r\nblah!", $entity->getBody());
    }

    public function test_body_is_appended()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('toString')
            ->once()
            ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setBody("blah\r\nblah!");
        $this->assertEquals(
            "Content-Type: text/plain; charset=utf-8\r\n".
            "\r\n".
            "blah\r\nblah!",
            $entity->toString()
        );
    }

    public function test_get_body_returns_string_from_byte_stream()
    {
        $os = $this->_createOutputStream('byte stream string');
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $entity->setBody($os);
        $this->assertEquals('byte stream string', $entity->getBody());
    }

    public function test_byte_stream_body_is_appended()
    {
        $headers = $this->_createHeaderSet([], false);
        $os = $this->_createOutputStream('streamed');
        $headers->shouldReceive('toString')
            ->once()
            ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setBody($os);
        $this->assertEquals(
            "Content-Type: text/plain; charset=utf-8\r\n".
            "\r\n".
            'streamed',
            $entity->toString()
        );
    }

    public function test_boundary_can_be_retrieved()
    {
        /* -- RFC 2046, 5.1.1.
     boundary := 0*69<bchars> bcharsnospace

     bchars := bcharsnospace / " "

     bcharsnospace := DIGIT / ALPHA / "'" / "(" / ")" /
                                            "+" / "_" / "," / "-" / "." /
                                            "/" / ":" / "=" / "?"
        */

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $this->assertRegExp(
            '/^[a-zA-Z0-9\'\(\)\+_\-,\.\/:=\?\ ]{0,69}[a-zA-Z0-9\'\(\)\+_\-,\.\/:=\?]$/D',
            $entity->getBoundary()
        );
    }

    public function test_boundary_never_changes()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $firstBoundary = $entity->getBoundary();
        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals($firstBoundary, $entity->getBoundary());
        }
    }

    public function test_boundary_can_be_set()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $entity->setBoundary('foobar');
        $this->assertEquals('foobar', $entity->getBoundary());
    }

    public function test_adding_children_generates_boundary_in_headers()
    {
        $child = $this->_createChild();
        $cType = $this->_createHeader('Content-Type', 'text/plain', [], false);
        $cType->shouldReceive('setParameter')
            ->once()
            ->with('boundary', \Mockery::any());
        $cType->shouldReceive('setParameter')
            ->zeroOrMoreTimes();

        $entity = $this->_createEntity($this->_createHeaderSet([
            'Content-Type' => $cType,
        ]),
            $this->_createEncoder(), $this->_createCache()
        );
        $entity->setChildren([$child]);
    }

    public function test_children_of_level_attachment_and_less_cause_multipart_mixed()
    {
        for ($level = Swift_Mime_MimeEntity::LEVEL_MIXED;
            $level > Swift_Mime_MimeEntity::LEVEL_TOP; $level /= 2) {
            $child = $this->_createChild($level);
            $cType = $this->_createHeader(
                'Content-Type', 'text/plain', [], false
            );
            $cType->shouldReceive('setFieldBodyModel')
                ->once()
                ->with('multipart/mixed');
            $cType->shouldReceive('setFieldBodyModel')
                ->zeroOrMoreTimes();

            $entity = $this->_createEntity($this->_createHeaderSet([
                'Content-Type' => $cType, ]),
                $this->_createEncoder(), $this->_createCache()
            );
            $entity->setChildren([$child]);
        }
    }

    public function test_children_of_level_alternative_and_less_cause_multipart_alternative()
    {
        for ($level = Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE;
            $level > Swift_Mime_MimeEntity::LEVEL_MIXED; $level /= 2) {
            $child = $this->_createChild($level);
            $cType = $this->_createHeader(
                'Content-Type', 'text/plain', [], false
            );
            $cType->shouldReceive('setFieldBodyModel')
                ->once()
                ->with('multipart/alternative');
            $cType->shouldReceive('setFieldBodyModel')
                ->zeroOrMoreTimes();

            $entity = $this->_createEntity($this->_createHeaderSet([
                'Content-Type' => $cType, ]),
                $this->_createEncoder(), $this->_createCache()
            );
            $entity->setChildren([$child]);
        }
    }

    public function test_children_of_level_related_and_less_cause_multipart_related()
    {
        for ($level = Swift_Mime_MimeEntity::LEVEL_RELATED;
            $level > Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE; $level /= 2) {
            $child = $this->_createChild($level);
            $cType = $this->_createHeader(
                'Content-Type', 'text/plain', [], false
            );
            $cType->shouldReceive('setFieldBodyModel')
                ->once()
                ->with('multipart/related');
            $cType->shouldReceive('setFieldBodyModel')
                ->zeroOrMoreTimes();

            $entity = $this->_createEntity($this->_createHeaderSet([
                'Content-Type' => $cType, ]),
                $this->_createEncoder(), $this->_createCache()
            );
            $entity->setChildren([$child]);
        }
    }

    public function test_highest_level_child_determines_content_type()
    {
        $combinations = [
            ['levels' => [Swift_Mime_MimeEntity::LEVEL_MIXED,
                Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
                Swift_Mime_MimeEntity::LEVEL_RELATED,
            ],
                'type' => 'multipart/mixed',
            ],
            ['levels' => [Swift_Mime_MimeEntity::LEVEL_MIXED,
                Swift_Mime_MimeEntity::LEVEL_RELATED,
            ],
                'type' => 'multipart/mixed',
            ],
            ['levels' => [Swift_Mime_MimeEntity::LEVEL_MIXED,
                Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            ],
                'type' => 'multipart/mixed',
            ],
            ['levels' => [Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
                Swift_Mime_MimeEntity::LEVEL_RELATED,
            ],
                'type' => 'multipart/alternative',
            ],
        ];

        foreach ($combinations as $combination) {
            $children = [];
            foreach ($combination['levels'] as $level) {
                $children[] = $this->_createChild($level);
            }

            $cType = $this->_createHeader(
                'Content-Type', 'text/plain', [], false
            );
            $cType->shouldReceive('setFieldBodyModel')
                ->once()
                ->with($combination['type']);

            $headerSet = $this->_createHeaderSet(['Content-Type' => $cType]);
            $headerSet->shouldReceive('newInstance')
                ->zeroOrMoreTimes()
                ->andReturnUsing(function () use ($headerSet) {
                    return $headerSet;
                });
            $entity = $this->_createEntity($headerSet,
                $this->_createEncoder(), $this->_createCache()
            );
            $entity->setChildren($children);
        }
    }

    public function test_children_appear_nested_in_string()
    {
        /* -- RFC 2046, 5.1.1.
     (excerpt too verbose to paste here)
     */

        $headers = $this->_createHeaderSet([], false);

        $child1 = new MimeEntityFixture(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            "Content-Type: text/plain\r\n".
            "\r\n".
            'foobar', 'text/plain'
        );

        $child2 = new MimeEntityFixture(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            "Content-Type: text/html\r\n".
            "\r\n".
            '<b>foobar</b>', 'text/html'
        );

        $headers->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn("Content-Type: multipart/alternative; boundary=\"xxx\"\r\n");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setBoundary('xxx');
        $entity->setChildren([$child1, $child2]);

        $this->assertEquals(
            "Content-Type: multipart/alternative; boundary=\"xxx\"\r\n".
            "\r\n".
            "\r\n--xxx\r\n".
            "Content-Type: text/plain\r\n".
            "\r\n".
            "foobar\r\n".
            "\r\n--xxx\r\n".
            "Content-Type: text/html\r\n".
            "\r\n".
            "<b>foobar</b>\r\n".
            "\r\n--xxx--\r\n",
            $entity->toString()
        );
    }

    public function test_mixing_levels_is_hierarchical()
    {
        $headers = $this->_createHeaderSet([], false);
        $newHeaders = $this->_createHeaderSet([], false);

        $part = $this->_createChild(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            "Content-Type: text/plain\r\n".
            "\r\n".
            'foobar'
        );

        $attachment = $this->_createChild(Swift_Mime_MimeEntity::LEVEL_MIXED,
            "Content-Type: application/octet-stream\r\n".
            "\r\n".
            'data'
        );

        $headers->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn("Content-Type: multipart/mixed; boundary=\"xxx\"\r\n");
        $headers->shouldReceive('newInstance')
            ->zeroOrMoreTimes()
            ->andReturn($newHeaders);
        $newHeaders->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn("Content-Type: multipart/alternative; boundary=\"yyy\"\r\n");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setBoundary('xxx');
        $entity->setChildren([$part, $attachment]);

        $this->assertRegExp(
            '~^'.
            "Content-Type: multipart/mixed; boundary=\"xxx\"\r\n".
            "\r\n\r\n--xxx\r\n".
            "Content-Type: multipart/alternative; boundary=\"yyy\"\r\n".
            "\r\n\r\n--(.*?)\r\n".
            "Content-Type: text/plain\r\n".
            "\r\n".
            'foobar'.
            "\r\n\r\n--\\1--\r\n".
            "\r\n\r\n--xxx\r\n".
            "Content-Type: application/octet-stream\r\n".
            "\r\n".
            'data'.
            "\r\n\r\n--xxx--\r\n".
            '$~',
            $entity->toString()
        );
    }

    public function test_setting_encoder_notifies_children()
    {
        $child = $this->_createChild(0, '', false);
        $encoder = $this->_createEncoder('base64');

        $child->shouldReceive('encoderChanged')
            ->once()
            ->with($encoder);

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $entity->setChildren([$child]);
        $entity->setEncoder($encoder);
    }

    public function test_receipt_of_encoder_change_notifies_children()
    {
        $child = $this->_createChild(0, '', false);
        $encoder = $this->_createEncoder('base64');

        $child->shouldReceive('encoderChanged')
            ->once()
            ->with($encoder);

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $entity->setChildren([$child]);
        $entity->encoderChanged($encoder);
    }

    public function test_receipt_of_charset_change_notifies_children()
    {
        $child = $this->_createChild(0, '', false);
        $child->shouldReceive('charsetChanged')
            ->once()
            ->with('windows-874');

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $entity->setChildren([$child]);
        $entity->charsetChanged('windows-874');
    }

    public function test_entity_is_written_to_byte_stream()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $is = $this->_createInputStream(false);
        $is->expects($this->atLeastOnce())
            ->method('write');

        $entity->toByteStream($is);
    }

    public function test_entity_headers_are_comitted_to_byte_stream()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );
        $is = $this->_createInputStream(false);
        $is->expects($this->atLeastOnce())
            ->method('write');
        $is->expects($this->atLeastOnce())
            ->method('commit');

        $entity->toByteStream($is);
    }

    public function test_ordering_text_before_html()
    {
        $htmlChild = new MimeEntityFixture(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            "Content-Type: text/html\r\n".
            "\r\n".
            'HTML PART',
            'text/html'
        );
        $textChild = new MimeEntityFixture(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            "Content-Type: text/plain\r\n".
            "\r\n".
            'TEXT PART',
            'text/plain'
        );
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn("Content-Type: multipart/alternative; boundary=\"xxx\"\r\n");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
        );
        $entity->setBoundary('xxx');
        $entity->setChildren([$htmlChild, $textChild]);

        $this->assertEquals(
            "Content-Type: multipart/alternative; boundary=\"xxx\"\r\n".
            "\r\n\r\n--xxx\r\n".
            "Content-Type: text/plain\r\n".
            "\r\n".
            'TEXT PART'.
            "\r\n\r\n--xxx\r\n".
            "Content-Type: text/html\r\n".
            "\r\n".
            'HTML PART'.
            "\r\n\r\n--xxx--\r\n",
            $entity->toString()
        );
    }

    public function test_unsetting_children_restores_content_type()
    {
        $cType = $this->_createHeader('Content-Type', 'text/plain', [], false);
        $child = $this->_createChild(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE);

        $cType->shouldReceive('setFieldBodyModel')
            ->twice()
            ->with('image/jpeg');
        $cType->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('multipart/alternative');
        $cType->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes()
            ->with(\Mockery::not('multipart/alternative', 'image/jpeg'));

        $entity = $this->_createEntity($this->_createHeaderSet([
            'Content-Type' => $cType,
        ]),
            $this->_createEncoder(), $this->_createCache()
        );

        $entity->setContentType('image/jpeg');
        $entity->setChildren([$child]);
        $entity->setChildren([]);
    }

    public function test_body_is_read_from_cache_when_using_to_string_if_present()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $cache = $this->_createCache(false);
        $cache->shouldReceive('hasKey')
            ->once()
            ->with(\Mockery::any(), 'body')
            ->andReturn(true);
        $cache->shouldReceive('getString')
            ->once()
            ->with(\Mockery::any(), 'body')
            ->andReturn("\r\ncache\r\ncache!");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $cache
        );

        $entity->setBody("blah\r\nblah!");
        $this->assertEquals(
            "Content-Type: text/plain; charset=utf-8\r\n".
            "\r\n".
            "cache\r\ncache!",
            $entity->toString()
        );
    }

    public function test_body_is_added_to_cache_when_using_to_string()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $cache = $this->_createCache(false);
        $cache->shouldReceive('hasKey')
            ->once()
            ->with(\Mockery::any(), 'body')
            ->andReturn(false);
        $cache->shouldReceive('setString')
            ->once()
            ->with(\Mockery::any(), 'body', "\r\nblah\r\nblah!", Swift_KeyCache::MODE_WRITE);

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $cache
        );

        $entity->setBody("blah\r\nblah!");
        $entity->toString();
    }

    public function test_body_is_cleared_from_cache_if_new_body_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $cache = $this->_createCache(false);
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $cache
        );

        $entity->setBody("blah\r\nblah!");
        $entity->toString();

        // We set the expectation at this point because we only care what happens when calling setBody()
        $cache->shouldReceive('clearKey')
            ->once()
            ->with(\Mockery::any(), 'body');

        $entity->setBody("new\r\nnew!");
    }

    public function test_body_is_not_cleared_from_cache_if_same_body_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $cache = $this->_createCache(false);
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $cache
        );

        $entity->setBody("blah\r\nblah!");
        $entity->toString();

        // We set the expectation at this point because we only care what happens when calling setBody()
        $cache->shouldReceive('clearKey')
            ->never();

        $entity->setBody("blah\r\nblah!");
    }

    public function test_body_is_cleared_from_cache_if_new_encoder_set()
    {
        $headers = $this->_createHeaderSet([], false);
        $headers->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $cache = $this->_createCache(false);
        $otherEncoder = $this->_createEncoder();
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $cache
        );

        $entity->setBody("blah\r\nblah!");
        $entity->toString();

        // We set the expectation at this point because we only care what happens when calling setEncoder()
        $cache->shouldReceive('clearKey')
            ->once()
            ->with(\Mockery::any(), 'body');

        $entity->setEncoder($otherEncoder);
    }

    public function test_body_is_read_from_cache_when_using_to_byte_stream_if_present()
    {
        $is = $this->_createInputStream();
        $cache = $this->_createCache(false);
        $cache->shouldReceive('hasKey')
            ->once()
            ->with(\Mockery::any(), 'body')
            ->andReturn(true);
        $cache->shouldReceive('exportToByteStream')
            ->once()
            ->with(\Mockery::any(), 'body', $is);

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $cache
        );
        $entity->setBody('foo');

        $entity->toByteStream($is);
    }

    public function test_body_is_added_to_cache_when_using_to_byte_stream()
    {
        $is = $this->_createInputStream();
        $cache = $this->_createCache(false);
        $cache->shouldReceive('hasKey')
            ->once()
            ->with(\Mockery::any(), 'body')
            ->andReturn(false);
        $cache->shouldReceive('getInputByteStream')
            ->once()
            ->with(\Mockery::any(), 'body');

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $cache
        );
        $entity->setBody('foo');

        $entity->toByteStream($is);
    }

    public function test_fluid_interface()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
        );

        $this->assertSame($entity,
            $entity
                ->setContentType('text/plain')
                ->setEncoder($this->_createEncoder())
                ->setId('foo@bar')
                ->setDescription('my description')
                ->setMaxLineLength(998)
                ->setBody('xx')
                ->setBoundary('xyz')
                ->setChildren([])
        );
    }

    abstract protected function _createEntity($headers, $encoder, $cache);

    protected function _createChild($level = null, $string = '', $stub = true)
    {
        $child = $this->getMockery('Swift_Mime_MimeEntity')->shouldIgnoreMissing();
        if (isset($level)) {
            $child->shouldReceive('getNestingLevel')
                ->zeroOrMoreTimes()
                ->andReturn($level);
        }
        $child->shouldReceive('toString')
            ->zeroOrMoreTimes()
            ->andReturn($string);

        return $child;
    }

    protected function _createEncoder($name = 'quoted-printable', $stub = true)
    {
        $encoder = $this->getMockBuilder('Swift_Mime_ContentEncoder')->getMock();
        $encoder->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $encoder->expects($this->any())
            ->method('encodeString')
            ->will($this->returnCallback(function () {
                $args = func_get_args();

                return array_shift($args);
            }));

        return $encoder;
    }

    protected function _createCache($stub = true)
    {
        return $this->getMockery('Swift_KeyCache')->shouldIgnoreMissing();
    }

    protected function _createHeaderSet($headers = [], $stub = true)
    {
        $set = $this->getMockery('Swift_Mime_HeaderSet')->shouldIgnoreMissing();
        $set->shouldReceive('get')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function ($key) use ($headers) {
                return $headers[$key];
            });
        $set->shouldReceive('has')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function ($key) use ($headers) {
                return array_key_exists($key, $headers);
            });

        return $set;
    }

    protected function _createHeader($name, $model = null, $params = [], $stub = true)
    {
        $header = $this->getMockery('Swift_Mime_ParameterizedHeader')->shouldIgnoreMissing();
        $header->shouldReceive('getFieldName')
            ->zeroOrMoreTimes()
            ->andReturn($name);
        $header->shouldReceive('getFieldBodyModel')
            ->zeroOrMoreTimes()
            ->andReturn($model);
        $header->shouldReceive('getParameter')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function ($key) use ($params) {
                return $params[$key];
            });

        return $header;
    }

    protected function _createOutputStream($data = null, $stub = true)
    {
        $os = $this->getMockery('Swift_OutputByteStream');
        if (isset($data)) {
            $os->shouldReceive('read')
                ->zeroOrMoreTimes()
                ->andReturnUsing(function () use ($data) {
                    static $first = true;
                    if (! $first) {
                        return false;
                    }

                    $first = false;

                    return $data;
                });
            $os->shouldReceive('setReadPointer')
                ->zeroOrMoreTimes();
        }

        return $os;
    }

    protected function _createInputStream($stub = true)
    {
        return $this->getMockBuilder('Swift_InputByteStream')->getMock();
    }
}
