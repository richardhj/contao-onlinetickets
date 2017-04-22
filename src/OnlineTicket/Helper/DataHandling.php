<?php

namespace OnlineTicket\Helper;

use Contao\DataContainer;
use Contao\File;
use Haste\Haste;
use Haste\IO\Reader\ModelCollectionReader;
use Haste\IO\Writer\CsvFileWriter;
use Haste\IO\Writer\ExcelFileWriter;
use Haste\Util\Format;
use OnlineTicket\Model\Agency;
use OnlineTicket\Model\Event;
use OnlineTicket\Model\Ticket;


class DataHandling
{

    /**
     * @param DataContainer $dc
     */
    public function exportAgencyBarcodes($dc)
    {
        /** @type \Model\Collection $tickets */
        $tickets = Ticket::findByAgency($dc->id);

        $reader = new ModelCollectionReader($tickets);
        $writer = new CsvFileWriter();
        $writer->enableHeaderFields();

        $reader->setHeaderFields(
            [
                'Event',
                'TicketAgency',
                'Ticket',
                'Barcode'
            ]
        );

        $writer->setRowCallback(
            function ($row) {
                return [
                    $row['event_id'],
                    $row['agency_id'],
                    $row['id'],
                    sprintf('%s.%s', $row['event_id'], $row['id'])
                ];
            }
        );

        $writer->writeFrom($reader);

        $file = new File($writer->getFilename());
        $file->sendToBrowser();
    }


    /**
     * @param DataContainer $dc
     */
    public function exportPreprintedTicketsPdf($dc)
    {
        $tickets = Ticket::findByAgency($dc->id);
        /** @var Event $event */
        $event = $tickets->getRelated('event_id');
        $tickets->reset(); # getRelated() increases index

        // TCPDF configuration
        $l                    = [];
        $l['a_meta_dir']      = 'ltr';
        $l['a_meta_charset']  = $GLOBALS['TL_CONFIG']['characterSet'];
        $l['a_meta_language'] = substr($GLOBALS['TL_LANGUAGE'], 0, 2);
        $l['w_page']          = 'page';

        // Include TCPDF config
        require_once TL_ROOT . '/system/config/tcpdf.php';

        $pageFormat    = array_map(
            'static::getInputUnitValue',
            [$event->ticket_width, $event->ticket_height]
        );
        $varPageFormat = (2 === count(array_filter($pageFormat))) ? $pageFormat : PDF_PAGE_FORMAT;

        // Create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $varPageFormat, true);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle(sprintf($GLOBALS['TL_LANG']['MSC']['ticketExportPdfTitle'], $dc->id, $event->name));

        // Prevent font subsetting (huge speed improvement)
        $pdf->setFontSubsetting(false);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set some language-dependent strings
        $pdf->setLanguageArray($l);

        // Set font
        $pdf->SetFont(
            $event->ticket_font_family ?: PDF_FONT_NAME_MAIN,
            ('' === $event->ticket_font_style) ?: (implode('', deserialize($event->ticket_font_style)) ?: ''),
            static::getInputUnitValue($event->ticket_font_size) ?: PDF_FONT_SIZE_MAIN
        );

        /*
         * Write every ticket to pdf
         */
        $elements = deserialize($event->ticket_elements);

        while ($tickets->next()) {
            $pdf->AddPage();

            foreach ($elements as $element) {
                switch ($element['te_element']) {
                    // Collection: Ticket
                    case 'id':
                        $pdf->MultiCell(
                            0,
                            0,
                            str_pad(
                                $tickets->{$element['te_element']},
                                (int)$event->ticket_fill_number,
                                '0',
                                STR_PAD_LEFT
                            ),
                            0,
                            'J',
                            false,
                            1,
                            $element['te_position_x'],
                            $element['te_position_y']
                        );
                        break;

                    // Collection: Event
                    case 'name':
                    case 'date':
                        $pdf->MultiCell(
                            0,
                            0,
                            $event->{$element['te_element']},
                            0,
                            'J',
                            false,
                            1,
                            $element['te_position_x'],
                            $element['te_position_y']
                        );
                        break;

                    // Barcodes
                    case 'C128':
                    case 'C39':
                        $pdf->write1DBarcode(
                            sprintf
                            (
                                '%u.%u',
                                $tickets->event_id,
                                $tickets->id
                            ),
                            $element['te_element'],
                            $element['te_position_x'],
                            $element['te_position_y'],
                            '',
                            static::getInputUnitValue($event->ticket_barcode_height)
                        );
                        break;

                    // QR code
                    case 'QRCODE,M':
                        $pdf->write2DBarcode(
                            $tickets->hash,
                            $element['te_element'],
                            $element['te_position_x'],
                            $element['te_position_y'],
                            static::getInputUnitValue($event->ticket_qrcode_width),
                            static::getInputUnitValue($event->ticket_qrcode_width)
                        );
                        break;
                }
            }
        }

        $pdf->lastPage();

        $pdf->Output(
            sprintf('ticket-agency_%u.pdf', $dc->id),
            'D'
        );
    }


