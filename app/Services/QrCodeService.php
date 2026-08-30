<?php

namespace App\Services;

use BaconQrCode\Writer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Common\ErrorCorrectionLevel;

class QrCodeService
{
    /**
     * Generate a QR code as SVG string.
     */
    public static function svg(string $data, int $size = 80): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle($size),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            return $writer->writeString($data, 'UTF-8', ErrorCorrectionLevel::H());
        } catch (\Exception $e) {
            return '<svg width="' . $size . '" height="' . $size . '" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#f5f5f5"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" font-size="10" fill="#999">QR Error</text></svg>';
        }
    }
}
