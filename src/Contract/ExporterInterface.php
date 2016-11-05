<?php
namespace Cyberduck\LaravelExcel\Contract;

use Box\Spout\Writer\Style\Color;
use Illuminate\Database\Eloquent\Collection;

interface ExporterInterface
{
    public function load(Collection $data);
    public function loadArray(array $data);
    public function setSerialiser(SerialiserInterface $serialiser);
    public function save($filename);
    public function stream($filename);
    public function createHeaderStyle($isBold, $fontSize, $color, $wrapText, $backgroundColor);
}
