document.addEventListener('DOMContentLoaded', function() {
    // Animation du carousel
    const carousel = document.querySelector('.carousel');
    const images = document.querySelectorAll('.carousel img');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    const indicators = document.querySelectorAll('.indicator');
    
    let currentIndex = 0;
    const totalImages = images.length;
    let intervalId;
    
    function moveToIndex(index) {
        if (index < 0) index = totalImages - 1;
        if (index >= totalImages) index = 0;
        
        currentIndex = index;
        carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === currentIndex);
        });
    }
    
    function startAutoSlide() {
        intervalId = setInterval(() => {
            moveToIndex(currentIndex + 1);
        }, 5000);
    }
    
    // Initialisation
    moveToIndex(0);
    startAutoSlide();
    
    // Événements
    prevBtn.addEventListener('click', () => {
        clearInterval(intervalId);
        moveToIndex(currentIndex - 1);
        startAutoSlide();
    });
    
    nextBtn.addEventListener('click', () => {
        clearInterval(intervalId);
        moveToIndex(currentIndex + 1);
        startAutoSlide();
    });
    
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            clearInterval(intervalId);
            moveToIndex(index);
            startAutoSlide();
        });
    });
    
    carousel.addEventListener('mouseenter', () => clearInterval(intervalId));
    carousel.addEventListener('mouseleave', startAutoSlide);

    // Autres animations...
});