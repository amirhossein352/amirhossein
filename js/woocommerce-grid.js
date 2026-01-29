(function(){
    if (typeof document === 'undefined') return;
    function ready(fn){ if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }
    ready(function(){
        var grid = document.querySelector('.woocommerce ul.products');
        if (!grid) return;
        // Ensure grid layout applied even if other styles compete
        grid.style.display = 'grid';
        grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
        grid.style.gap = '25px';
        grid.style.listStyle = 'none';
        grid.style.padding = '0';
        grid.style.margin = '0';
    });
})();

