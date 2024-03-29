<?php

namespace Guave\FormSaveBundle\Controller;

use Contao\DC_Table;
use Contao\FormFieldModel;
use Contao\Image;
use Contao\Model\Collection;
use Contao\StringUtil;
use Guave\FormSaveBundle\Config\Config;
use Guave\FormSaveBundle\Model\FormSaveModel;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use SplTempFileObject;

class CsvExportController
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function checkVisibility(
        $row,
        $href,
        $label,
        $title,
        $icon,
        $attributes
    ): string
    {
        if ($row['storeValues'] !== '1' || $row['targetTable'] !== FormSaveModel::getTable()) {
            return '';
        }

        if (FormSaveModel::findByPid($row['id'])) {
            return '<a href="contao?do=form&' . $href . '&amp;id=' . $row['id'] . '" title="'
                . StringUtil::specialchars($title) . '"' . $attributes . '>'
                . Image::getHtml($icon, $label)
                . '</a>';
        }

        $icon = str_replace('.svg', '_.gif', $icon);
        return Image::getHtml($icon, $label);
    }

    public function exportCsv(DC_Table $dc): void
    {
        $formFields = FormFieldModel::findByPid($dc->id);
        $records = FormSaveModel::findByPid($dc->id, ['order' => 'tstamp DESC']);

        try {
            $writer = Writer::createFromFileObject(new SplTempFileObject())
                ->setDelimiter($this->config->getSeparator())
                ->setEnclosure($this->config->getQuote())
                ->setEndOfLine("\r\n");

            $writer->insertOne($this->getHeader($formFields));

            foreach ($records as $record) {
                $writer->insertOne($this->prepareRow($record->row()));
            }

            $writer->output('file.csv');
            die();
        } catch (CannotInsertRecord $e) {
            echo $e->getRecord();
        }
    }

    private function getHeader(Collection $formFields): array
    {
        $header = ['submitted'];
        /** @var FormFieldModel $formField */
        foreach ($formFields as $formField) {
            if ($formField->name !== '') {
                $header[] = $formField->name;
            }
        }
        sort($header);
        return $header;
    }

    private function prepareRow(array $row): array
    {
        $event = [
            'submitted' => date('d.m.y H:i', $row['tstamp']),
            'alias' => $row['alias'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
        ];

        $fields = unserialize($row['form_data']);
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                $field = implode(', ', $field);
            }
            $event[$key] = $field;
        }

        ksort($event);
        return $event;
    }
}
