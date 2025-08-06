<?php

return [
	'mode'                 => '',
	'format'               => 'A4',
	'default_font_size'    => '14',
	'default_font'         => 'sans-serif',
	'margin_left'          => 5,
	'margin_right'         => 5,
	'margin_top'           => 5,
	'margin_bottom'        => 5,
	'margin_header'        => 0,
	'margin_footer'        => 0,
	'orientation'          => 'P',
	'title'                => 'KIS Invoice',
	'author'               => '',
	'watermark'            => '',
	'show_watermark'       => false,
	'watermark_font'       => 'sans-serif',
	'display_mode'         => 'real',
	'watermark_text_alpha' => 0.1,


  'custom_font_path' => base_path('/resources/fonts/'), // don't forget the trailing slash!
  'custom_font_data' => [
    'examplefont' => [
      'R'  => 'AngsanaNew.ttf',    // regular font
      'B'  => 'AngsanaNew-Bold.ttf',       // optional: bold font
      'I'  => 'AngsanaNew-Italic.ttf',     // optional: italic font
      'BI' => 'AngsanaNew-BoldItalic.ttf' // optional: bold-italic font
    ],
		'sarabanfont' => [
			'R'  => 'THSarabunNew.ttf',    // regular font
			'B'  => 'THSarabunNew-Bold.ttf',       // optional: bold font
			'I'  => 'THSarabunNew-Italic.ttf',     // optional: italic font
			'BI' => 'THSarabunNew-BoldItalic.ttf' // optional: bold-italic font
		]
    // ...add as many as you want.
  ]

];
