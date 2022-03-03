<?php
namespace app\services;


use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use App\Entities\ExportEntity;

class ExportService
{
    public function exec(ExportEntity $entity)
    {
        $filename = public_path() . '/uploads/' . time() . '.xlsx';    

        $border = (new BorderBuilder())
            ->setBorderTop   (Color::BLACK, Border::WIDTH_THIN, Border::STYLE_DASHED)
            ->setBorderLeft  (Color::BLACK, Border::WIDTH_THIN, Border::STYLE_DASHED)
            ->setBorderRight (Color::BLACK, Border::WIDTH_THIN, Border::STYLE_DASHED)
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_DASHED)
            ->build();

        $headerStyle = (new StyleBuilder())
            ->setBorder($border)
            ->setFontSize(11)
            ->setBackgroundColor(Color::toARGB('EAEAEA'))
            ->setFontBold()
            ->build();

        $style = (new StyleBuilder())
            ->setBorder($border)
            ->setFontSize(11)
            ->build();

        $rows   = [];
        $rows[] = WriterEntityFactory::createRowFromArray($entity->getHeader(), $headerStyle);
        foreach ($entity->getBody() as $k => $item) {
            $rows[] = WriterEntityFactory::createRowFromArray(is_array($item) ? $item : [$item], $style);
        }

        $writer = WriterEntityFactory::createWriterFromFile($filename);

        $writer
            ->openToFile($filename)
            ->addRows($rows)
            ->close();

        $this->send($filename);
    }

    protected function send($path)
    {
        if (file_exists($path)) {
            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($path));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        }
    }
}