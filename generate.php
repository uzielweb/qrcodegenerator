<?php
error_reporting(0);
ini_set('display_errors', 0);
setlocale(LC_NUMERIC, 'C'); // Critical for SVG floats
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/vendor/autoload.php';

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Common\EccLevel;

/**
 * Detect if uploaded file is an SVG.
 */
function isLogoSvg(array $fileInfo): bool {
    $mime = $fileInfo['type'] ?? '';
    if (str_contains($mime, 'svg')) return true;
    $ext = strtolower(pathinfo($fileInfo['name'] ?? '', PATHINFO_EXTENSION));
    return $ext === 'svg';
}

/**
 * Parse SVG content to extract aspect ratio (width / height).
 */
function getSvgAspectRatio(string $svgContent): float {
    if (preg_match('/<svg([^>]*)>/is', $svgContent, $matches)) {
        $attributes = $matches[1];
        if (preg_match('/\bviewBox\s*=\s*(["\'])(.*?)\1/is', $attributes, $m)) {
            $parts = preg_split('/[\s,]+/', trim($m[2]));
            if (count($parts) === 4 && (float)$parts[3] > 0) {
                return (float)$parts[2] / (float)$parts[3];
            }
        }
        if (preg_match('/\bwidth\s*=\s*(["\'])(.*?)\1/is', $attributes, $mw)
            && preg_match('/\bheight\s*=\s*(["\'])(.*?)\1/is', $attributes, $mh)) {
            $w = (float)$mw[2];
            $h = (float)$mh[2];
            if ($h > 0) return $w / $h;
        }
    }
    return 1.0;
}

/**
 * Prepares an SVG logo to be embedded directly into the QR SVG.
 * Modifies the root <svg> tag to position and scale it correctly.
 */
function prepareSvgLogoForEmbed(string $svgContent, float $x, float $y, float $w, float $h): string {
    $svg = preg_replace('/<\?xml[^?]*\?>/i', '', $svgContent);
    $svg = preg_replace('/<!DOCTYPE[^>]*>/i', '', $svg);
    $svg = trim($svg);

    if (!preg_match('/<svg([^>]*)>/is', $svg, $matches)) {
        return '';
    }
    
    $attributes = $matches[1];
    
    // Extract original dimensions if no viewBox is present
    $viewBox = null;
    if (preg_match('/\bviewBox\s*=\s*(["\'])(.*?)\1/is', $attributes, $m)) {
        $viewBox = $m[2];
    } else {
        $origW = $origH = null;
        if (preg_match('/\bwidth\s*=\s*(["\'])(.*?)\1/is', $attributes, $mw)) $origW = (float)$mw[2];
        if (preg_match('/\bheight\s*=\s*(["\'])(.*?)\1/is', $attributes, $mh)) $origH = (float)$mh[2];
        if ($origW > 0 && $origH > 0) {
            $viewBox = "0 0 {$origW} {$origH}";
        }
    }
    
    // Remove existing positioning/sizing attributes
    $attributes = preg_replace('/\b(?:x|y|width|height)\s*=\s*(["\']).*?\1/is', '', $attributes);
    
    // Ensure viewBox is set
    if ($viewBox && !preg_match('/\bviewBox\s*=/i', $attributes)) {
        $attributes .= sprintf(' viewBox="%s"', $viewBox);
    }
    
    $newRoot = sprintf('<svg x="%f" y="%f" width="%f" height="%f" %s>', $x, $y, $w, $h, $attributes);
    return preg_replace('/<svg[^>]*>/is', $newRoot, $svg, 1);
}

/**
 * Rasterize SVG to a GD resource for PNG overlay.
 */
