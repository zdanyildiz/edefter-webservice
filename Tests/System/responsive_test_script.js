/**
 * Gerçek Site CSS Media Query Test Scripti
 * Bu scripti tarayıcı console'unda çalıştırın
 */

(function () {
    console.log('🔍 CSS Media Query Diagnostic Test Başlıyor...\n');

    // 1. Viewport Bilgileri
    console.log('📱 VIEWPORT BİLGİLERİ:');
    console.log('- Genişlik:', window.innerWidth + 'px');
    console.log('- Yükseklik:', window.innerHeight + 'px');
    console.log('- Device Pixel Ratio:', window.devicePixelRatio);
    console.log('- User Agent:', navigator.userAgent.split(' ').slice(-2).join(' '));

    // 2. CSS Variables Kontrolü
    console.log('\n🎨 CSS VARIABLES KONTROLÜ:');
    const rootStyles = getComputedStyle(document.documentElement);

    const breakpoints = [
        '--mobile-breakpoint',
        '--tablet-breakpoint',
        '--desktop-breakpoint'
    ];

    breakpoints.forEach(bp => {
        const value = rootStyles.getPropertyValue(bp).trim();
        console.log(`- ${bp}: ${value || 'TANIMLI DEĞİL'}`);
    });

    // 3. Media Query Test
    console.log('\n📺 MEDIA QUERY TESTLERİ:');

    const tabletBP = rootStyles.getPropertyValue('--tablet-breakpoint').trim();

    if (tabletBP) {
        const staticQuery = `(max-width: ${tabletBP})`;
        const variableQuery = '(max-width: var(--tablet-breakpoint))';

        console.log(`- Statik Query "${staticQuery}":`,
            window.matchMedia(staticQuery).matches ? '✅ ÇALIŞIYOR' : '❌ ÇALIŞMIYOR');

        // CSS Variables media query tarayıcı desteği test et
        try {
            const variableMatch = window.matchMedia(variableQuery).matches;
            console.log(`- Variable Query "${variableQuery}":`,
                variableMatch ? '✅ ÇALIŞIYOR' : '❌ ÇALIŞMIYOR');
        } catch (e) {
            console.log(`- Variable Query: ❌ DESTEKLENMIYOR (${e.message})`);
        }
    } else {
        console.log('- ❌ --tablet-breakpoint tanımlı değil!');
    }

    // 4. CSS Dosyaları Kontrolü
    console.log('\n📄 CSS DOSYALARI KONTROLÜ:');
    const stylesheets = Array.from(document.styleSheets);
    let cssVariableFound = false;

    stylesheets.forEach((sheet, index) => {
        try {
            const href = sheet.href ? sheet.href.split('/').pop() : `inline-${index}`;
            console.log(`- ${href}: ${sheet.cssRules ? sheet.cssRules.length : 'N/A'} rules`);

            if (sheet.cssRules) {
                Array.from(sheet.cssRules).forEach(rule => {
                    if (rule.type === CSSRule.STYLE_RULE && rule.selectorText === ':root') {
                        cssVariableFound = true;
                    }
                });
            }
        } catch (e) {
            console.log(`- Stylesheet ${index}: CORS hatası veya erişim sorunu`);
        }
    });

    console.log('\n🎯 SONUÇ:');
    console.log('- CSS Variables:', cssVariableFound ? '✅ Bulundu' : '❌ Bulunamadı');
    console.log('- Tarayıcı Desteği:', CSS.supports('color', 'var(--test)') ? '✅ Destekli' : '❌ Desteksiz');

    // 5. Öneriler
    console.log('\n💡 ÖNERİLER:');
    if (!cssVariableFound) {
        console.log('- CSS Variables tanımlı değil, index.css yüklenmemiş olabilir');
    }

    if (window.innerWidth <= 992) {
        console.log('- Viewport 992px altında, media query tetiklenmeli');
    } else {
        console.log('- Viewport 992px üstünde, media query tetiklenmemeli');
    }

    console.log('\n🔧 TEST KOMUTLARI:');
    console.log('window.testResponsive() - Bu test scripti');
    console.log('getComputedStyle(document.documentElement).getPropertyValue("--tablet-breakpoint")');
    console.log('window.matchMedia("(max-width: 992px)").matches');

    // Global test fonksiyonu tanımla
    window.testResponsive = arguments.callee;

})();
