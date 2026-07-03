<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.1);
            --input-bg: rgba(15, 23, 42, 0.6);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(139, 92, 246, 0.15) 0%, transparent 40%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 700px;
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }

        header {
            text-align: center;
            margin-bottom: 32px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 600px) {
            .main-grid { grid-template-columns: 1fr; }
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        textarea, select, input[type="file"], input[type="number"] {
            width: 100%;
            padding: 14px;
            background: var(--input-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text);
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        textarea:focus, select:focus, input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
            background: rgba(15, 23, 42, 0.8);
        }

        input[type="file"]::file-selector-button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            margin-right: 12px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        #generate-btn {
            width: 100%;
            padding: 18px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 10px;
        }

        #generate-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.5);
        }

        .result-section {
            margin-top: 48px;
            display: none;
            flex-direction: column;
            align-items: center;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .previews-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
            width: 100%;
        }

        @media (max-width: 500px) {
            .previews-grid { grid-template-columns: 1fr; }
        }

        .preview-box {
            background: white;
            padding: 16px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        }

        .preview-label {
            color: var(--bg);
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .preview-img {
            width: 100%;
            height: auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .preview-img svg, .preview-img img {
            width: 100%;
            max-width: 250px;
            height: auto;
        }

        .download-actions {
            display: flex;
            gap: 16px;
            width: 100%;
            max-width: 500px;
        }

        .btn-download {
            flex: 1;
            padding: 14px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: var(--text);
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-download:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--text-muted);
        }

        .loader {
            width: 22px;
            height: 22px;
            border: 3px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>QR Generator Pro</h1>
            <p class="subtitle">Personalização avançada para sua marca.</p>
        </header>

        <div class="main-grid">
            <div class="form-group full-width">
                <label for="data">Conteúdo (Texto ou URL)</label>
                <textarea id="data" rows="3" placeholder="Insira o link ou texto..."></textarea>
            </div>

            <div class="form-group">
                <label for="shape">Tipo de Módulo</label>
                <select id="shape">
                    <option value="square">Quadrado (Padrão)</option>
                    <option value="circle">Circular</option>
                </select>
            </div>

            <div class="form-group">
                <label for="ecc">Complexidade (ECC)</label>
                <select id="ecc">
                    <option value="L">Baixa (Low)</option>
                    <option value="M">Média (Medium)</option>
                    <option value="Q">Quartil (Quartile)</option>
                    <option value="H" selected>Alta (High - Recomendado)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="size">Densidade (Escala)</label>
                <input type="number" id="size" value="500" min="100" max="2000" step="50">
            </div>

            <div class="form-group">
                <label>Formatos Disponíveis</label>
                <div style="font-size: 0.85rem; color: var(--text-muted); padding-top: 10px;">
                    SVG & PNG (Gerados simultaneamente)
                </div>
            </div>

            <div class="form-group full-width">
                <label for="logo">Logotipo Central (Opcional)</label>
                <input type="file" id="logo" accept="image/png, image/jpeg, image/svg+xml">
            </div>

            <button id="generate-btn" class="full-width">
                <span id="btn-text">GERAR QR CODES</span>
                <div class="loader" id="loader"></div>
            </button>
        </div>

        <div class="result-section" id="result-section">
            <div class="previews-grid">
                <div class="preview-box">
                    <span class="preview-label">Vetor (SVG)</span>
                    <div id="svg-preview" class="preview-img"></div>
                </div>
                <div class="preview-box">
                    <span class="preview-label">Imagem (PNG)</span>
                    <div id="png-preview" class="preview-img"></div>
                </div>
            </div>
            
            <div class="download-actions">
                <button class="btn-download" id="dl-svg">Download SVG</button>
                <button class="btn-download" id="dl-png">Download PNG</button>
            </div>
        </div>
    </div>

    <script>
        const generateBtn = document.getElementById('generate-btn');
        const dataIn = document.getElementById('data');
        const shapeSel = document.getElementById('shape');
        const eccSel = document.getElementById('ecc');
        const sizeIn = document.getElementById('size');
        const logoIn = document.getElementById('logo');

        const resultSec = document.getElementById('result-section');
        const svgPreview = document.getElementById('svg-preview');
        const pngPreview = document.getElementById('png-preview');
        const loader = document.getElementById('loader');
        const btnTxt = document.getElementById('btn-text');

        let lastResult = null;

        generateBtn.addEventListener('click', async () => {
            const data = dataIn.value.trim();
            if (!data) return alert('Por favor, informe o conteúdo.');

            generateBtn.disabled = true;
            loader.style.display = 'block';
            btnTxt.style.opacity = '0.4';

            const formData = new FormData();
            formData.append('data', data);
            formData.append('shape', shapeSel.value);
            formData.append('ecc', eccSel.value);
            formData.append('size', sizeIn.value);
            if (logoIn.files[0]) formData.append('logo', logoIn.files[0]);

            try {
                const response = await fetch('generate.php', { method: 'POST', body: formData });
                const text = await response.text();
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    console.error('Raw response:', text);
                    throw new Error('Erro na resposta do servidor (JSON inválido). Verifique o console.');
                }

                lastResult = result;
                if (result.error) throw new Error(result.error);

                if (result.png_missing_logo && result.svg) {
                    try {
                        const generatedPng = await svgToPngBase64(result.svg, 4);
                        result.png = generatedPng;
                        lastResult.png = generatedPng;
                    } catch (e) {
                        console.error('Failed to rasterize SVG in browser', e);
                    }
                }

                svgPreview.innerHTML = result.svg;
                pngPreview.innerHTML = `<img src="${result.png}" alt="QR PNG">`;
                
                resultSec.style.display = 'flex';
                resultSec.scrollIntoView({ behavior: 'smooth' });

            } catch (err) {
                alert(err.message || 'Erro ao gerar QR Code.');
            } finally {
                generateBtn.disabled = false;
                loader.style.display = 'none';
                btnTxt.style.opacity = '1';
            }
        });

        document.getElementById('dl-svg').addEventListener('click', () => handleDownload('svg'));
        document.getElementById('dl-png').addEventListener('click', () => handleDownload('png'));

        function svgToPngBase64(svgString, scale = 4) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                const svgBlob = new Blob([svgString], {type: 'image/svg+xml;charset=utf-8'});
                const url = URL.createObjectURL(svgBlob);
                
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width * scale;
                    canvas.height = img.height * scale;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    URL.revokeObjectURL(url);
                    resolve(canvas.toDataURL('image/png'));
                };
                img.onerror = reject;
                img.src = url;
            });
        }

        async function handleDownload(target) {
            if ((target === 'png' && lastResult?.png) || (target === 'svg' && lastResult?.svg)) {
                const blobContent = (target === 'svg') ? lastResult.svg : null;
                const uri = (target === 'png') ? lastResult.png : null;
                
                const filename = getSlug(dataIn.value) + '.' + target;

                if (blobContent) {
                    const blob = new Blob([blobContent], { type: 'image/svg+xml' });
                    const url = URL.createObjectURL(blob);
                    triggerDl(url, filename);
                    URL.revokeObjectURL(url);
                } else {
                    triggerDl(uri, filename);
                }
            }
        }

        function getSlug(text) {
            if (!text) return 'qrcode';
            
            let result = text.trim();
            try {
                if (result.startsWith('http')) {
                    const urlObj = new URL(result);
                    let path = urlObj.pathname.split('/').filter(p => p !== '').pop();
                    if (path) {
                        result = path;
                    } else {
                        result = urlObj.hostname;
                    }
                }
            } catch (e) {
                // Not a valid URL, use original text
            }

            return result
                .toString()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '') || 'qrcode';
        }

        function triggerDl(url, name) {
            const a = document.createElement('a');
            a.href = url;
            a.download = name;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>
</body>
</html>
