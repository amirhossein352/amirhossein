/**
 * Reviews Section - Dedicated Interactions
 */
(function(){
    if (typeof document === 'undefined') return;
    var d = document;

    function ready(fn){
        if (d.readyState === 'complete' || d.readyState === 'interactive') fn();
        else d.addEventListener('DOMContentLoaded', fn);
    }

    ready(function(){
        var root = d.querySelector('.zs-reviews-section, .woocommerce-Reviews');
        if (!root) return;

        var writeBtn = d.querySelector('.zs-reviews-write-btn');
        if (writeBtn) {
            writeBtn.addEventListener('click', function(){
                var targetSel = this.getAttribute('data-scroll-to') || '#review_form';
                var target = d.querySelector(targetSel);
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }

        var sortSelect = d.getElementById('zs-reviews-sort');
        if (sortSelect) {
            sortSelect.addEventListener('change', function(){
                var value = this.value;
                var list = d.querySelector('.commentlist');
                if (!list) return;
                var items = Array.prototype.slice.call(list.children);
                var by = function(el){
                    var ratingNode = el.querySelector('.star-rating span');
                    var width = ratingNode ? parseFloat(ratingNode.style.width) || 0 : 0;
                    var rating = Math.round((width/100)*5*10)/10;
                    var dateEl = el.querySelector('time');
                    var date = dateEl ? new Date(dateEl.getAttribute('datetime') || dateEl.textContent) : new Date(0);
                    return { rating: rating, time: date.getTime() };
                };
                items.sort(function(a,b){
                    var A = by(a), B = by(b);
                    if (value === 'highest') return B.rating - A.rating;
                    if (value === 'lowest') return A.rating - B.rating;
                    return B.time - A.time; // newest
                });
                items.forEach(function(it){ list.appendChild(it); });
            });
        }

        var reviews = d.querySelectorAll('.commentlist .review .description');
        reviews.forEach(function(desc){
            var text = desc.textContent || '';
            if (text.length > 280) {
                var full = text;
                var short = full.slice(0, 280) + '…';
                desc.setAttribute('data-full', full);
                desc.textContent = short;
                var toggle = d.createElement('button');
                toggle.className = 'zs-review-readmore';
                toggle.type = 'button';
                toggle.textContent = 'نمایش بیشتر';
                toggle.addEventListener('click', function(){
                    var expanded = toggle.getAttribute('data-expanded') === '1';
                    if (expanded) {
                        desc.textContent = short;
                        toggle.textContent = 'نمایش بیشتر';
                        toggle.setAttribute('data-expanded','0');
                    } else {
                        desc.textContent = full;
                        toggle.textContent = 'نمایش کمتر';
                        toggle.setAttribute('data-expanded','1');
                    }
                });
                desc.parentNode.appendChild(toggle);
            }
        });

        // Filters: verified-only and with-media
        var filterVerified = d.getElementById('zs-filter-verified');
        var filterMedia = d.getElementById('zs-filter-media');
        function applyFilters(){
            var list = d.querySelector('.commentlist');
            if (!list) return;
            var items = list.querySelectorAll('li');
            items.forEach(function(li){
                var isVerified = !!li.querySelector('.woocommerce-review__verified');
                var hasMedia = !!li.querySelector('img, video');
                var show = true;
                if (filterVerified && filterVerified.checked && !isVerified) show = false;
                if (filterMedia && filterMedia.checked && !hasMedia) show = false;
                li.style.display = show ? '' : 'none';
            });
        }
        if (filterVerified) filterVerified.addEventListener('change', applyFilters);
        if (filterMedia) filterMedia.addEventListener('change', applyFilters);
        applyFilters();

        // Helpful voting (client-side)
        var list = d.querySelector('.commentlist');
        if (list) {
            var items = list.querySelectorAll('li');
            items.forEach(function(li){
                var idMatch = (li.id || '').match(/comment-(\d+)/);
                var cid = idMatch ? idMatch[1] : null;
                if (!cid) return;
                var actions = li.querySelector('.zs-review-actions');
                if (!actions) {
                    actions = d.createElement('div');
                    actions.className = 'zs-review-actions';
                    var target = li.querySelector('.comment-text') || li;
                    target.appendChild(actions);
                }
                var helpfulBtn = d.createElement('button');
                helpfulBtn.type = 'button';
                helpfulBtn.className = 'zs-helpful';
                helpfulBtn.innerHTML = '<i class="fas fa-thumbs-up"></i><span>مفید بود</span><span class="zs-helpful-count">0</span>';
                actions.appendChild(helpfulBtn);

                var key = 'zs_helpful_' + cid;
                var votedKey = key + '_voted';
                var count = parseInt(localStorage.getItem(key) || '0', 10);
                var voted = localStorage.getItem(votedKey) === '1';
                helpfulBtn.querySelector('.zs-helpful-count').textContent = String(count);
                if (voted) helpfulBtn.setAttribute('data-active','1');

                helpfulBtn.addEventListener('click', function(){
                    if (localStorage.getItem(votedKey) === '1') return; // prevent double vote
                    count += 1;
                    localStorage.setItem(key, String(count));
                    localStorage.setItem(votedKey, '1');
                    helpfulBtn.querySelector('.zs-helpful-count').textContent = String(count);
                    helpfulBtn.setAttribute('data-active','1');
                });
            });
        }

        // Interactive rating stars replacing the select#rating
        var ratingSelect = d.getElementById('rating');
        var ratingWrap = d.querySelector('.comment-form-rating');
        if (ratingSelect && ratingWrap) {
            // Build stars
            var stars = d.createElement('div');
            stars.className = 'zs-rating-stars';
            for (var i = 1; i <= 5; i++) {
                var s = d.createElement('span');
                s.className = 'star';
                s.innerHTML = '★';
                (function(score){
                    s.addEventListener('mouseenter', function(){
                        highlight(score);
                    });
                    s.addEventListener('mouseleave', function(){
                        highlight(parseInt(ratingSelect.value || '0', 10));
                    });
                    s.addEventListener('click', function(){
                        ratingSelect.value = String(score);
                        highlight(score);
                    });
                })(i);
                stars.appendChild(s);
            }
            ratingWrap.appendChild(stars);

            function highlight(score){
                var nodes = stars.querySelectorAll('.star');
                nodes.forEach(function(n, idx){
                    if (idx < score) n.classList.add('is-filled');
                    else n.classList.remove('is-filled');
                });
            }
            // init from current value
            highlight(parseInt(ratingSelect.value || '0', 10));
        }
    });
})();


