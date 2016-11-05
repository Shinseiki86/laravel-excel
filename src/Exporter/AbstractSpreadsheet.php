<?php
namespace Cyberduck\LaravelExcel\Exporter;

use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\StyleBuilder;
use Illuminate\Database\Eloquent\Collection;
use Box\Spout\Writer\WriterFactory;
use Cyberduck\LaravelExcel\Serialiser\BasicSerialiser;
use Cyberduck\LaravelExcel\Contract\SerialiserInterface;
use Cyberduck\LaravelExcel\Contract\ExporterInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractSpreadsheet implements ExporterInterface
{
    protected $data;
    protected $type;
    protected $serialiser;
    protected $headerStyle = null;

    public function __construct()
    {
        $this->data = [];
        $this->type = $this->getType();
        $this->serialiser = new BasicSerialiser();
    }


    public function load(Collection $data)
    {
        $this->data = $data;
        return $this;
    }

    public function loadArray(array $data)
    {
        $this->data = $data;
        return $this;
    }


    public function setSerialiser(SerialiserInterface $serialiser)
    {
        $this->serialiser = $serialiser;
        return $this;
    }

    abstract public function getType();

    public function save($filename)
    {
        $writer = $this->create();
        $writer->openToFile($filename);
        $writer = $this->makeRows($writer);
        $writer->close();
    }

    public function stream($filename)
    {
        $writer = $this->create();
        $writer->openToBrowser($filename);
        $writer = $this->makeRows($writer);
        $writer->close();
    }

    protected function create()
    {
        return WriterFactory::create($this->type);
    }

    public function createHeaderStyle($isBold = true, $fontSize = 12, $color = Color::BLACK, $wrapText = false, $backgroundColor = Color::LIGHT_BLUE)
    {
        $style = new StyleBuilder();

        if($isBold) $style = $style->setFontBold();

        $style = $style->setFontSize($fontSize)
            ->setFontColor($color);

        if($wrapText) $style = $style->setShouldWrapText();

        $style = $style->setBackgroundColor($backgroundColor)->build();

        $this->headerStyle = $style;

        return $this;
    }

    protected function makeRows($writer)
    {
        //heading
        $headerRow = $this->serialiser->getHeaderRow($this->data);

        if (!empty($headerRow)) {
            if($this->headerStyle != null)
                $writer->addRowWithStyle($headerRow, $this->headerStyle);
            else $writer->addRow($headerRow);
        }

        //data
        foreach ($this->data as $record) {
            if(is_array($record))
                $writer->addRow($record);
            elseif ($record instanceof Model)
                $writer->addRow($this->serialiser->getData($record));
            else
                $writer->addRow($this->serialiser->getDataFromStdClass($record));
        }
        return $writer;
    }
}
