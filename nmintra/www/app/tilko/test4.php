<?php

require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

// PDF 파일 경로
$pdfFilePath = 'example.pdf';

// PDF 파서 객체 생성
$parser = new Parser();

// PDF 파일 파싱
$pdf = $parser->parseFile($pdfFilePath);

// PDF에서 텍스트 추출
$text = $pdf->getText();

// 추출된 텍스트 출력
echo $text;

// XML로 변환하기
$xml = new SimpleXMLElement('<root/>');
$xml->addChild('content', htmlspecialchars($text));

// XML 파일로 저장하기
file_put_contents('output.xml', $xml->asXML());

?>
