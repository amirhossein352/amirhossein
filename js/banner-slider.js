/**
 * Banner Slider with Swiper
 * 
 * @package khane_irani
 */

document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('bannerSlider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.banner-slide');
    if (slides.length === 0) return;

    // Initialize Swiper
    const swiper = new Swiper('#bannerSlider', {
        direction: 'horizontal',
        loop: slides.length > 1,
        autoplay: slides.length > 1 ? {
            delay: 5000,
            disableOnInteraction: false,
        } : false,
        speed: 500,
        effect: 'slide',
        slidesPerView: 1,
        spaceBetween: 0,
        pagination: {
            el: '#bannerSliderDots',
            clickable: true,
        },
        navigation: {
            nextEl: '#bannerSliderNext',
            prevEl: '#bannerSliderPrev',
        },
        // RTL support
        rtl: true,
    });
});