    /**
     * @param DataContainer $dc
     */
    public function exportEventReport($dc)
    {
        $writer = new ExcelReport();

        /** @var Event $event */
        /** @noinspection PhpUndefinedMethodInspection */
        $event = Event::findByPk($dc->id);

        /** @var Agency|\Model\Collection $agencies */
        $agencies = Agency::findBy('pid', $event->id);

        /*
         * Write head
         */
        $writer->writeRow(
            [
                static::getLabelFromLanguageFile('ticket_report')
            ]
        );

        $writer->setH1Style();

        // Write event data
        foreach (['name', 'date', 'id'] as $field) {
            /** @noinspection PhpUndefinedMethodInspection */
            $writer->writeRow(
                [
                    static::getLabelFromLanguageFile($field, Event::getTable()),
                    ('date' === $field) ? Format::date($event->$field) : $event->$field
                ]
            );

            $writer->setStyle()->getFont()->setBold(true);
            $writer->setStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        }

        // Set event id aligned left
        $writer->setStyle(1, 1, 0, 1)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $writer->writeRow([]);

        /*
         * Total sales listing
         */
        $salesColumns = [
            'name',
            'ticket_price',
            'tickets_generated',
            'tickets_sold',
            'tickets_checkedin',
            'tickets_sales'
        ];

        // Write table head
        $writer->writeRow(
            array_map(
                'static::getLabelFromLanguageFile',
                $salesColumns
            )
        );

        // Set h3 style for table head
        $writer->setH3Style(count($salesColumns));

        $rows = 0;

        // Write row for online sales and each agency sales
        do {
            $row = [];

            foreach ($salesColumns as $field) {
                if ('tickets_sales' === $field) {
                    // Formula for multiplying ticket_price by tickets_sold
                    $row[] = sprintf(
                        '=%2$s%1$u*%3$s%1$u',
                        $writer->getCurrentRow() + 1,
                        \PHPExcel_Cell::stringFromColumnIndex(array_search('ticket_price', $salesColumns)),
                        \PHPExcel_Cell::stringFromColumnIndex(array_search('tickets_sold', $salesColumns))
                    );

                    continue;
                }

                // Write online sales row initially
                if (!$rows) {
                    // Write online sales column
                    $row[] = $this->getPropertyForOnlineSales($event->id, $field);

                    continue;
                }

                // Write agency sales column
                $row[] = $agencies->$field;
            }

            $writer->writeRow($row);
            $rows++;

        } while ($agencies !== null && $agencies->next());

        $row = [];

        // Sum line
        foreach ($salesColumns as $i => $field) {
            switch ($field) {
                case 'name':
                    $row[] = static::getLabelFromLanguageFile('total');
                    break;

                default:
                    $row[] = sprintf(
                        '=SUM(%1$s%2$u:%1$s%3$u)',
                        \PHPExcel_Cell::stringFromColumnIndex($i),
                        $writer->getCurrentRow() + 1 - $rows,
                        $writer->getCurrentRow()
                    );
            }
        }

        $writer->writeRow($row);
        $writer->setStyle(1, count($salesColumns))->getFont()->setBold(true);

        // Merge cell with row title and align right
        $writer->getPHPExcel()->getActiveSheet()->mergeCellsByColumnAndRow(
            0,
            $writer->getCurrentRow(),
            1,
            $writer->getCurrentRow()
        );
        $writer->setStyle(1, 1)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        // Set the currency format for corresponding columns
        foreach (['ticket_price', 'tickets_sales'] as $field) {
            $writer->setStyle(
                $rows + 1,
                1,
                $writer->getCurrentRow() - $rows,
                array_search($field, $salesColumns)
            )->getNumberFormat()->setFormatCode(
                str_replace('EUR', '€', \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE)
            );
        }

        $writer->writeRow([]);

        /*
         * Tickets single listing
         */
        $tickets = Ticket::findByEvent($dc->id);

        $ticketsColumns = [
            'id',
            'tstamp',
            'agency_id',
            'checkin',
            'checkin_user'
        ];

        // Write table head
        $writer->writeRow(
            array_map(
                'static::getLabelFromLanguageFile',
                $ticketsColumns,
                array_fill(0, count($ticketsColumns), Ticket::getTable())
            )
        );

        // Set id head right aligned
        $writer->setStyle(1, 1, 0, array_search('id', $ticketsColumns))->getAlignment()->setHorizontal(
            \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        );

        // Set h3 style for table head
        $writer->setH3Style(count($ticketsColumns));

        while ($tickets->next()) {
            $row = [];

            foreach ($ticketsColumns as $field) {
                $value = $tickets->$field;

                /** @noinspection PhpUndefinedMethodInspection */
                if ($tickets->current()->isOnline() && 'agency_id' === $field) {
                    $value = 'Online'; //@todo lang
                } // Might be timestamp
                elseif (preg_match('/\d{10}/', $value)) {
                    $value = Format::datim($value);
                } elseif (!$value) {
                    $value = '';
                } else {
                    // Do we have a related data set?
                    try {
                        /** @type \Model $objRelated */
                        $objRelated = $tickets->getRelated($field);
                        $value      = $objRelated->name;

                    } catch (\Exception $e) {
                    }
                }

                $row[] = $value;
            }

            $writer->writeRow($row);
        }

        /*
         * Entrances graph
         */
        // Create new sheet for auxiliary calculations
        $auxiliaryLine = $writer->getCurrentRow() - $tickets->count();
        $writer->switchSheet(1);

        for ($i = 0; $i < $tickets->count(); $i++) {
            $startRow     = $writer->getCurrentRow() + 1 - $i;
            $currentRow   = $writer->getCurrentRow() + 1;
            $endRow       = $writer->getCurrentRow() - $i + $tickets->count();
            $firstColumn  = \PHPExcel_Cell::stringFromColumnIndex(0);
            $secondColumn = \PHPExcel_Cell::stringFromColumnIndex(1);
            $thirdColumn  = \PHPExcel_Cell::stringFromColumnIndex(2);
            $fourthColumn = \PHPExcel_Cell::stringFromColumnIndex(3);
            $fifthColumn  = \PHPExcel_Cell::stringFromColumnIndex(4);

            $writer->writeRow(
                [
                    sprintf(
                        '=HOUR(%s!%s%u)/24',
                        'Worksheet',
                        \PHPExcel_Cell::stringFromColumnIndex(array_search('checkin', $ticketsColumns)),
                        ++$auxiliaryLine
                    ),
                    sprintf(
                        '=COUNTIF($%1$s$%3$u:%1$s%2$u,%1$s%2$u)', // Returns 1 if checkin time occurs initially
                        $firstColumn,
                        $currentRow,
                        $startRow
                    ),
                    sprintf(
                        '=IFERROR('
                        . 'INDEX(' // Return cell value by its index
                        . '%1$s:%1$s,AGGREGAT(15,6,ROW($%1$s$%3$u:$%1$s$%4$u)/($%2$s$%3$u:$%2$s$%4$u=1),ROW())'
                        // Returns row index where B column value is 1
                        . ')'
                        . ',"")',
                        $firstColumn,
                        $secondColumn,
                        $startRow,
                        $endRow
                    ),
                    sprintf(
                        '=IFERROR('
                        . 'AGGREGAT(15,6,$%1$s$%2$u:$%1$s$%3$u,ROW())' // Sort values ascending
                        . ',"")',
                        $thirdColumn,
                        $startRow,
                        $endRow
                    ),
                    sprintf(
                        '=COUNTIF($%1$s$%2$u:$%1$s$%5$u,%4$s%3$s)',
                        $firstColumn,
                        $startRow,
                        $currentRow,
                        $fourthColumn,
                        $endRow
                    ),
                    sprintf(
                        '=SUM($%1$s$%2$u:%1$s%3$u)',
                        $fifthColumn,
                        $startRow,
                        $currentRow
                    )
                ]
            );
        }

        // Set time format for specific columns
        $writer
            ->setStyle(
                $tickets->count(),
                1,
                $writer->getCurrentRow() + 1 - $tickets->count() /* Start row */
            )
            ->getNumberFormat()
            ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3);
        $writer
            ->setStyle(
                $tickets->count(),
                1,
                $writer->getCurrentRow() + 1 - $tickets->count()
                /* Start row */,
                2 /* Third column */
            )
            ->getNumberFormat()
            ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3);
        $writer
            ->setStyle(
                $tickets->count(),
                1,
                $writer->getCurrentRow() + 1 - $tickets->count()
                /* Start row */,
                3 /* Fourth column */
            )
            ->getNumberFormat()
            ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3);

        // Jump back to main sheet
        $writer->switchSheet(0);

        /*
         * Finish and output file
         */
        $writer->finish();

        $file = new File($writer->getFilename());
        $file->sendToBrowser();
    }


    /**
     * Return a sales property vor online sales
     *
     * @param integer $eventId
     * @param string  $field
     *
     * @return mixed
     */
    public static function getPropertyForOnlineSales($eventId, $field)
    {
        switch ($field) {
            case 'name':
                return 'Online';
                break;

            case 'ticket_price':
                # Different ticket prices are possible so we calculate the average of tickets sold
                $tickets = Ticket::findOnlineByEvent($eventId); // @todo returns wrong collection (all tickets)

                if (null === $tickets) {
                    return '';
                }

                // Sum all online tickets prices
                $total = array_sum(
                    array_map(
                        function ($id, $key) use ($tickets) {
                            // Filter disabled tickets
                            if (!$tickets->offsetGet($key)->tstamp) {
                                return 0;
                            }

                            return $tickets->offsetGet($key)->getRelated('item_id')->price;
                        },
                        $tickets->fetchEach('id'),
                        array_keys($tickets->fetchEach('id'))
                    )
                );

                // Divide online tickets total by count of sold tickets
                return $total / static::getPropertyForOnlineSales(
                        $eventId,
                        'tickets_sold'
                    ); # can not use ->count() because of disabled tickets
                break;

            case 'tickets_generated':
                return Ticket::countBy(
                    ['order_id<>0', 'event_id=?'],
                    [$eventId]
                );
                break;

            case 'tickets_sold':
                return Ticket::countBy(
                    ['order_id<>0', 'event_id=?', 'tstamp<>0'],
                    [$eventId]
                );
                break;

            case 'tickets_checkedin':
                return Ticket::countBy(
                    ['order_id<>0', 'event_id=?', 'checkin<>0'],
                    [$eventId]
                );
                break;

            default:
                return '';
        }
    }


    /**
     * Extract the value of a widget with type "inputUnit"
     *
     * @param mixed $input The serialized string or already unserialized array
     *
     * @return string
     */
    protected static function getInputUnitValue($input)
    {
        $input = deserialize($input);

        if (false === is_array($input)) {
            return '';
        }

        return $input['value'];
    }


    /**
     * Return the language label
     *
     * @param string $name  The field's name or language key
     * @param string $table The table name the field belongs to or empty string to use the MSC language array
     *
     * @return string
     */
    protected static function getLabelFromLanguageFile($name, $table = '')
    {
        if ('' !== $table) {
            return Format::dcaLabel($table, $name);
        }

        return Format::dcaLabelFromArray(['name' => $name]);
    }
}
