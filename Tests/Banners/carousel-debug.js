/**
 * Carousel Debug JavaScript
 * Bu script carousel butonlarının neden çalışmadığını anlamak için tasarlanmıştır
 * Browser console'da çalıştırın: copy-paste yapın
 */

console.log('🔍 CAROUSEL DEBUG BAŞLATILIYOR...');

// 1. Banner grup kontrolü
const bannerGroup = document.querySelector('.banner-group-1');
console.log('📌 Banner Group:', bannerGroup);

if (!bannerGroup) {
    console.error('❌ Banner group bulunamadı!');
} else {
    console.log('✅ Banner group bulundu:', bannerGroup.className);
    
    // 2. Carousel container kontrolü
    const carouselContainer = bannerGroup.querySelector('.carousel-container');
    console.log('📌 Carousel Container:', carouselContainer);
    
    if (!carouselContainer) {
        console.error('❌ Carousel container bulunamadı!');
    } else {
        console.log('✅ Carousel container bulundu');
        
        // 3. Banner items kontrolü
        const items = carouselContainer.querySelectorAll('.banner-item');
        console.log('📌 Banner Items:', items.length, 'adet');
        items.forEach((item, index) => {
            console.log(`   Item ${index}:`, item.textContent.substring(0, 50) + '...');
        });
        
        // 4. Carousel controls kontrolü
        const carouselControls = bannerGroup.querySelector('.carousel-controls');
        console.log('📌 Carousel Controls:', carouselControls);
        
        if (!carouselControls) {
            console.error('❌ Carousel controls bulunamadı!');
        } else {
            console.log('✅ Carousel controls bulundu');
            
            // 5. Buton kontrolü
            const prevButton = carouselControls.querySelector('.prev-carousel');
            const nextButton = carouselControls.querySelector('.next-carousel');
            
            console.log('📌 Prev Button:', prevButton);
            console.log('📌 Next Button:', nextButton);
            
            if (!prevButton || !nextButton) {
                console.error('❌ Butonlardan biri veya ikisi de bulunamadı!');
            } else {
                console.log('✅ Her iki buton da bulundu');
                
                // 6. Buton stil analizi
                const prevStyles = window.getComputedStyle(prevButton);
                const nextStyles = window.getComputedStyle(nextButton);
                
                console.log('🎨 PREV BUTTON STİLLERİ:');
                console.log('   pointer-events:', prevStyles.pointerEvents);
                console.log('   z-index:', prevStyles.zIndex);
                console.log('   position:', prevStyles.position);
                console.log('   display:', prevStyles.display);
                console.log('   visibility:', prevStyles.visibility);
                console.log('   opacity:', prevStyles.opacity);
                console.log('   cursor:', prevStyles.cursor);
                
                console.log('🎨 NEXT BUTTON STİLLERİ:');
                console.log('   pointer-events:', nextStyles.pointerEvents);
                console.log('   z-index:', nextStyles.zIndex);
                console.log('   position:', nextStyles.position);
                console.log('   display:', nextStyles.display);
                console.log('   visibility:', nextStyles.visibility);
                console.log('   opacity:', nextStyles.opacity);
                console.log('   cursor:', nextStyles.cursor);
                
                // 7. Event listener kontrolü
                console.log('🎧 EVENT LISTENER KONTROLÜ:');
                console.log('   Prev onclick:', typeof prevButton.onclick);
                console.log('   Next onclick:', typeof nextButton.onclick);
                
                // 8. Manuel tıklama testi
                console.log('🧪 MANUEL TIKLAMA TESTİ:');
                
                // Prev button test
                try {
                    console.log('   Prev button tıklanıyor...');
                    prevButton.click();
                    console.log('   ✅ Prev button tıklama başarılı');
                } catch (error) {
                    console.error('   ❌ Prev button tıklama hatası:', error);
                }
                
                // Next button test (2 saniye sonra)
                setTimeout(() => {
                    try {
                        console.log('   Next button tıklanıyor...');
                        nextButton.click();
                        console.log('   ✅ Next button tıklama başarılı');
                    } catch (error) {
                        console.error('   ❌ Next button tıklama hatası:', error);
                    }
                }, 2000);
                
                // 9. Scroll davranış testi
                setTimeout(() => {
                    console.log('🔄 SCROLL DAVRANIŞI TESTİ:');
                    const beforeScroll = carouselContainer.scrollLeft;
                    console.log('   Scroll pozisyonu (öncesi):', beforeScroll);
                    
                    carouselContainer.scrollBy({
                        left: 200,
                        behavior: 'smooth'
                    });
                    
                    setTimeout(() => {
                        const afterScroll = carouselContainer.scrollLeft;
                        console.log('   Scroll pozisyonu (sonrası):', afterScroll);
                        console.log('   Scroll değişimi:', afterScroll - beforeScroll);
                    }, 1000);
                }, 4000);
                
                // 10. Container boyut analizi
                console.log('📏 CONTAINER BOYUT ANALİZİ:');
                console.log('   Container width:', carouselContainer.clientWidth);
                console.log('   Container scroll width:', carouselContainer.scrollWidth);
                console.log('   Scroll edilebilir alan:', carouselContainer.scrollWidth - carouselContainer.clientWidth);
                
                // 11. Item boyut analizi
                if (items.length > 0) {
                    const firstItem = items[0];
                    const itemStyles = window.getComputedStyle(firstItem);
                    console.log('📏 İLK ITEM BOYUT ANALİZİ:');
                    console.log('   Item width:', firstItem.offsetWidth);
                    console.log('   Item margin-left:', itemStyles.marginLeft);
                    console.log('   Item margin-right:', itemStyles.marginRight);
                    console.log('   Item padding:', itemStyles.padding);
                }
            }
        }
    }
}

console.log('🔍 DEBUG TAMAMLANDI');

// 12. Global test fonksiyonları oluştur
window.testCarouselNext = function() {
    console.log('🧪 TEST: Next button');
    const btn = document.querySelector('.next-carousel');
    if (btn) {
        btn.click();
        console.log('✅ Next button tıklandı');
    } else {
        console.error('❌ Next button bulunamadı');
    }
};

window.testCarouselPrev = function() {
    console.log('🧪 TEST: Prev button');
    const btn = document.querySelector('.prev-carousel');
    if (btn) {
        btn.click();
        console.log('✅ Prev button tıklandı');
    } else {
        console.error('❌ Prev button bulunamadı');
    }
};

console.log('🎮 TEST FONKSİYONLARI HAZIR:');
console.log('   testCarouselNext() - Next butonunu test et');
console.log('   testCarouselPrev() - Prev butonunu test et');
