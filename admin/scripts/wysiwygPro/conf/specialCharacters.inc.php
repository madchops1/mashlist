<?php
if (!defined('IN_WPRO')) exit;
// HTML special characters as defined by the W3C: http://www.w3.org/TR/REC-html40/sgml/entities.html

$symbols = array(
	
	//array(
			'Common' => array(
				'#160',
				'#8216',
				'#8217',
				
				'#8220',
				'#8221',
				'#8212',
				
				'#163',
				'#8364',
				'#165',
				
				'#169',
				'#174',
				'#8482',
				
				'#167',
				'#182',
				'#8230',
			),
			
			// latin 1 //
			'Latin 1' => array(
				'#160',
				'#161',
				'#162',
				'#163',
				'#164',
				'#165',
				'#166',
				'#167',
				'#168',
				'#169',
				'#170',
				'#171',
				'#172',
				'#173',
				'#174',
				'#175',
				'#176',
				'#177',
				'#178',
				'#179',
				'#180',
				'#181',
				'#182',
				'#183',
				'#184',
				'#185',
				'#186',
				'#187',
				'#188',
				'#189',
				'#190',
				'#192',
				'#193',
				'#194',
				'#195',
				'#197',
				'#198',
				'#199',
				'#200',
				'#201',
				'#202',
				'#203',
				'#204',
				'#205',
				'#206',
				'#207',
				'#208',
				'#209',
				'#210',
				'#211',
				'#212',
				'#213',
				'#214',
				'#215',
				'#216',
				'#217',
				'#218',
				'#219',
				'#220',
				'#221',
				'#222',
				'#223',
				'#224',
				'#225',
				'#226',
				'#227',
				'#228',
				'#229',
				'#230',
				'#231',
				'#232',
				'#233',
				'#234',
				'#235',
				'#236',
				'#237',
				'#238',
				'#239',
				'#240',
				'#241',
				'#242',
				'#243',
				'#244',
				'#245',
				'#246',
				'#247',
				'#248',
				'#249',
				'#250',
				'#251',
				'#252',
				'#253',
				'#254',
				'#255',
			//),
			
			//'Markup-significant and Internationalization' => array(
			
			// C0 Controls and Basic Latin
			//'C0 Controls and Basic Latin' => array (
				'#34',
				'#38',
				'#60',
				'#62',
			//),
			
			// Latin Extended-A
			//'Latin Extended-A' => array(
				'#338',
				'#339',
				'#352',
				'#353',
				'#376',
			//),
			
			// Latin Extended-B
			//'Latin Extended-B' => array(
				'#402',
			),
			
			// Spacing Modifier Letters
			'Spacing Modifier Letters' => array(
				'#710',
				'#732',
			),
			
			// General Punctuation
			'General Punctuation' => array(
				'#8194',
				'#8195',
				'#8201',
				'#8204',
				'#8205',
				'#8206',
				'#8207',
				'#8211',
				'#8212',
				'#8216',
				'#8217',
				'#8218',
				'#8220',
				'#8221',
				'#8222',
				'#8224',
				'#8225',
				'#8240',
				'#8249',
				'#8250',
				'#8364',

				'#8226',
				'#8230',
				'#8242',
				'#8243',
				'#8254',
				'#8260',
			//),
			
			// General punctuation
			//'GeneralPunctuation2' => array(
				'#8226',
				'#8230',
				'#8242',
				'#8243',
				'#8254',
				'#8260',
			),

			
		//),
		
		//'Symbols, mathematical symbols, and Greek letters' => array(
			// mathematical, greek and symbols //
			

			
			// Greek
			'Greek Symbols' => array(
				'#913',
				'#914',
				'#915',
				'#916',
				'#917',
				'#918',
				'#919',
				'#920',
				'#921',
				'#922',
				'#923',
				'#924',
				'#925',
				'#926',
				'#927',
				'#928',
				'#929',
				'#931',
				'#932',
				'#933',
				'#934',
				'#935',
				'#936',
				'#937',
				'#945',
				'#946',
				'#947',
				'#948',
				'#949',
				'#950',
				'#951',
				'#952',
				'#953',
				'#954',
				'#955',
				'#956',
				'#957',
				'#958',
				'#959',
				'#960',
				'#961',
				'#962',
				'#963',
				'#964',
				'#965',
				'#966',
				'#967',
				'#968',
				'#969',
				'#977',
				'#978',
				'#982',
			),
			
		
			// Letterlike symbols
			'Letter-like Symbols' => array(
				'#8472',
				'#8465',
				'#8476',
				'#8482',
				'#8501',
				'warning',
			),

			// Arrows
			'Arrows' => array(
				'#8592',
				'#8593',
				'#8594',
				'#8595',
				'#8596',
				'#8629',
				'#8656',
				'#8657',
				'#8658',
				'#8659',
				'#8660',
				'warning',
			),
			
			// Mathematical Operators
			'Mathematical Operators' => array(
				'#8794',
				'#8706',
				'#8707',
				'#8709',
				'#8711',
				'#8712',
				'#8713',
				'#8715',
				'#8719',
				'#8721',
				'#8722',
				'#8727',
				'#8730',
				'#8733',
				'#8734',
				'#8736',
				'#8743',
				'#8744',
				'#8745',
				'#8746',
				'#8747',
				'#8756',
				'#8764',
				'#8773',
				'#8776',
				'#8800',
				'#8801',
				'#8804',
				'#8805',
				'#8834',
				'#8835',
				'#8836',
				'#8838',
				'#8839',
				'#8853',
				'#8855',
				'#8869',
				'#8901',
				'warning',
			),
			
			// Miscellaneous Technical
			'Miscellaneous Technical' => array(
				'#8968',
				'#8969',
				'#8970',
				'#8971',
				'#9001',
				'#9002',
				'warning',
			),
			
			// Miscellaneous Symbols
			'Miscellaneous Symbols' => array(
				'#9824',
				'#9827',
				'#9829',
				'#9830',
				'warning',
			),
				
		//),			
);

?>