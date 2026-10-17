<?php
/**
 * Recursos visuais compartilhados (site público, painel e login):
 * tipografia, paleta da marca, Tailwind (CDN) e ícones.
 *
 * Paleta coninfoms
 *   Dark Blue  #182C48  cor primária
 *   Slate Gray #323A42  cor secundária
 *   Light Gray #F1F3F5  cor de apoio
 *   Accent Blue #2B5797 UI e destaques
 */
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- Dev Sans (Light 300 / Regular 400 / Bold 700) com fallback visual para Inter -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
<link rel="stylesheet" href="<?= htmlspecialchars(asset('assets/css/brand.css')) ?>">

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['"Dev Sans"', 'Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif']
                },
                colors: {
                    brand: {
                        dark: '#182C48',
                        slate: '#323A42',
                        light: '#F1F3F5',
                        accent: '#2B5797',
                        blue: '#2B5797'
                    },
                    // Escalas do Tailwind remapeadas para a paleta da marca: as classes
                    // utilitárias já existentes (gray-*, blue-*, indigo-*) assumem a nova identidade.
                    gray: {
                        50: '#F8F9FA', 100: '#F1F3F5', 200: '#E1E5E9', 300: '#CBD1D7', 400: '#9AA3AC',
                        500: '#6B747D', 600: '#4D565F', 700: '#3E464E', 800: '#323A42', 900: '#182C48'
                    },
                    blue: {
                        50: '#EEF3FA', 100: '#DCE6F3', 200: '#B9CCE6', 300: '#8AAAD2', 400: '#5A83BA',
                        500: '#3E6BA8', 600: '#2B5797', 700: '#234779', 800: '#1C3860', 900: '#182C48'
                    },
                    indigo: {
                        50: '#EEF3FA', 100: '#DCE6F3', 200: '#B9CCE6', 300: '#8AAAD2', 400: '#5A83BA',
                        500: '#3E6BA8', 600: '#2B5797', 700: '#234779', 800: '#1C3860', 900: '#182C48'
                    }
                }
            }
        }
    }
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
