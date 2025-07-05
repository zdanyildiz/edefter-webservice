/*// Altın oran sabiti
const goldenRatio = (1 + Math.sqrt(5)) / 2;

// Tarayıcı genişliği ve yüksekliğini al
const windowWidth = window.innerWidth;
const windowHeight = window.innerHeight;

// Altın oranına göre body'nin yeni boyutlarını hesapla
const newWidth = windowHeight * goldenRatio;
const newHeight = windowWidth / goldenRatio;

// Body'nin boyutlarını ayarla
document.body.style.width = `${newWidth}px`;
document.body.style.height = `${newHeight}px`;
document.body.style.overflow = 'hidden';

// Body'i hem dikey hem yatay ortalayalım
document.body.style.display = 'flex';
document.body.style.justifyContent = 'center';
document.body.style.alignItems = 'center';

// Margin'i auto verelim
document.body.style.margin = 'auto';

// Box shadow verelim
document.body.style.boxShadow = '0 0 10px 5px #000';*/