<?php

namespace App\Services;

use App\Models\Invoice;
use App\Support\Pdf\BanglaLanguageToFont;
use Illuminate\Support\Facades\File;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

/**
 * Renders an invoice as a PDF with mPDF (pure PHP — works on shared hosting).
 *
 * Bangla text (and the ৳ sign) is handled by mPDF's script detection, which switches
 * Bengali runs to Kalpurush (resources/fonts) with conjunct shaping.
 */
class InvoicePdfService
{
    public function filename(Invoice $invoice): string
    {
        return preg_replace('/[^A-Za-z0-9\-_]/', '_', $invoice->invoice_number) . '.pdf';
    }

    /**
     * PDF file contents as a string.
     */
    public function render(Invoice $invoice): string
    {
        $invoice->loadMissing(['parent.user', 'child', 'items', 'payments.receivedBy']);

        $tempDir = storage_path('app/mpdf');
        File::ensureDirectoryExists($tempDir);

        $defaults = (new ConfigVariables())->getDefaults();
        $fontDefaults = (new FontVariables())->getDefaults();

        $mpdf = new Mpdf([
            'fontDir'           => array_merge($defaults['fontDir'], [resource_path('fonts')]),
            'fontdata'          => $fontDefaults['fontdata'] + [
                BanglaLanguageToFont::FONT => [
                    'R'      => 'Kalpurush.ttf', // single weight; mPDF synthesises bold
                    'useOTL' => 0xFF,            // OpenType shaping for Bangla conjuncts
                ],
            ],
            'languageToFont'    => new BanglaLanguageToFont(),
            'mode'              => 'utf-8',
            'format'            => 'A4',
            'margin_left'       => 14,
            'margin_right'      => 14,
            'margin_top'        => 14,
            'margin_bottom'     => 18,
            'margin_footer'     => 8,
            'default_font'      => 'dejavusans',
            'default_font_size' => 9.5,
            'tempDir'           => $tempDir,
            'autoScriptToLang'  => true, // detect Bangla runs…
            'autoLangToFont'    => true, // …and render them with a Bangla-capable font
        ]);

        $org = config('parentapp.organization');

        $mpdf->SetTitle('Invoice ' . $invoice->invoice_number);
        $mpdf->SetAuthor($org['name']);
        $mpdf->SetCreator($org['name']);

        if ($invoice->status === 'cancelled') {
            $mpdf->SetWatermarkText('VOID', 0.08);
            $mpdf->showWatermarkText = true;
        } elseif ($invoice->status === 'paid' || ((float) $invoice->total_amount > 0 && $invoice->balance_due <= 0)) {
            $mpdf->SetWatermarkText('PAID', 0.08);
            $mpdf->showWatermarkText = true;
        }

        $mpdf->SetHTMLFooter(
            '<table width="100%" style="font-size:7.5pt;color:#94a3b8;border-top:0.5px solid #e2e8f0;padding-top:4px"><tr>'
            . '<td>' . e($org['name']) . ' · ' . e($invoice->invoice_number) . '</td>'
            . '<td align="right">Page {PAGENO} of {nbpg}</td>'
            . '</tr></table>'
        );

        $mpdf->WriteHTML(view('admin.invoices.pdf', [
            'invoice' => $invoice,
            'org'     => $org,
            'logo'    => public_path('icons/icon-192.png'),
        ])->render());

        return $mpdf->Output('', Destination::STRING_RETURN);
    }
}