function rasterizeSvg(string $svgContent, int $targetPxWidth, int $targetPxHeight) {
    if (extension_loaded('imagick')) {
        try {
            $im = new Imagick();
            $im->setBackgroundColor(new ImagickPixel('transparent'));
            $im->readImageBlob($svgContent);
            $im->setImageFormat('png');
            $im->scaleImage($targetPxWidth, $targetPxHeight, true);
            $pngBlob = $im->getImageBlob();
            $im->clear();
            $im->destroy();
            return imagecreatefromstring($pngBlob);
        } catch (\Exception $e) {
            // fallback if imagick fails
        }
    }

    // Fallback: return transparent empty image
    $gd = imagecreatetruecolor($targetPxWidth, $targetPxHeight);
    imagesavealpha($gd, true);
    $transparent = imagecolorallocatealpha($gd, 0, 0, 0, 127);
    imagefill($gd, 0, 0, $transparent);
    return $gd;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['data'] ?? '';
    $scale = isset($_POST['size']) ? (int)max(1, min(20, (int)$_POST['size'] / 33)) : 10; 
    $shape = $_POST['shape'] ?? 'square'; // square or circle
    $ecc = $_POST['ecc'] ?? 'H'; // L, M, Q, H
    $logoFile = $_FILES['logo'] ?? null;

    if (empty($data)) {
        echo json_encode(['error' => 'Por favor, insira algum texto ou URL.']);
        exit;
    }

    $hasLogo = $logoFile && $logoFile['error'] === UPLOAD_ERR_OK;
    $isSvgLogo = $hasLogo && isLogoSvg($logoFile);
    $logoRawData = $hasLogo ? file_get_contents($logoFile['tmp_name']) : null;

    try {
        $eccLevelMap = ['L' => EccLevel::L, 'M' => EccLevel::M, 'Q' => EccLevel::Q, 'H' => EccLevel::H];
        $targetEcc = $eccLevelMap[$ecc] ?? EccLevel::H;

        $baseOptions = [
            'version'             => QRCode::VERSION_AUTO, // Let library choose based on data length
            'eccLevel'            => $targetEcc,
            'addQuietzone'        => true,
            'imageTransparent'    => true,
            'scale'               => $scale,
            'drawCircularModules' => ($shape === 'circle'),
            'circleRadius'        => 0.45,
            'keepAsSquare'        => [
                QRMatrix::M_FINDER,
                QRMatrix::M_FINDER_DOT,
                QRMatrix::M_ALIGNMENT,
            ],
        ];

        // --- Determine logo aspect ratio (used by both PNG and SVG) ---
        $logoAspect = 1.0;
        if ($hasLogo) {
            if ($isSvgLogo) {
                $logoAspect = getSvgAspectRatio($logoRawData);
            } else {
                $tmpImg = imagecreatefromstring($logoRawData);
                if ($tmpImg) {
                    $w = imagesx($tmpImg);
                    $h = imagesy($tmpImg);
                    if ($h > 0) $logoAspect = $w / $h;
                    imagedestroy($tmpImg);
                }
            }
        }

        // =============================================================
        // Generate PNG
        // =============================================================
        $pngOptions = new QROptions(array_merge($baseOptions, ['outputType' => QRCode::OUTPUT_IMAGE_PNG]));
        $qrcodePng = new QRCode($pngOptions);
        $pngData = $qrcodePng->render($data);

        $pngMissingLogo = false;

        if ($hasLogo) {
            $qrGd = imagecreatefromstring(base64_decode(explode(',', $pngData)[1]));
            $qrWidth = imagesx($qrGd);
            $qrHeight = imagesy($qrGd);

            // Make logo 25% of QR width
            $targetLogoWidth = (int)($qrWidth * 0.25);
            $targetLogoHeight = (int)($targetLogoWidth / $logoAspect);

            $logoGd = null;
            if ($isSvgLogo) {
                if (extension_loaded('imagick')) {
                    $logoGd = rasterizeSvg($logoRawData, $targetLogoWidth, $targetLogoHeight);
                } else {
                    $pngMissingLogo = true;
                }
            } else {
                $logoGd = imagecreatefromstring($logoRawData);
            }

            if ($logoGd) {
                $origLogoW = imagesx($logoGd);
                $origLogoH = imagesy($logoGd);

                $logoX = (int)(($qrWidth - $targetLogoWidth) / 2);
                $logoY = (int)(($qrHeight - $targetLogoHeight) / 2);

                // Draw white background aligned perfectly to the module grid ($scale)
                $pad = $targetLogoWidth * 0.08;
                $bgX = floor(($logoX - $pad) / $scale) * $scale;
                $bgY = floor(($logoY - $pad) / $scale) * $scale;
                $bgRight = ceil(($logoX + $targetLogoWidth + $pad) / $scale) * $scale;
                $bgBottom = ceil(($logoY + $targetLogoHeight + $pad) / $scale) * $scale;

                $white = imagecolorallocate($qrGd, 255, 255, 255);
                // Subtract 1 from right/bottom because GD coordinates are inclusive
                imagefilledrectangle($qrGd, (int)$bgX, (int)$bgY, (int)($bgRight - 1), (int)($bgBottom - 1), $white);

                imagecopyresampled($qrGd, $logoGd, $logoX, $logoY, 0, 0, $targetLogoWidth, $targetLogoHeight, $origLogoW, $origLogoH);
                
                ob_start();
                imagepng($qrGd);
                $pngData = 'data:image/png;base64,' . base64_encode(ob_get_clean());
                imagedestroy($logoGd);
            }
            imagedestroy($qrGd);
        }

        // =============================================================
        // Generate SVG
        // =============================================================
        $svgOptions = new QROptions(array_merge($baseOptions, [
            'outputType'  => QRCode::OUTPUT_MARKUP_SVG,
            'imageBase64' => false,
        ]));
        $qrcodeSvg = new QRCode($svgOptions);
        $svgData = $qrcodeSvg->render($data);

        if ($hasLogo) {
            // Calculate size dynamically based on the matrix
            $totalModules = $qrcodeSvg->getQRMatrix()->getSize();

            // Make logo 25% of QR width
            $targetWidth = $totalModules * 0.25;
            $targetHeight = $targetWidth / $logoAspect;
            
            $logoX = ($totalModules - $targetWidth) / 2;
            $logoY = ($totalModules - $targetHeight) / 2;

            // Align white background perfectly to the integer module grid
            $pad = $targetWidth * 0.08;
            $bgX = floor($logoX - $pad);
            $bgY = floor($logoY - $pad);
            $bgW = ceil($logoX + $targetWidth + $pad) - $bgX;
            $bgH = ceil($logoY + $targetHeight + $pad) - $bgY;

            // Add a 0.1 module overlap to hide SVG anti-aliasing artifacts (fiapos) at exact boundaries
            $overlap = 0.1;
            $bgRect = sprintf('<rect x="%f" y="%f" width="%f" height="%f" fill="#ffffff" />', 
                $bgX - $overlap, $bgY - $overlap, $bgW + ($overlap * 2), $bgH + ($overlap * 2));

            if ($isSvgLogo) {
                $logoTag = prepareSvgLogoForEmbed($logoRawData, $logoX, $logoY, $targetWidth, $targetHeight);
            } else {
                $logoBase64 = base64_encode($logoRawData);
                $logoType = $logoFile['type'];
                $logoTag = sprintf(
                    '<image x="%f" y="%f" width="%f" height="%f" href="data:%s;base64,%s" />',
                    $logoX, $logoY, $targetWidth, $targetHeight, $logoType, $logoBase64
                );
            }

            $svgData = str_replace('</svg>', $bgRect . $logoTag . '</svg>', $svgData);
        }

        echo json_encode([
            'png' => $pngData,
            'svg' => $svgData,
            'png_missing_logo' => $pngMissingLogo
        ]);

    } catch (\Exception $e) {
        echo json_encode(['error' => 'Erro ao gerar QR Code: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método não permitido']);
}
