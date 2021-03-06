<?php

namespace spec\Pim\Bundle\ExcelConnectorBundle\Excel\Builder;

use PhpSpec\ObjectBehavior;

class ProductFamilyExcelBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\ExcelConnectorBundle\Excel\Builder\ProductFamilyExcelBuilder');
    }

    function it_creates_one_tab_per_family()
    {
        $this->add(['family' => 'FAMILY1', 'col1' => 'value1', 'col2' => 'value2']);
        $this->add(['family' => 'FAMILY1', 'col1' => 'value3', 'col2' => 'value4']);
        $this->add(['family' => 'FAMILY2', 'col1' => 'value5', 'col3' => 'value6']);
        $this->add(['family' => 'FAMILY2', 'col1' => 'value7', 'col3' => 'value8']);
        $excelObject = $this->getExcelObject();
        $excelObject->shouldHaveCellRow('FAMILY1', 1, ['family', 'col1', 'col2']);
        $excelObject->shouldHaveCellRow('FAMILY1', 2, ['FAMILY1', 'value1', 'value2']);
        $excelObject->shouldHaveCellRow('FAMILY1', 3, ['FAMILY1', 'value3', 'value4']);
        $excelObject->shouldHaveCellRow('FAMILY2', 1, ['family', 'col1', 'col3']);
        $excelObject->shouldHaveCellRow('FAMILY2', 2, ['FAMILY2', 'value5', 'value6']);
        $excelObject->shouldHaveCellRow('FAMILY2', 3, ['FAMILY2', 'value7', 'value8']);
    }

    public function getMatchers()
    {
        return [
            'haveCellValue' => [$this, 'hasCellValue'],
            'haveCellRow' => [$this, 'hasCellRow']
        ];
    }

    public function hasCellValue(\PHPExcel $excelObject, $worksheetName, $coordinate, $value)
    {
        $worksheet = $excelObject->getSheetByName($worksheetName);
        if (!$worksheet) {
            return false;
        }

        return $worksheet->getCell($coordinate)->getValue() === $value;
    }

    public function hasCellRow(\PHPExcel $excelObject, $worksheetName, $row, array $values)
    {
        $worksheet = $excelObject->getSheetByName($worksheetName);
        if (!$worksheet) {
            return false;
        }

        $rowIterator = $worksheet->getRowIterator($row);
        if (!$rowIterator->valid()) {
            return false;
        }

        $cellIterator = $rowIterator->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        foreach ($cellIterator as $cell) {
            $value = array_shift($values);
            if ($value !== $cell->getValue()) {
                return false;
            }
        }

        return 0 === count($values);
    }
}
