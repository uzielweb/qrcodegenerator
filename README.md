# QR Code Generator Pro

Um gerador de QR Code premium, construído com PHP e JavaScript, que oferece personalização avançada e suporte à inserção de logotipos centrais (incluindo vetores SVG).

## 🚀 Funcionalidades

- **Conteúdo Personalizado:** Suporta textos longos ou URLs.
- **Tipos de Módulos:** Escolha entre módulos quadrados (padrão) ou circulares.
- **Nível de Correção de Erros (ECC):** Permite selecionar a complexidade (Low, Medium, Quartile, High - recomendado para uso com logotipos).
- **Densidade/Escala:** Ajuste o tamanho do QR Code gerado de 100 a 2000.
- **Logotipo Central:** Faça upload do seu logotipo em formato `PNG`, `JPEG` ou até mesmo em vetor `SVG` para deixá-lo centralizado no QR Code de forma nativa e alinhada à grade.
- **Geração Múltipla:** Gera os formatos `SVG` (vetor de alta qualidade) e `PNG` simultaneamente.
- **Downloads Rápidos:** Botões para baixar facilmente o formato desejado após a geração.

## 🛠️ Como Usar

1. **Acesse a aplicação:** Abra o arquivo `index.php` em um servidor local (ex: Laragon, XAMPP) ou no seu servidor de hospedagem que suporte PHP.
2. **Conteúdo:** No campo "Conteúdo (Texto ou URL)", insira o link ou o texto que deseja codificar.
3. **Personalização visual:**
   - **Tipo de Módulo:** Selecione entre Quadrado ou Circular.
   - **Complexidade (ECC):** Por padrão, fica em Alta (High). Isso garante que o QR Code será lido mesmo com um logotipo central cobrindo uma parte dele.
   - **Densidade (Escala):** Controle as dimensões base do QR Code ajustando este valor.
4. **Logotipo (Opcional):** Clique em "Escolher arquivo" na seção "Logotipo Central" para adicionar sua marca. O sistema suporta formatos tradicionais e `SVG` mantendo a transparência e qualidade.
5. **Gerar:** Clique no botão **GERAR QR CODES**.
6. **Visualização e Download:** O sistema vai exibir as pré-visualizações em Vetor (SVG) e Imagem (PNG). Basta clicar nos botões de download correspondentes logo abaixo para salvar os arquivos gerados.

## ⚙️ Tecnologias Utilizadas

- **Frontend:** HTML, CSS (com Design Glassmorphism e tipografia Outfit), JavaScript.
- **Backend:** PHP 8.x.
- **Biblioteca Base:** [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) para o motor de geração de matrizes e renderização nativa.

## 📋 Requisitos

- Servidor web com PHP 7.4 ou superior (Recomendado PHP 8+).
- Extensão `imagick` ou `gd` (usada como fallback para conversão de matrizes em casos de PNG).
- Composer (as dependências já devem estar na pasta `vendor/`).
