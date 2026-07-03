# QR Code Generator Pro

A premium QR Code generator built with PHP and JavaScript that offers advanced customization and native support for inserting central logos (including SVG vectors).

## 🚀 Features

- **Custom Content:** Supports long texts or URLs.
- **Module Types:** Choose between square (default) or circular modules.
- **Error Correction Level (ECC):** Select the complexity (Low, Medium, Quartile, High - recommended when using logos).
- **Density/Scale:** Adjust the generated QR Code size from 100 to 2000.
- **Central Logo:** Upload your logo in `PNG`, `JPEG`, or even `SVG` vector format to natively embed and perfectly align it to the QR grid.
- **Multiple Formats:** Generates `SVG` (high-quality vector) and `PNG` simultaneously.
- **Quick Downloads:** Buttons to easily download your desired format right after generation.

## 🛠️ How to Use

1. **Access the application:** Open the `index.php` file on a local server (e.g., Laragon, XAMPP) or on your web host that supports PHP.
2. **Content:** In the "Conteúdo (Texto ou URL)" field, enter the link or text you want to encode.
3. **Visual Customization:**
   - **Type of Module:** Select between Square or Circular.
   - **Complexity (ECC):** Set to High by default. This ensures the QR Code remains readable even with a central logo covering part of it.
   - **Density (Scale):** Control the base dimensions of the QR Code by tweaking this value.
4. **Logo (Optional):** Click "Escolher arquivo" (Choose file) in the Central Logo section to add your brand. The system supports traditional image formats and `SVG` while preserving transparency and quality.
5. **Generate:** Click the **GERAR QR CODES** button.
6. **Preview and Download:** The system will display previews in Vector (SVG) and Image (PNG). Just click the download buttons right below to save the generated files.

## ⚙️ Technologies Used

- **Frontend:** HTML, CSS (Glassmorphism Design and Outfit typography), JavaScript.
- **Backend:** PHP 8.x.
- **Core Library:** [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) for the matrix generation engine and native rendering.

## 📋 Requirements

- Web server with PHP 7.4 or higher (PHP 8+ recommended).
- `imagick` or `gd` extension (used as a fallback for matrix rasterization into PNG).
- Composer (dependencies should already be installed in the `vendor/` folder).
