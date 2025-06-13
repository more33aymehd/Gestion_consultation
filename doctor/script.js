document.addEventListener('DOMContentLoaded', function() {
    const sliderItems = document.querySelectorAll('.slider-item');
    let currentIndex = 0;

    function showSlide(index) {
        sliderItems.forEach((item, i) => {
            item.classList.toggle('active', i === index);
        });
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % sliderItems.length;
        showSlide(currentIndex);
    }

    showSlide(currentIndex);
    setInterval(nextSlide, 5000); // Change slide toutes les 5 secondes
});