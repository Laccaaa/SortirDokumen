<?php

final class SimpleXlsxWriter
{
    private string $sheetName;
    /** @var array<int, array<int, array{v:string, s:int, t:string}>> */
    private array $rows = [];
    /** @var array<int, float> */
    private array $colWidths = [];
    /** @var array<int, array{ref:string}> */
    private array $merges = [];
    private int $freezeRow = 0;
    private string $autoFilterRef = '';

    public function __construct(string $sheetName = 'Sheet1')
    {
        $this->sheetName = $this->sanitizeSheetName($sheetName);
    }

    public function setColumnWidths(array $widths): self
    {
        $this->colWidths = [];
        foreach ($widths as $index => $width) {
            $i = (int)$index;
            if ($i <= 0) continue;
            $this->colWidths[$i] = (float)$width;
        }
        return $this;
    }

    public function addRow(array $values, int $styleIndex = 4, string $cellType = 'inlineStr'): self
    {
        $row = [];
        foreach (array_values($values) as $v) {
            $row[] = [
                'v' => (string)$v,
                's' => $styleIndex,
                't' => $cellType,
            ];
        }
        $this->rows[] = $row;
        return $this;
    }

    public function merge(string $ref): self
    {
        $this->merges[] = ['ref' => $ref];
        return $this;
    }

    public function freezePanesAtRow(int $rowNumber): self
    {
        $this->freezeRow = max(0, $rowNumber);
        return $this;
    }

    public function setAutoFilter(string $ref): self
    {
        $this->autoFilterRef = $ref;
        return $this;
    }

    public function send(string $downloadFilename): void
    {
        $downloadFilename = $this->sanitizeFilename($downloadFilename);
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
        if ($tmp === false) {
            http_response_code(500);
            echo 'Gagal membuat file export.';
            exit;
        }

        $zip = new ZipArchive();
        if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            @unlink($tmp);
            http_response_code(500);
            echo 'Gagal membuat file export.';
            exit;
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->relsXml());
        $zip->addFromString('docProps/core.xml', $this->coreXml());
        $zip->addFromString('docProps/app.xml', $this->appXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml());
        $zip->close();

        if (ob_get_length()) {
            @ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
        header('Content-Length: ' . filesize($tmp));
        header('Cache-Control: max-age=0');

        readfile($tmp);
        @unlink($tmp);
        exit;
    }

