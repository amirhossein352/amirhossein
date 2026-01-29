/**
 * Features Section - Expand/Collapse for long descriptions
 */
(function(){
    if (typeof document === 'undefined') return;
    var d = document;
    function ready(fn){
        if (d.readyState === 'complete' || d.readyState === 'interactive') fn();
        else d.addEventListener('DOMContentLoaded', fn);
    }
    ready(function(){
        var section = d.querySelector('.service-features-section');
        if (!section) return;
        var cards = section.querySelectorAll('.feature-card');
        cards.forEach(function(card){
            var desc = card.querySelector('.feature-description');
            if (!desc) return;
            // if the text is long, clamp and add readmore
            var txt = desc.textContent || '';
            if (txt.length > 160) {
                desc.classList.add('feature-description--clamp');
                var btn = d.createElement('button');
                btn.className = 'feature-readmore';
                btn.type = 'button';
                btn.textContent = 'نمایش بیشتر';
                btn.addEventListener('click', function(){
                    var expanded = card.classList.toggle('is-expanded');
                    btn.textContent = expanded ? 'نمایش کمتر' : 'نمایش بیشتر';
                });
                desc.parentNode.appendChild(btn);
            }
        });
    });
})();


