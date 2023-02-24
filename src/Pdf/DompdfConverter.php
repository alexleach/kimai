<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Pdf;

use App\Constants;
use App\Utils\FileHelper;

use Dompdf\Dompdf;
use Dompdf\Options;


final class DompdfConverter implements HtmlToPdfConverter
{
    public function __construct(private FileHelper $fileHelper, private string $cacheDirectory)
    {
    }

    private function sanitizeOptions(array $options): array
    {
        if (\array_key_exists('tempDir', $options)) {
            unset($options['tempDir']);
        }

        return $options;
    }

    /**
     * @param string $html
     * @param array $options
     * @return string
     * @throws \Dompdf\DompdfException
     */
    public function convertToPdf(string $html, array $options = []): string
    {
	$options['tempDir'] = $this->cacheDirectory;

        if (\array_key_exists('fonts', $options)) {
            $options['fontDir'] = $this->fileHelper->getDataDirectory('fonts');
        }

        $dompdfOptions = new Options($options);
        $dompdf = new Dompdf($dompdfOptions);
        $dompdf->addInfo("Creator", Constants::SOFTWARE);

        // some OS'es do not follow the PHP default settings
        if ((int) \ini_get('pcre.backtrack_limit') < 1000000) {
            @ini_set('pcre.backtrack_limit', '1000000');
        }

        // large amount of data take time
        @ini_set('max_execution_time', '120');

        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render as PDF
        $dompdf->render();

        // Output to stream
        return $dompdf->output();
    }
}