    private function sanitizeSheetName(string $name): string
    {
        $name = trim($name);
        $name = str_replace(['\\', '/', '?', '*', '[', ']', ':'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name) ?? 'Sheet1';
        $name = trim($name);
        if ($name === '') $name = 'Sheet1';
        return mb_substr($name, 0, 31);
    }

    private function sanitizeFilename(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/[^\w\-. ]+/u', '_', $name) ?? 'export.xlsx';
        $name = preg_replace('/\s+/', '_', $name) ?? 'export.xlsx';
        if (!str_ends_with(strtolower($name), '.xlsx')) {
            $name .= '.xlsx';
        }
        return $name;
    }

    private function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function colLetter(int $n): string
    {
        $n = max(1, $n);
        $s = '';
        while ($n > 0) {
            $n--;
            $s = chr(65 + ($n % 26)) . $s;
            $n = intdiv($n, 26);
        }
        return $s;
    }

    private function lastColLetter(): string
    {
        $max = 1;
        foreach ($this->rows as $row) {
            $max = max($max, count($row));
        }
        return $this->colLetter($max);
    }

    private function sheetXml(): string
    {
        $rowCount = count($this->rows);
        $lastCol = $this->lastColLetter();
        $dimension = 'A1:' . $lastCol . max(1, $rowCount);

        $colsXml = '';
        if ($this->colWidths) {
            ksort($this->colWidths);
            $colsXml .= "<cols>";
            foreach ($this->colWidths as $i => $w) {
                $colsXml .= '<col min="' . $i . '" max="' . $i . '" width="' . $w . '" customWidth="1"/>';
            }
            $colsXml .= "</cols>";
        }

        $sheetViews = '<sheetViews><sheetView workbookViewId="0">';
        if ($this->freezeRow > 0) {
            $topLeft = 'A' . ($this->freezeRow + 1);
            $sheetViews .= '<pane ySplit="' . $this->freezeRow . '" topLeftCell="' . $topLeft . '" activePane="bottomLeft" state="frozen"/>';
        }
        $sheetViews .= '</sheetView></sheetViews>';

        $sheetData = '<sheetData>';
        foreach ($this->rows as $ri => $row) {
            $r = $ri + 1;
            $sheetData .= '<row r="' . $r . '">';
            foreach ($row as $ci => $cell) {
                $c = $this->colLetter($ci + 1) . $r;
                $s = (int)$cell['s'];
                $t = $cell['t'] === 'n' ? 'n' : 'inlineStr';
                $v = $cell['v'];
                if ($t === 'n' && is_numeric($v)) {
                    $sheetData .= '<c r="' . $c . '" s="' . $s . '"><v>' . $this->esc((string)$v) . '</v></c>';
                } else {
                    $sheetData .= '<c r="' . $c . '" t="inlineStr" s="' . $s . '"><is><t xml:space="preserve">' . $this->esc($v) . '</t></is></c>';
                }
            }
            $sheetData .= '</row>';
        }
        $sheetData .= '</sheetData>';

        $autoFilterXml = '';
        if ($this->autoFilterRef !== '') {
            $autoFilterXml = '<autoFilter ref="' . $this->esc($this->autoFilterRef) . '"/>';
        }

        $mergeXml = '';
        if ($this->merges) {
            $mergeXml = '<mergeCells count="' . count($this->merges) . '">';
            foreach ($this->merges as $m) {
                $mergeXml .= '<mergeCell ref="' . $this->esc($m['ref']) . '"/>';
            }
            $mergeXml .= '</mergeCells>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<dimension ref="' . $this->esc($dimension) . '"/>'
            . $sheetViews
            . $colsXml
            . $sheetData
            . $autoFilterXml
            . $mergeXml
            . '</worksheet>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>'
            . '<sheet name="' . $this->esc($this->sheetName) . '" sheetId="1" r:id="rId1"/>'
            . '</sheets>'
            . '</workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="4">'
            . '<font><sz val="11"/><color theme="1"/><name val="Calibri"/><family val="2"/></font>'
            . '<font><b/><sz val="14"/><color theme="1"/><name val="Calibri"/><family val="2"/></font>'
            . '<font><b/><sz val="12"/><color theme="1"/><name val="Calibri"/><family val="2"/></font>'
            . '<font><b/><sz val="11"/><color theme="1"/><name val="Calibri"/><family val="2"/></font>'
            . '</fonts>'
            . '<fills count="3">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFE5E7EB"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/><diagonal/></border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="5">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="3" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="top" wrapText="1"/></xf>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '<dxfs count="0"/>'
            . '<tableStyles count="0" defaultTableStyle="TableStyleMedium9" defaultPivotStyle="PivotStyleLight16"/>'
            . '</styleSheet>';
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '</Types>';
    }

    private function relsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function coreXml(): string
    {
        $now = gmdate('Y-m-d\\TH:i:s\\Z');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" '
            . 'xmlns:dc="http://purl.org/dc/elements/1.1/" '
            . 'xmlns:dcterms="http://purl.org/dc/terms/" '
            . 'xmlns:dcmitype="http://purl.org/dc/dcmitype/" '
            . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:creator>SortirDokumen</dc:creator>'
            . '<cp:lastModifiedBy>SortirDokumen</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:modified>'
            . '</cp:coreProperties>';
    }

    private function appXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" '
            . 'xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>SortirDokumen</Application>'
            . '</Properties>';
    }
}
