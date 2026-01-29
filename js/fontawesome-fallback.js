/**
 * Font Awesome Fallback - Check if icons are loading
 */
(function() {
    'use strict';
    
    function checkFontAwesome() {
        // Create a test icon
        var testIcon = document.createElement('i');
        testIcon.className = 'fas fa-check';
        testIcon.style.position = 'absolute';
        testIcon.style.visibility = 'hidden';
        testIcon.style.fontSize = '1px';
        document.body.appendChild(testIcon);
        
        // Check if Font Awesome is loaded
        var computed = window.getComputedStyle(testIcon, ':before');
        var isLoaded = computed.content && computed.content !== 'none' && computed.content !== '';
        
        document.body.removeChild(testIcon);
        
        if (!isLoaded) {
            console.warn('Font Awesome not loaded, trying fallback...');
            // Try jsDelivr
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css';
            link.integrity = 'sha256-HtsXJanqjKTc8vVXjzslq8Jj5U9ybB4f2pxB4PHyYYtk=';
            link.crossOrigin = 'anonymous';
            document.head.appendChild(link);
            
            // If still not loaded after 2 seconds, try use.fontawesome.com
            setTimeout(function() {
                checkFontAwesome();
                if (!window.getComputedStyle(document.createElement('i'), ':before').content) {
                    var link2 = document.createElement('link');
                    link2.rel = 'stylesheet';
                    link2.href = 'https://use.fontawesome.com/releases/v6.4.0/css/all.css';
                    document.head.appendChild(link2);
                }
            }, 2000);
        } else {
            console.log('Font Awesome loaded successfully');
        }
    }
    
    // Check after page load
    if (document.readyState === 'complete') {
        setTimeout(checkFontAwesome, 1000);
    } else {
        window.addEventListener('load', function() {
            setTimeout(checkFontAwesome, 1000);
        });
    }
})();

